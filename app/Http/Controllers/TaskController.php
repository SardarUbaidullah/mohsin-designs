<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use App\Models\Projects;
use App\Models\User;
use App\Models\Milestones;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    // GET /tasks
public function index(Request $request)
{
    $query = Tasks::with(['project', 'assignee', 'creator', 'milestone']);

    // Filter by project if provided
    if ($request->has('project_id') && $request->project_id) {
        $query->where('project_id', $request->project_id);
    }

    // Filter by milestone if provided
    if ($request->has('milestone_id') && $request->milestone_id) {
        $query->where('milestone_id', $request->milestone_id);
    }

    // NEW: Filter by assigned user if provided
    if ($request->has('assigned_user') && $request->assigned_user) {
        $query->where('assigned_to', $request->assigned_user);
    }

    // NEW: Filter by priority if provided
    if ($request->has('priority') && $request->priority) {
        $query->where('priority', $request->priority);
    }

    $tasks = $query->latest()->get();
    $projects = Projects::all();

    // NEW: Get unique users who are assigned to tasks
    $assignedUsers = User::whereIn('id', function($query) {
        $query->select('assigned_to')
              ->from('tasks')
              ->whereNotNull('assigned_to');
    })->get();

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

    return view('admin.Tasks.index', compact('tasks', 'projects', 'taskCounts', 'milestones', 'assignedUsers'));
}  public function pendingTasks(Request $request)
    {
        $query = Tasks::whereIn('status', ['todo', 'in_progress'])
            ->with(['project', 'assignee', 'creator', 'milestone']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::all();

        return view('admin.Tasks.pending', compact('tasks', 'projects'));
    }

    public function completedTasks(Request $request)
    {
        $query = Tasks::where('status', 'done')
            ->with(['project', 'assignee', 'creator', 'milestone']);

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->get();
        $projects = Projects::all();

        return view('admin.Tasks.completed', compact('tasks', 'projects'));
    }

    // GET /tasks/create
    public function create(Request $request)
    {
        $projects = Projects::all();
        $users = User::where('role', '!=', 'client')->get();
        $selectedProject = $request->get('project_id');

        // Get milestones for the selected project if any
        $milestones = collect();
        if ($selectedProject) {
            $milestones = Milestones::where('project_id', $selectedProject)->get();
        }

        return view('admin.Tasks.create', compact('projects', 'users', 'selectedProject', 'milestones'));
    }

    // POST /tasks
    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'  => ['required', 'exists:projects,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'milestone_id' => ['nullable', Rule::exists('milestones', 'id')->where('project_id', $request->project_id)],
            'created_by'  => ['nullable', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'status'      => ['nullable', Rule::in(['todo', 'in_progress', 'done'])],
            'start_date'  => ['nullable', 'date'],
            'due_date'    => ['nullable', 'date'],
        ]);

        // Set created_by to current user if not provided
        if (!isset($data['created_by'])) {
            $data['created_by'] = auth()->id();
        }

        $task = Tasks::create($data);

        // Add user to project chat if assigned
        if ($request->assigned_to) {
            $project = $task->project;
            if ($project) {
                $project->teamMembers()->syncWithoutDetaching([$request->assigned_to]);
            }
        }

        $task->addUserToProjectChat();

        return redirect()->route('tasks.index')->with('success', 'Task created successfully');
    }

    // GET /tasks/{id}
    public function show($id)
    {
        $task = Tasks::with(['project', 'assignee', 'creator', 'subtasks', 'milestone'])->find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        return view('admin.Tasks.show', compact('task'));
    }

    // GET /tasks/{id}/edit
    public function edit($id)
    {
        $task = Tasks::find($id);
        $projects = Projects::all();
        $users = User::where('role', '!=', 'client')->get();

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        $milestones = Milestones::where('project_id', $task->project_id)->get();

        return view('admin.Tasks.edit', compact('task', 'projects', 'users', 'milestones'));
    }

    // PUT /tasks/{id}
    public function update(Request $request, $id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        $data = $request->validate([
            'project_id'  => ['sometimes', 'required', 'exists:projects,id'],
            'assigned_to' => ['sometimes', 'nullable', 'exists:users,id'],
            'milestone_id' => ['sometimes', 'nullable', Rule::exists('milestones', 'id')->where('project_id', $request->project_id ?? $task->project_id)],
            'created_by'  => ['sometimes', 'nullable', 'exists:users,id'],
            'title'       => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'priority'    => ['sometimes', 'nullable', Rule::in(['low', 'medium', 'high'])],
            'status'      => ['sometimes', 'nullable', Rule::in(['todo', 'in_progress', 'done'])],
            'start_date'  => ['sometimes', 'nullable', 'date'],
            'due_date'    => ['sometimes', 'nullable', 'date'],
        ]);

        $task->update($data);

        // Add user to project chat if assigned
        if ($request->assigned_to) {
            $project = $task->project;
            if ($project) {
                $project->teamMembers()->syncWithoutDetaching([$request->assigned_to]);
            }
        }

        $task->addUserToProjectChat();

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully');
    }

    // DELETE /tasks/{id}
    public function destroy($id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return redirect()->route('tasks.index')->with('error', 'Task not found');
        }

        $task->delete(); // soft delete
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully');
    }

    public function markAsComplete($id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $task->update(['status' => 'done']);

        return redirect()->back()->with('success', 'Task marked as completed!');
    }

    public function markAsInProgress($id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return redirect()->back()->with('error', 'Task not found');
        }

        $task->update(['status' => 'in_progress']);

        return redirect()->back()->with('success', 'Task marked as in progress!');
    }

    // New method to get milestones for a project
    public function getMilestones($projectId)
    {
        $project = Projects::findOrFail($projectId);

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
                                   ($milestone->status === 'completed' ? ' âœ…' : '')
                ];
            });

        return response()->json($milestones);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = Tasks::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found'
            ], 404);
        }

        $request->validate([
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])]
        ]);

        $task->update([
            'status' => $request->status,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully!',
            'task' => $task->load('project', 'assignee', 'milestone')
        ]);
    }
}
