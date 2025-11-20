<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Models\Projects;
use App\Models\User;
use App\Models\Milestones;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Providers\NotificationService;

class TaskController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->with(['project', 'user', 'assignee', 'milestone']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by milestone if provided
        if ($request->has('milestone_id') && $request->milestone_id) {
            $query->where('milestone_id', $request->milestone_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::where('manager_id', auth()->id())->get();

        // Get counts for different statuses - optimized for Kanban
        $taskCounts = [
            'all' => $tasks->count(),
            'todo' => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'done' => $tasks->where('status', 'done')->count(),
            'pending' => $tasks->whereIn('status', ['todo', 'in_progress'])->count(),
        ];

        // Get milestones for the current filtered project if any
        $milestones = collect();
        if ($request->has('project_id') && $request->project_id) {
            $milestones = Milestones::where('project_id', $request->project_id)->get();
        }

        return view('manager.tasks.index', compact('tasks', 'projects', 'taskCounts', 'milestones'));
    }

    public function pendingTasks(Request $request)
    {
        $query = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->whereIn('status', ['todo', 'in_progress'])
          ->with(['project', 'user', 'assignee', 'milestone']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::where('manager_id', auth()->id())->get();

        return view('manager.tasks.pending', compact('tasks', 'projects'));
    }

    public function completedTasks(Request $request)
    {
        $query = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->where('status', 'done')
          ->with(['project', 'user', 'assignee', 'milestone']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::where('manager_id', auth()->id())->get();

        return view('manager.tasks.completed', compact('tasks', 'projects'));
    }

    public function create(Request $request)
    {
        // For Super Admin - get all projects
// For Admin/User - get only their managed projects

if (auth()->user()->role == 'super_admin') {
    $projects = Projects::all();
} else {
    $projects = Projects::where('manager_id', auth()->id())->get();
}
        $users = User::where('role' ,'!=', 'client')->get();

        $selectedProject = $request->get('project_id');

        // Get milestones for the selected project if any
        $milestones = collect();
        if ($selectedProject) {
            $milestones = Milestones::where('project_id', $selectedProject)->get();
        }

        return view('manager.tasks.create', compact('projects', 'users', 'selectedProject', 'milestones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => ['required', Rule::exists('projects', 'id')->where('manager_id', auth()->id())],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'milestone_id' => ['nullable', Rule::exists('milestones', 'id')->where('project_id', $request->project_id)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done'])],
            'due_date' => ['nullable', 'date'],
        ]);

        $task = Tasks::create([
            'project_id' => $request->project_id,
            'assigned_to' => $request->assigned_to,
            'milestone_id' => $request->milestone_id,
            'created_by' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority ?? 'medium',
            'status' => $request->status ?? 'todo',
            'due_date' => $request->due_date,
        ]);

        if ($request->assigned_to) {
            $project = $task->project;
            if ($project) {
                $project->teamMembers()->syncWithoutDetaching([$request->assigned_to]);
            }

            // Send notification to assigned user using NotificationService
            $assignedUser = User::find($request->assigned_to);
            if ($assignedUser) {
                $this->notificationService->notifyTaskAssigned($task, $assignedUser);
            }
        }

        return redirect()->route('manager.tasks.index')
                        ->with('success', 'Task created successfully!');
    }

    public function show($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->with(['project', 'user', 'assignee', 'subtasks', 'milestone'])
          ->findOrFail($id);

        return view('manager.tasks.show', compact('task'));
    }

    public function edit($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $projects = Projects::where('manager_id', auth()->id())->get();
        $users = User::where('role', 'user')->get();
        $milestones = Milestones::where('project_id', $task->project_id)->get();

        return view('manager.tasks.edit', compact('task', 'projects', 'users', 'milestones'));
    }

    public function update(Request $request, $id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $oldStatus = $task->status;
        $oldAssignee = $task->assigned_to;

        $request->validate([
            'project_id' => ['sometimes', Rule::exists('projects', 'id')->where('manager_id', auth()->id())],
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
            'milestone_id' => ['sometimes', 'nullable', Rule::exists('milestones', 'id')->where('project_id', $request->project_id ?? $task->project_id)],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'priority' => ['sometimes', 'nullable', Rule::in(['low', 'medium', 'high'])],
            'status' => ['sometimes', 'nullable', Rule::in(['todo', 'in_progress', 'done'])],
            'due_date' => ['sometimes', 'nullable', 'date'],
        ]);

        // Update task with explicit field assignment
        $task->title = $request->title;
        $task->project_id = $request->project_id;
        $task->assigned_to = $request->assigned_to;
        $task->milestone_id = $request->milestone_id;
        $task->description = $request->description;
        $task->priority = $request->priority;
        $task->status = $request->status;

        // Handle due_date - ensure proper format
        if ($request->filled('due_date')) {
            $task->due_date = \Carbon\Carbon::parse($request->due_date);
        } else {
            $task->due_date = null;
        }

        $task->save();

        // Send notifications for important changes using NotificationService
        $this->sendUpdateNotifications($task, $oldStatus, $oldAssignee);

        return redirect()->route('manager.tasks.show', $task->id)
                        ->with('success', 'Task updated successfully!');
    }

    public function destroy($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $task->delete();

        return redirect()->route('manager.tasks.index')
                        ->with('success', 'Task deleted successfully!');
    }

    public function markAsComplete($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $oldStatus = $task->status;
        $task->update(['status' => 'done']);

        // Send status change notification using NotificationService
        if ($oldStatus !== 'done') {
            $this->notificationService->notifyTaskCompleted($task, auth()->user());
        }

        return redirect()->back()
                        ->with('success', 'Task marked as completed!');
    }

    public function markAsInProgress($id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $oldStatus = $task->status;
        $task->update(['status' => 'in_progress']);

        // Send task updated notification
        if ($oldStatus !== 'in_progress') {
            $this->notificationService->notifyTaskUpdated($task, auth()->user());
        }

        return redirect()->back()
                        ->with('success', 'Task marked as in progress!');
    }

    // New method to get milestones for a project
    public function getMilestones($projectId)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($projectId);

        $milestones = Milestones::where('project_id', $projectId)
            ->select('id', 'title', 'due_date', 'status')
            ->get()
            ->map(function($milestone) {
                return [
                    'id' => $milestone->id,
                    'title' => $milestone->title,
                    'due_date' => $milestone->due_date,
                    'status' => $milestone->status,
                    'display_text' => $milestone->title .
                                   ($milestone->due_date ? ' (Due: ' . \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') . ')' : '') .
                                   ($milestone->status === 'completed' ? ' ✅' : '')
                ];
            });

        return response()->json($milestones);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($id);

        $oldStatus = $task->status;

        $request->validate([
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])]
        ]);

        $task->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        // Send status change notification using NotificationService
        if ($oldStatus !== $request->status) {
            if ($request->status === 'done') {
                $this->notificationService->notifyTaskCompleted($task, auth()->user());
            } else {
                $this->notificationService->notifyTaskUpdated($task, auth()->user());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully!',
            'task' => $task->load('project', 'assignee', 'milestone')
        ]);
    }

    /**
     * Send notifications for task updates using NotificationService
     */
    private function sendUpdateNotifications($task, $oldStatus, $oldAssignee)
    {
        $currentUser = auth()->user();

        // Notify about status change
        if ($oldStatus !== $task->status) {
            if ($task->status === 'done') {
                $this->notificationService->notifyTaskCompleted($task, $currentUser);
            } else {
                $this->notificationService->notifyTaskUpdated($task, $currentUser);
            }
        }

        // Notify about assignment change
        if ($oldAssignee != $task->assigned_to) {
            if ($task->assigned_to) {
                $newAssignee = User::find($task->assigned_to);
                if ($newAssignee) {
                    $this->notificationService->notifyTaskAssigned($task, $newAssignee);
                }
            }
            // If task was unassigned, notify the old assignee
            elseif ($oldAssignee) {
                $oldAssignedUser = User::find($oldAssignee);
                if ($oldAssignedUser) {
                    // You can create a specific notification for unassignment if needed
                    $this->notificationService->notifyTaskUpdated($task, $currentUser);
                }
            }
        }

        // Check for deadline approaching or overdue
        $this->checkTaskDeadlines($task);
    }

    /**
     * Check task deadlines and send notifications if needed
     */
    private function checkTaskDeadlines($task)
    {
        if (!$task->due_date) {
            return;
        }

        $now = now();
        $dueDate = \Carbon\Carbon::parse($task->due_date);
        $daysUntilDue = $now->diffInDays($dueDate, false);

        // If task is overdue and not completed
        if ($daysUntilDue < 0 && $task->status !== 'done') {
            $this->notificationService->notifyTaskOverdue($task);
        }
        // If task is due within 3 days
        elseif ($daysUntilDue <= 3 && $daysUntilDue >= 0 && $task->status !== 'done') {
            $this->notificationService->notifyTaskDeadlineApproaching($task);
        }
    }

    /**
     * Check for due tasks and send notifications (call this via scheduler)
     */
    public function checkDueTasks()
    {
        $dueTasks = Tasks::where('due_date', '<=', now()->addDays(1))
                        ->where('due_date', '>', now())
                        ->whereIn('status', ['todo', 'in_progress'])
                        ->with('assignee', 'project')
                        ->get();

        foreach ($dueTasks as $task) {
            $this->notificationService->notifyTaskDeadlineApproaching($task);
        }

        // Check for overdue tasks
        $overdueTasks = Tasks::where('due_date', '<', now())
                            ->whereIn('status', ['todo', 'in_progress'])
                            ->with('assignee', 'project')
                            ->get();

        foreach ($overdueTasks as $task) {
            $this->notificationService->notifyTaskOverdue($task);
        }

        return response()->json([
            'due_tasks_count' => $dueTasks->count(),
            'overdue_tasks_count' => $overdueTasks->count()
        ]);
    }
}
