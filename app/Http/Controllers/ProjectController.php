<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Models\Projects;
use App\Models\User;
use App\Models\Client;
use App\Models\Tasks;
use App\Models\Milestones;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Providers\NotificationService;

class ProjectController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // GET /projects
 public function index(Request $request)
{
    $query = Projects::with(['tasks', 'teamMembers', 'milestones', 'category'])->latest();

    // Apply category filter
    if ($request->has('category_id') && $request->category_id != 'all') {
        $query->where('category_id', $request->category_id);
    }

    // Apply status filter
    if ($request->has('status') && $request->status != 'all') {
        $query->where('status', $request->status);
    }

    $projects = $query->get();

    // Calculate statistics
    $totalTasks = Tasks::count();
    $teamMembersCount = User::whereHas('assignedTasks')->distinct()->count();
    $totalMilestones = Milestones::count();

    // Get categories for filter dropdown
    $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

    return view('projects.index', compact('projects', 'totalTasks', 'teamMembersCount', 'totalMilestones', 'categories'));
}
    // Running Projects
    public function running()
    {
        $projects = Projects::whereIn('status', ['pending', 'in_progress'])
                       ->withCount(['tasks', 'milestones'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }])
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
            'total_milestones' => $projects->sum('milestones_count'),
            'overdue' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->isPast();
            })->count()
        ];

        return view('admin.projects.running', compact('projects', 'runningStats'));
    }

    // Completed Projects
    public function completed()
    {
        $projects = Projects::where('status', 'completed')
                       ->withCount(['tasks', 'milestones'])
                       ->with(['tasks' => function($query) {
                           $query->select('project_id', 'status');
                       }])
                       ->latest()
                       ->get();

        // Calculate completed projects statistics
        $completionStats = [
            'total' => $projects->count(),
            'completed_this_month' => $projects->where('updated_at', '>=', now()->subMonth())->count(),
            'completed_this_quarter' => $projects->where('updated_at', '>=', now()->subMonths(3))->count(),
            'total_tasks_completed' => $projects->sum('tasks_count'),
            'total_milestones_completed' => $projects->sum('milestones_count'),
            'on_time' => $projects->filter(function($project) {
                return $project->due_date && \Carbon\Carbon::parse($project->due_date)->gte($project->updated_at);
            })->count()
        ];

        return view('admin.projects.completed', compact('projects', 'completionStats'));
    }

    // GET /projects/create
    public function create()
    {
$managers = User::where('role', '!=', 'client')->get();
    $clients = Client::active()->get();
    $teamMembers = User::where('role', 'user')->get();
    $categories = Category::where('is_active', true)->get();

    return view('projects.create', compact('managers', 'clients', 'teamMembers', 'categories'));
    }

    // POST /projects
  // In your ProjectController - store method
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
    $managerId = $request->manager_id;
    if (Auth::user()->role == 'admin') {
        $managerId = Auth::id();
    }

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

    return redirect()->route('projects.index')->with('success', 'Project created successfully');
}

    // GET /projects/{id}
    public function show($id)
    {
        $project = Projects::with([
            'tasks',
            'tasks.assignee',
            'tasks.creator',
            'manager',
            'client',
            'milestones',
            'milestones.tasks',
            'teamMembers'
        ])->findOrFail($id);

        return view('projects.show', compact('project'));
    }

    // GET /projects/{id}/edit
    public function edit($id)
    {
        $project = Projects::with(['teamMembers'])->findOrFail($id);
        $managers = User::where('role', 'admin')->orWhere('role', 'manager')->get();
        $clients = Client::active()->get();
    $categories = Category::all();
        $teamMembers = User::where('role', 'user')->get();

        return view('projects.edit', compact('project', 'managers', 'clients', 'teamMembers' ,'categories'));
    }

    // PUT /projects/{id}
    public function update(Request $request, $id)
    {
        $project = Projects::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
            'client_id' => 'nullable|exists:clients,id',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $oldManagerId = $project->manager_id;
        $oldStatus = $project->status;

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'due_date' => $request->due_date,
            'manager_id' => $request->manager_id,
            'client_id' => $request->client_id,
            'status' => $request->status,
            'created_by' => auth()->id(),
        ]);

        // Notify team members about project update (EXCEPT manager changes - handled separately)
        $this->notificationService->sendToTeamMembers($project, 'project_updated', [
            'title' => 'Project Updated',
            'message' => "Project '{$project->name}' has been updated",
            'action_url' => route('projects.show', $project),
            'icon' => 'fas fa-sync',
            'color' => 'indigo',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'updated_by' => auth()->user()->name,
        ]);

        // Notify if manager changed
        if ($oldManagerId != $request->manager_id) {
            // Notify NEW manager
            $newManager = User::find($request->manager_id);
            if ($newManager) {
                $this->notificationService->sendToUser($newManager->id, 'project_assigned', [
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

            // Notify OLD manager (if they exist and are not the new manager)
            $oldManager = User::find($oldManagerId);
            if ($oldManager && $oldManager->id != $request->manager_id) {
                $this->notificationService->sendToUser($oldManager->id, 'project_unassigned', [
                    'title' => 'Project Unassigned',
                    'message' => "You are no longer manager for project: {$project->name}",
                    'action_url' => route('projects.index'),
                    'icon' => 'fas fa-user-times',
                    'color' => 'gray',
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ]);
            }
        }

        // Notify if status changed to completed
        if ($oldStatus != 'completed' && $request->status == 'completed') {
            $this->notificationService->sendToTeamMembers($project, 'project_completed', [
                'title' => 'Project Completed',
                'message' => "Project '{$project->name}' has been completed",
                'action_url' => route('projects.show', $project),
                'icon' => 'fas fa-check-circle',
                'color' => 'green',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'completed_by' => auth()->user()->name,
            ]);

            // Notify super_admin about project completion
            $superAdmin = User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'project_completed', [
                    'title' => 'Project Completed',
                    'message' => "Project '{$project->name}' has been completed by " . auth()->user()->name,
                    'action_url' => route('projects.show', $project),
                    'icon' => 'fas fa-trophy',
                    'color' => 'green',
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'completed_by' => auth()->user()->name,
                ]);
            }
        }

        // Sync team members
        $oldTeamMembers = $project->teamMembers->pluck('id')->toArray();
        $newTeamMembers = $request->team_members ?? [];

        if ($request->has('team_members')) {
            $project->teamMembers()->sync($request->team_members);

            // Notify newly added team members (EXCEPT if they are the manager)
            $addedMembers = array_diff($newTeamMembers, $oldTeamMembers);
            foreach ($addedMembers as $memberId) {
                $teamMember = User::find($memberId);
                if ($teamMember && $teamMember->id != $project->manager_id) {
                    $this->notificationService->notifyTeamMemberAdded($project, $teamMember, auth()->user());
                }
            }

            // Notify removed team members
            $removedMembers = array_diff($oldTeamMembers, $newTeamMembers);
            foreach ($removedMembers as $memberId) {
                $this->notificationService->sendToUser($memberId, 'team_member_removed', [
                    'title' => 'Removed from Project',
                    'message' => "You have been removed from project: {$project->name}",
                    'action_url' => route('projects.index'),
                    'icon' => 'fas fa-user-minus',
                    'color' => 'red',
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'removed_by' => auth()->user()->name,
                ]);
            }
        } else {
            $project->teamMembers()->detach();

            // Notify all removed team members
            foreach ($oldTeamMembers as $memberId) {
                $this->notificationService->sendToUser($memberId, 'team_member_removed', [
                    'title' => 'Removed from Project',
                    'message' => "You have been removed from project: {$project->name}",
                    'action_url' => route('projects.index'),
                    'icon' => 'fas fa-user-minus',
                    'color' => 'red',
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'removed_by' => auth()->user()->name,
                ]);
            }
        }

        return redirect()->route('projects.show', $project->id)
                        ->with('success', 'Project updated successfully!');
    }

    // DELETE /projects/{id}
    public function destroy($id)
    {
        $project = Projects::findOrFail($id);

        // Notify team members about project deletion (EXCEPT manager - handled separately)
        $teamMembersToNotify = $project->teamMembers->where('id', '!=', $project->manager_id);
        foreach ($teamMembersToNotify as $member) {
            $this->notificationService->sendToUser($member->id, 'project_deleted', [
                'title' => 'Project Deleted',
                'message' => "Project '{$project->name}' has been deleted",
                'action_url' => route('projects.index'),
                'icon' => 'fas fa-trash',
                'color' => 'red',
                'project_name' => $project->name,
                'deleted_by' => auth()->user()->name,
            ]);
        }

        // Notify project manager separately
        if ($project->manager) {
            $this->notificationService->sendToUser($project->manager->id, 'project_deleted', [
                'title' => 'Project Deleted',
                'message' => "Project '{$project->name}' that you managed has been deleted",
                'action_url' => route('projects.index'),
                'icon' => 'fas fa-trash',
                'color' => 'red',
                'project_name' => $project->name,
                'deleted_by' => auth()->user()->name,
            ]);
        }

        // Notify super_admin about project deletion
        $superAdmin = User::where('role', 'super_admin')->first();
        if ($superAdmin && auth()->user()->id != $superAdmin->id) {
            $this->notificationService->sendToUser($superAdmin->id, 'project_deleted', [
                'title' => 'Project Deleted',
                'message' => "Project '{$project->name}' has been deleted by " . auth()->user()->name,
                'action_url' => route('projects.index'),
                'icon' => 'fas fa-trash',
                'color' => 'red',
                'project_name' => $project->name,
                'deleted_by' => auth()->user()->name,
            ]);
        }

        $project->delete();

        return redirect()->route('projects.index')
                        ->with('success', 'Project deleted successfully!');
    }

    // Mark project as completed
    public function markComplete($id)
    {
        $project = Projects::findOrFail($id);
        $oldStatus = $project->status;

        $project->update(['status' => 'completed']);

        // Notify status change to team members
        if ($oldStatus != 'completed') {
            $this->notificationService->sendToTeamMembers($project, 'project_completed', [
                'title' => 'Project Completed! 🎉',
                'message' => "Project '{$project->name}' has been successfully completed",
                'action_url' => route('projects.show', $project),
                'icon' => 'fas fa-check-circle',
                'color' => 'green',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'completed_by' => auth()->user()->name,
            ]);

            // Notify super_admin ONLY about project completion
            $superAdmin = User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'project_completed', [
                    'title' => 'Project Completed',
                    'message' => "Project '{$project->name}' has been completed by " . auth()->user()->name,
                    'action_url' => route('projects.show', $project),
                    'icon' => 'fas fa-trophy',
                    'color' => 'green',
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'completed_by' => auth()->user()->name,
                ]);
            }
        }

        return redirect()->back()
                        ->with('success', 'Project marked as completed!');
    }

    // Mark project as in progress
    public function markInProgress($id)
    {
        $project = Projects::findOrFail($id);
        $oldStatus = $project->status;

        $project->update(['status' => 'in_progress']);

        // Notify status change to team members only
        if ($oldStatus != 'in_progress') {
            $this->notificationService->sendToTeamMembers($project, 'project_status_changed', [
                'title' => 'Project Started',
                'message' => "Project '{$project->name}' is now in progress",
                'action_url' => route('projects.show', $project),
                'icon' => 'fas fa-play-circle',
                'color' => 'blue',
                'project_id' => $project->id,
                'project_name' => $project->name,
            ]);
        }

        return redirect()->back()
                        ->with('success', 'Project marked as in progress!');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $project = Projects::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:pending,in_progress,completed'
            ]);

            $oldStatus = $project->status;

            $project->update([
                'status' => $validated['status']
            ]);

            // Notify status change to team members
            if ($oldStatus != $validated['status']) {
                $statusMessages = [
                    'pending' => [
                        'title' => 'Project Pending',
                        'message' => "Project '{$project->name}' is now pending",
                        'icon' => 'fas fa-clock',
                        'color' => 'yellow'
                    ],
                    'in_progress' => [
                        'title' => 'Project Started',
                        'message' => "Project '{$project->name}' is now in progress",
                        'icon' => 'fas fa-play-circle',
                        'color' => 'blue'
                    ],
                    'completed' => [
                        'title' => 'Project Completed! 🎉',
                        'message' => "Project '{$project->name}' has been completed",
                        'icon' => 'fas fa-check-circle',
                        'color' => 'green'
                    ]
                ];

                $messageConfig = $statusMessages[$validated['status']];

                $this->notificationService->sendToTeamMembers($project, 'project_status_changed', array_merge($messageConfig, [
                    'action_url' => route('projects.show', $project),
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'changed_by' => auth()->user()->name,
                ]));

                // Special notification for completion to super_admin only
                if ($validated['status'] == 'completed') {
                    $superAdmin = User::where('role', 'super_admin')->first();
                    if ($superAdmin) {
                        $this->notificationService->sendToUser($superAdmin->id, 'project_completed', [
                            'title' => 'Project Completed',
                            'message' => "Project '{$project->name}' has been completed by " . auth()->user()->name,
                            'action_url' => route('projects.show', $project),
                            'icon' => 'fas fa-trophy',
                            'color' => 'green',
                            'project_id' => $project->id,
                            'project_name' => $project->name,
                            'completed_by' => auth()->user()->name,
                        ]);
                    }
                }
            }

            return redirect()->back()->with('success', 'Project status updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update project status');
        }
    }

    // Add team member to project
    public function addTeamMember(Request $request, $id)
    {
        $project = Projects::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $teamMember = User::find($request->user_id);

        if (!$project->teamMembers->contains($teamMember->id)) {
            $project->teamMembers()->attach($teamMember->id);

            // Notify team member (EXCEPT if they are the manager)
            if ($teamMember->id != $project->manager_id) {
                $this->notificationService->notifyTeamMemberAdded($project, $teamMember, auth()->user());
            }

            return redirect()->back()->with('success', 'Team member added successfully');
        }

        return redirect()->back()->with('error', 'Team member already exists in project');
    }

    // Remove team member from project
    public function removeTeamMember(Request $request, $id)
    {
        $project = Projects::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $teamMember = User::find($request->user_id);

        if ($project->teamMembers->contains($teamMember->id)) {
            $project->teamMembers()->detach($teamMember->id);

            // Notify removed team member
            $this->notificationService->sendToUser($teamMember->id, 'team_member_removed', [
                'title' => 'Removed from Project',
                'message' => "You have been removed from project: {$project->name}",
                'action_url' => route('projects.index'),
                'icon' => 'fas fa-user-minus',
                'color' => 'red',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'removed_by' => auth()->user()->name,
            ]);

            return redirect()->back()->with('success', 'Team member removed successfully');
        }

        return redirect()->back()->with('error', 'Team member not found in project');
    }

    // Upload file to project
    public function uploadFile(Request $request, $id)
    {
        $project = Projects::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'description' => 'nullable|string|max:500'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('project-files/' . $project->id, 'public');

            // Save file record to database (you'll need to create a File model)
            $uploadedFile = \App\Models\File::create([
                'project_id' => $project->id,
                'user_id' => auth()->id(),
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'description' => $request->description,
            ]);

            // Notify team members about file upload
            $this->notificationService->sendToTeamMembers($project, 'file_uploaded', [
                'title' => 'New File Uploaded',
                'message' => "New file '{$file->getClientOriginalName()}' has been uploaded to project '{$project->name}'",
                'action_url' => route('projects.show', $project) . '#files',
                'icon' => 'fas fa-file-upload',
                'color' => 'pink',
                'file_name' => $file->getClientOriginalName(),
                'uploaded_by' => auth()->user()->name,
                'project_name' => $project->name,
            ]);

            return redirect()->back()->with('success', 'File uploaded successfully');
        }

        return redirect()->back()->with('error', 'File upload failed');
    }
}
