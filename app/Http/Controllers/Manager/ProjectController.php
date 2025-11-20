<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Projects;
use App\Models\User;
use App\Models\Client;
use App\Models\Category;
use App\Models\Tasks;
use Illuminate\Http\Request;
use App\Providers\NotificationService;

class ProjectController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

   // GET /manager/projects
// GET /manager/projects
public function index(Request $request)
{
    $user = auth()->user();

    // 🟣 FIXED: Base query WITHOUT any filter
    $query = Projects::with(['tasks', 'teamMembers', 'category'])->latest();

    // 🟦 DEFAULT — Assigned To Me
    if (!$request->has('filter')) {
        $query->where('manager_id', $user->id);
    }

    // 🟩 FILTER — Created By Me
    if ($request->filter == 'created_by_me') {
        $query->where('created_by', $user->id);
    }

    // Get final projects
    $projects = $query->get();

    // 👇 Your OLD logic kept SAME 100%
    $totalTasks = Tasks::whereHas('project', function($query) use ($user) {
        $query->where('manager_id', $user->id);
    })->count();

    $teamMembersCount = User::whereHas('assignedTasks', function($query) use ($user) {
        $query->whereHas('project', function($q) use ($user) {
            $q->where('manager_id', $user->id);
        });
    })->distinct()->count();

    $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

    return view('manager.projects.index', compact('projects', 'totalTasks', 'teamMembersCount', 'categories'));
}


    // GET /projects/create - Only if manager has permission
    public function create()
    {
        $user = auth()->user();

        if (!$user->can_create_project) {
            return redirect()->route('manager.projects.index')
                           ->with('error', 'You do not have permission to create projects.');
        }

        $managers = User::where('role','!=' ,'client')->get();
        $clients = Client::active()->get();
        $teamMembers = User::where('role', 'user')->get();
        $categories = Category::where('is_active', true)->get();

        return view('manager.projects.create', compact('managers', 'clients', 'teamMembers', 'categories'));
    }

    // POST /projects - Store new project (MANAGER SPECIFIC)
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'client_id' => 'nullable|exists:clients,id',
        'manager_id' => 'required|exists:users,id',
        'category_id' => 'required|exists:categories,id',
        'start_date' => 'required|date|after_or_equal:today',
        'due_date' => 'nullable|date|after:start_date',
        'status' => 'required|in:pending,in_progress,completed',
    ]);

    

    // Auto-set manager_id for admin users
    // $managerId = $request->manager_id;
    // if (Auth::user()->role == 'admin') {
    //     $managerId = Auth::id();
    // }
 $managerId = $request->manager_id;

    $project = Projects::create([
        'name' => $request->name,
        'description' => $request->description,
        'start_date' => $request->start_date,
        'due_date' => $request->due_date,
        'manager_id' => $managerId,
        'client_id' => $request->client_id,
        'category_id' => $request->category_id,
        'status' => $request->status,
        'created_by' => auth()->id(),
    ]);

    // Create project chat room
    $project->createProjectChatRoom();

    // Add team members if selected
    if ($request->has('team_members')) {
        foreach ($request->team_members as $memberId) {
            $project->teamMembers()->attach($memberId);

            // Notify team member they were added to project (EXCEPT if they are the manager)
            $teamMember = User::find($memberId);
            if ($teamMember && $teamMember->id != $managerId) {
                $this->notificationService->notifyTeamMemberAdded($project, $teamMember, auth()->user());
            }
        }
    }

    // Notify ONLY the assigned manager (not all admins)
    $manager = User::find($managerId);
    if ($manager) {
        $this->notificationService->sendToUser($manager->id, 'project_assigned', [
            'title' => 'Project Assigned',
            'message' => "You have been assigned as manager for project: {$project->name}",
            'action_url' => route('projects.show', $project),
            'icon' => 'fas fa-project-diagram',
            'color' => 'purple',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'assigned_by' => auth()->user()->name,
        ]);
    }

    // Notify super_admin ONLY (not all admins) that a project was created
    if (auth()->user()->role !== 'super_admin') {
        $superAdmin = User::where('role', 'super_admin')->first();
        if ($superAdmin) {
            $this->notificationService->sendToUser($superAdmin->id, 'project_created', [
                'title' => 'New Project Created',
                'message' => "New project '{$project->name}' has been created by " . auth()->user()->name,
                'action_url' => route('projects.show', $project),
                'icon' => 'fas fa-project-diagram',
                'color' => 'blue',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'created_by' => auth()->user()->name,
            ]);
        }
    }


        return redirect()->route('manager.projects.index')->with('success', 'Project created successfully!');
    }

    // Running Projects
    public function running()
    {
        $projects = Projects::where('manager_id', auth()->id())
                       ->whereIn('status', ['pending', 'in_progress'])
                       ->withCount(['tasks'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }, 'category'])
                       ->latest()
                       ->get();

        // Calculate running projects statistics
        $runningStats = [
            'total' => $projects->count(),
            'in_progress' => $projects->where('status', 'in_progress')->count(),
            'pending' => $projects->where('status', 'pending')->count(),
            'total_tasks' => $projects->sum('tasks_count'),
            'completed_tasks' => $projects->sum(function($project) {
                return $project->tasks->where('status', 'done')->count();
            }),
            'overdue' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->isPast();
            })->count()
        ];

        return view('manager.projects.running', compact('projects', 'runningStats'));
    }

    // Completed Projects
    public function completed()
    {
        $projects = Projects::where('manager_id', auth()->id())
                       ->where('status', 'completed')
                       ->withCount(['tasks'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }, 'category'])
                       ->latest()
                       ->get();

        // Calculate completed projects statistics
        $completionStats = [
            'total' => $projects->count(),
            'completed_this_month' => $projects->where('updated_at', '>=', now()->subMonth())->count(),
            'completed_this_quarter' => $projects->where('updated_at', '>=', now()->subMonths(3))->count(),
            'total_tasks_completed' => $projects->sum('tasks_count'),
            'on_time' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->gte($project->updated_at);
            })->count()
        ];

        return view('manager.projects.completed', compact('projects', 'completionStats'));
    }

    // GET /projects/{id}
    public function show($id)
    {
        $project = Projects::where('manager_id', auth()->id())
                          ->with(['tasks', 'tasks.assignee', 'tasks.user', 'manager', 'category'])
                          ->withCount(['tasks'])
                          ->findOrFail($id);

        return view('manager.projects.show', compact('project'));
    }

    // GET /projects/{id}/edit
    public function edit($id)
    {
        $project = Projects::where('manager_id', auth()->id())
                          ->with(['teamMembers', 'category'])
                          ->findOrFail($id);
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();

        $clients = Client::active()->get();
        $teamMembers = User::where('role', 'user')->get();
        $categories = Category::where('is_active', true)->get();

        return view('manager.projects.edit', compact('project', 'clients', 'teamMembers', 'managers', 'categories'));
    }

    // PUT /projects/{id}
    public function update(Request $request, $id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'category_id' => 'required|exists:categories,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id'
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'client_id' => $request->client_id,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        // Sync team members
        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members);
        } else {
            $project->teamMembers()->detach();
        }

        return redirect()->route('manager.projects.show', $project->id)
                        ->with('success', 'Project updated successfully!');
    }

    // DELETE /projects/{id}
    public function destroy($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        $project->delete();

        return redirect()->route('manager.projects.index')
                        ->with('success', 'Project deleted successfully!');
    }

    // Mark project as completed
    public function markComplete($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        $project->update(['status' => 'completed']);

        return redirect()->back()
                        ->with('success', 'Project marked as completed!');
    }

    // Mark project as in progress
    public function markInProgress($id)
    {
        $project = Projects::where('manager_id', auth()->id())->findOrFail($id);
        $project->update(['status' => 'in_progress']);

        return redirect()->back()
            ->with('success', 'Project marked as in progress!');
    }

    public function updateStatus(Request $request, Projects $project)
    {
        try {
            // Ensure the project belongs to the current manager
            if ($project->manager_id != auth()->id()) {
                abort(403);
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed'
            ]);

            $project->update([
                'status' => $validated['status']
            ]);

            return redirect()->back()->with('success', 'Project status updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update project status');
        }
    }
}
