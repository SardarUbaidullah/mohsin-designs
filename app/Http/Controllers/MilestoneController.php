<?php

namespace App\Http\Controllers;

use App\Models\Milestones;
use App\Models\Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Providers\NotificationService;

class MilestoneController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Check if user has access to the milestone
     */
    private function checkMilestoneAccess($milestone)
    {
        $user = auth()->user();

        // Super admin has access to all milestones
        if ($user->role === 'super_admin') {
            return true;
        }

        // Admin/Manager and User can only access their project's milestones
        if (in_array($user->role, ['admin', 'user'])) {
            return $milestone->project->manager_id === $user->id;
        }

        return false;
    }

    /**
     * Get projects based on user role
     */
    private function getAccessibleProjects()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            return Projects::where('status', '!=', 'completed')
                         ->orWhereNull('status')
                         ->latest()
                         ->get();
        }

        if (in_array($user->role, ['admin', 'user'])) {
            return Projects::where('manager_id', $user->id)
                         ->where(function($query) {
                             $query->where('status', '!=', 'completed')
                                   ->orWhereNull('status');
                         })
                         ->latest()
                         ->get();
        }

        return collect();
    }

    /**
     * Display a listing of milestones.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->role === 'super_admin';
        $isManager = $user->role === 'admin';
        $isUser = $user->role === 'user';

        // Base query based on role
        if ($isSuperAdmin) {
            $query = Milestones::with(['project', 'tasks']);
            $projectsQuery = Projects::query();
        } elseif ($isManager || $isUser) {
            $query = Milestones::whereHas('project', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            })->with(['project', 'tasks']);
            $projectsQuery = Projects::where('manager_id', $user->id);
        } else {
            abort(403, 'Unauthorized access.');
        }

        // Filter by project if provided
        if ($request->has('project_id') && $request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $milestones = $query->latest()->paginate(10);
        $projects = $projectsQuery->get();
        $selectedProject = $request->has('project_id') ? Projects::find($request->project_id) : null;

        // Get milestone statistics based on role
        if ($isSuperAdmin) {
            $milestoneStats = [
                'total' => Milestones::count(),
                'completed' => Milestones::where('status', 'completed')->count(),
                'in_progress' => Milestones::where('status', 'in_progress')->count(),
                'pending' => Milestones::where('status', 'pending')->count(),
            ];
        } else {
            $milestoneStats = [
                'total' => Milestones::whereHas('project', function($query) use ($user) {
                    $query->where('manager_id', $user->id);
                })->count(),
                'completed' => Milestones::whereHas('project', function($query) use ($user) {
                    $query->where('manager_id', $user->id);
                })->where('status', 'completed')->count(),
                'in_progress' => Milestones::whereHas('project', function($query) use ($user) {
                    $query->where('manager_id', $user->id);
                })->where('status', 'in_progress')->count(),
                'pending' => Milestones::whereHas('project', function($query) use ($user) {
                    $query->where('manager_id', $user->id);
                })->where('status', 'pending')->count(),
            ];
        }

        return view('admin.milestones.index', compact(
            'milestones',
            'projects',
            'selectedProject',
            'milestoneStats',
            'isSuperAdmin',
            'isManager',
            'isUser'
        ));
    }

    /**
     * Show the form for creating a new milestone.
     */
    public function create()
    {
        $user = auth()->user();

        if (!in_array($user->role, ['super_admin', 'admin', 'user'])) {
            abort(403, 'Unauthorized access.');
        }

        $projects = $this->getAccessibleProjects();
        $selectedProjectId = request('project_id');

        return view('admin.milestones.create', compact('projects', 'selectedProjectId'));
    }

    /**
     * Store a newly created milestone in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['super_admin', 'admin', 'user'])) {
            abort(403, 'Unauthorized access.');
        }

        // For admin and user roles, validate that they own the project
        $projectValidation = 'required|exists:projects,id';
        if (in_array($user->role, ['admin', 'user'])) {
            $projectValidation .= '|exists:projects,id,manager_id,' . $user->id;
        }

        $validated = $request->validate([
            'project_id' => $projectValidation,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        try {
            DB::transaction(function () use ($validated, $user) {
                $milestone = Milestones::create($validated);

                // NOTIFICATION: New milestone created
                $this->notifyMilestoneCreated($milestone, $user);
            });

            // Determine redirect route based on user role
            if ($user->role === 'super_admin') {
                $redirectRoute = $request->has('redirect_to_project')
                    ? route('projects.show', $validated['project_id'])
                    : route('milestones.index', ['project_id' => $validated['project_id']]);
            } else {
                $redirectRoute = $request->has('redirect_to_project')
                    ? route('manager.projects.show', $validated['project_id'])
                    : route('manager.milestones.index', ['project_id' => $validated['project_id']]);
            }

            return redirect($redirectRoute)
                ->with('success', 'Milestone created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create milestone. Please try again.');
        }
    }

    /**
     * Display the specified milestone.
     */
    public function show($id)
    {
        $milestone = Milestones::with(['project', 'tasks.assignee', 'tasks.subtasks'])
                            ->findOrFail($id);

        // Check access
        if (!$this->checkMilestoneAccess($milestone)) {
            abort(403, 'Unauthorized access.');
        }

        // Calculate progress
        $totalTasks = $milestone->tasks->count();
        $completedTasks = $milestone->tasks->where('status', 'done')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        return view('admin.milestones.show', compact('milestone', 'progress', 'totalTasks', 'completedTasks'));
    }

    /**
     * Show the form for editing the specified milestone.
     */
    public function edit($id)
    {
        $milestone = Milestones::findOrFail($id);

        // Check access
        if (!$this->checkMilestoneAccess($milestone)) {
            abort(403, 'Unauthorized access.');
        }

        $projects = $this->getAccessibleProjects();

        return view('admin.milestones.edit', compact('milestone', 'projects'));
    }

    /**
     * Update the specified milestone in storage.
     */
    public function update(Request $request, $id)
    {
        $milestone = Milestones::findOrFail($id);
        $user = auth()->user();

        // Check access
        if (!$this->checkMilestoneAccess($milestone)) {
            abort(403, 'Unauthorized access.');
        }

        // For admin and user roles, validate that they own the project if changing project
        $projectValidation = 'required|exists:projects,id';
        if (in_array($user->role, ['admin', 'user'])) {
            $projectValidation .= '|exists:projects,id,manager_id,' . $user->id;
        }

        $validated = $request->validate([
            'project_id' => $projectValidation,
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $oldStatus = $milestone->status;
        $oldDueDate = $milestone->due_date;

        try {
            DB::transaction(function () use ($milestone, $validated, $user, $oldStatus, $oldDueDate) {
                $milestone->update($validated);

                // If milestone is completed, update related tasks
                if ($validated['status'] === 'completed') {
                    $milestone->tasks()->where('status', '!=', 'done')->update(['status' => 'done']);
                }

                // NOTIFICATION: Milestone updated
                $this->notifyMilestoneUpdated($milestone, $user, $oldStatus, $oldDueDate);
            });

            // Determine redirect route based on user role
            if ($user->role === 'super_admin') {
                $redirectRoute = $request->has('redirect_to_project')
                    ? route('projects.show', $milestone->project_id)
                    : route('milestones.index', ['project_id' => $milestone->project_id]);
            } else {
                $redirectRoute = $request->has('redirect_to_project')
                    ? route('manager.projects.show', $milestone->project_id)
                    : route('manager.milestones.index', ['project_id' => $milestone->project_id]);
            }

            return redirect($redirectRoute)
                ->with('success', 'Milestone updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update milestone. Please try again.');
        }
    }

    /**
     * Remove the specified milestone from storage.
     */
    public function destroy($id)
    {
        $milestone = Milestones::findOrFail($id);
        $user = auth()->user();

        // Check access
        if (!$this->checkMilestoneAccess($milestone)) {
            abort(403, 'Unauthorized access.');
        }

        $projectId = $milestone->project_id;

        try {
            DB::transaction(function () use ($milestone, $user) {
                // Store milestone info for notification before deletion
                $milestoneInfo = [
                    'title' => $milestone->title,
                    'project_name' => $milestone->project->name,
                    'project_id' => $milestone->project_id
                ];

                // NOTIFICATION: Milestone deleted
                $this->notifyMilestoneDeleted($milestone, $user);

                // Detach tasks from milestone before deletion
                $milestone->tasks()->update(['milestone_id' => null]);
                $milestone->delete();
            });

            // Determine redirect route based on user role
            if ($user->role === 'super_admin') {
                return redirect()->route('milestones.index', ['project_id' => $projectId])
                    ->with('success', 'Milestone deleted successfully!');
            } else {
                return redirect()->route('manager.milestones.index', ['project_id' => $projectId])
                    ->with('success', 'Milestone deleted successfully!');
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete milestone. Please try again.');
        }
    }

    /**
     * Update milestone status via AJAX
     */
    public function updateStatus(Request $request, $id)
    {
        $milestone = Milestones::findOrFail($id);
        $user = auth()->user();

        // Check access
        if (!$this->checkMilestoneAccess($milestone)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        $oldStatus = $milestone->status;
        $milestone->update(['status' => $request->status]);

        // NOTIFICATION: Milestone status changed
        $this->notifyMilestoneStatusChanged($milestone, $user, $oldStatus);

        return response()->json([
            'success' => true,
            'message' => 'Milestone status updated successfully!',
            'status' => $milestone->status
        ]);
    }

    /**
     * Get milestones for a specific project (AJAX)
     */
    public function getProjectMilestones($projectId)
    {
        $user = auth()->user();

        // Check project access
        $project = Projects::findOrFail($projectId);

        if (in_array($user->role, ['admin', 'user']) && $project->manager_id !== $user->id) {
            return response()->json([], 403);
        }

        $milestones = Milestones::where('project_id', $projectId)
                              ->where('status', '!=', 'completed')
                              ->get(['id', 'title']);

        return response()->json($milestones);
    }

    /**
     * Bulk update milestones status
     */
    public function bulkUpdate(Request $request)
    {
        $user = auth()->user();

        if (!in_array($user->role, ['super_admin', 'admin', 'user'])) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'milestone_ids' => 'required|array',
            'milestone_ids.*' => 'exists:milestones,id',
            'status' => 'required|in:pending,in_progress,completed'
        ]);

        // For admin and user roles, filter milestones they have access to
        $milestoneQuery = Milestones::whereIn('id', $request->milestone_ids);

        if (in_array($user->role, ['admin', 'user'])) {
            $milestoneQuery->whereHas('project', function($query) use ($user) {
                $query->where('manager_id', $user->id);
            });
        }

        $milestones = $milestoneQuery->get();

        try {
            $milestoneQuery->update(['status' => $request->status]);

            // NOTIFICATION: Bulk milestone status update
            $this->notifyBulkMilestoneUpdate($milestones, $user, $request->status);

            return redirect()->back()
                ->with('success', 'Selected milestones updated successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update milestones. Please try again.');
        }
    }

    // ==================== NOTIFICATION METHODS ====================

    /**
     * Notify when milestone is created
     */
    private function notifyMilestoneCreated($milestone, $createdBy)
    {
        $project = $milestone->project;

        $data = [
            'title' => 'New Milestone Created',
            'message' => "New milestone '{$milestone->title}' has been created for project '{$project->name}'",
            'action_url' => route('milestones.show', $milestone),
            'icon' => 'fas fa-flag',
            'color' => 'blue',
            'milestone_id' => $milestone->id,
            'milestone_title' => $milestone->title,
            'project_id' => $project->id,
            'project_name' => $project->name,
            'due_date' => $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') : 'Not set',
            'created_by' => $createdBy->name,
        ];

        // Notify project team members
        $this->notificationService->sendToTeamMembers($project, 'milestone_created', $data);

        // Notify super admin if created by admin or user
        if ($createdBy->role !== 'super_admin') {
            $superAdmin = \App\Models\User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'milestone_created', $data);
            }
        }
    }

    /**
     * Notify when milestone is updated
     */
    private function notifyMilestoneUpdated($milestone, $updatedBy, $oldStatus, $oldDueDate)
    {
        $project = $milestone->project;
        $changes = [];

        // Check for status change
        if ($oldStatus !== $milestone->status) {
            $changes[] = "status changed from {$oldStatus} to {$milestone->status}";
        }

        // Check for due date change
        if ($oldDueDate != $milestone->due_date) {
          $oldDueDateFormatted = $oldDueDate ? \Carbon\Carbon::parse($oldDueDate)->format('M d, Y') : 'Not set';
$newDueDateFormatted = $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') : 'Not set';
            $changes[] = "due date changed from {$oldDueDateFormatted} to {$newDueDateFormatted}";
        }

        $changeDescription = !empty($changes) ? ' (' . implode(', ', $changes) . ')' : '';

        $data = [
            'title' => 'Milestone Updated',
            'message' => "Milestone '{$milestone->title}' has been updated{$changeDescription}",
            'action_url' => route('milestones.show', $milestone),
            'icon' => 'fas fa-edit',
            'color' => 'yellow',
            'milestone_id' => $milestone->id,
            'milestone_title' => $milestone->title,
            'project_id' => $project->id,
            'project_name' => $project->name,
            'updated_by' => $updatedBy->name,
            'changes' => $changes,
        ];

        // Notify project team members
        $this->notificationService->sendToTeamMembers($project, 'milestone_updated', $data);
    }

    /**
     * Notify when milestone status changes
     */
    private function notifyMilestoneStatusChanged($milestone, $changedBy, $oldStatus)
    {
        if ($oldStatus === $milestone->status) {
            return; // No change, no notification
        }

        $project = $milestone->project;

        $statusConfig = [
            'pending' => ['icon' => 'fas fa-clock', 'color' => 'yellow', 'title' => 'Milestone Pending'],
            'in_progress' => ['icon' => 'fas fa-play-circle', 'color' => 'blue', 'title' => 'Milestone Started'],
            'completed' => ['icon' => 'fas fa-check-circle', 'color' => 'green', 'title' => 'Milestone Completed! 🎉'],
        ];

        $config = $statusConfig[$milestone->status] ?? $statusConfig['pending'];

        $data = [
            'title' => $config['title'],
            'message' => "Milestone '{$milestone->title}' status changed from {$oldStatus} to {$milestone->status}",
            'action_url' => route('milestones.show', $milestone),
            'icon' => $config['icon'],
            'color' => $config['color'],
            'milestone_id' => $milestone->id,
            'milestone_title' => $milestone->title,
            'project_id' => $project->id,
            'project_name' => $project->name,
            'old_status' => $oldStatus,
            'new_status' => $milestone->status,
            'changed_by' => $changedBy->name,
        ];

        // Notify project team members
        $this->notificationService->sendToTeamMembers($project, 'milestone_status_changed', $data);

        // Special notification for completion
        if ($milestone->status === 'completed') {
            // Notify super admin
            $superAdmin = \App\Models\User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'milestone_completed', array_merge($data, [
                    'title' => 'Milestone Completed',
                    'message' => "Milestone '{$milestone->title}' has been completed in project '{$project->name}'",
                ]));
            }
        }
    }

    /**
     * Notify when milestone is deleted
     */
    private function notifyMilestoneDeleted($milestone, $deletedBy)
    {
        $project = $milestone->project;

        $data = [
            'title' => 'Milestone Deleted',
            'message' => "Milestone '{$milestone->title}' has been deleted from project '{$project->name}'",
            'action_url' => route('milestones.index', ['project_id' => $project->id]),
            'icon' => 'fas fa-trash',
            'color' => 'red',
            'milestone_title' => $milestone->title,
            'project_id' => $project->id,
            'project_name' => $project->name,
            'deleted_by' => $deletedBy->name,
        ];

        // Notify project team members
        $this->notificationService->sendToTeamMembers($project, 'milestone_deleted', $data);

        // Notify super admin if deleted by admin or user
        if ($deletedBy->role !== 'super_admin') {
            $superAdmin = \App\Models\User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'milestone_deleted', $data);
            }
        }
    }

    /**
     * Notify for bulk milestone updates
     */
    private function notifyBulkMilestoneUpdate($milestones, $updatedBy, $newStatus)
    {
        if ($milestones->isEmpty()) {
            return;
        }

        $project = $milestones->first()->project; // Use first project for context
        $milestoneCount = $milestones->count();

        $data = [
            'title' => 'Multiple Milestones Updated',
            'message' => "{$milestoneCount} milestones have been updated to {$newStatus} status",
            'action_url' => route('milestones.index', ['project_id' => $project->id]),
            'icon' => 'fas fa-sync',
            'color' => 'purple',
            'milestone_count' => $milestoneCount,
            'new_status' => $newStatus,
            'updated_by' => $updatedBy->name,
            'project_id' => $project->id,
            'project_name' => $project->name,
        ];

        // Notify project team members
        $this->notificationService->sendToTeamMembers($project, 'milestone_bulk_updated', $data);
    }

    /**
     * Notify when milestone deadline is approaching
     */
    public function notifyApproachingDeadlines()
    {
        $approachingMilestones = Milestones::where('status', '!=', 'completed')
            ->where('due_date', '<=', now()->addDays(3))
            ->where('due_date', '>=', now())
            ->with('project')
            ->get();

        foreach ($approachingMilestones as $milestone) {
            $project = $milestone->project;
            $daysLeft = now()->diffInDays($milestone->due_date);

            $data = [
                'title' => 'Milestone Deadline Approaching',
                'message' => "Milestone '{$milestone->title}' is due in {$daysLeft} day(s)",
                'action_url' => route('milestones.show', $milestone),
                'icon' => 'fas fa-clock',
                'color' => 'orange',
                'milestone_id' => $milestone->id,
                'milestone_title' => $milestone->title,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'due_date' => $milestone->due_date->format('M d, Y'),
                'days_left' => $daysLeft,
            ];

            // Notify project team members and manager
            $this->notificationService->sendToTeamMembers($project, 'milestone_deadline_approaching', $data);
            $this->notificationService->sendToProjectManager($project, 'milestone_deadline_approaching', $data);
        }
    }

    /**
     * Notify when milestone is overdue
     */
    public function notifyOverdueMilestones()
    {
        $overdueMilestones = Milestones::where('status', '!=', 'completed')
            ->where('due_date', '<', now())
            ->with('project')
            ->get();

        foreach ($overdueMilestones as $milestone) {
            $project = $milestone->project;
            $daysOverdue = now()->diffInDays($milestone->due_date);

            $data = [
                'title' => 'Milestone Overdue',
                'message' => "Milestone '{$milestone->title}' is {$daysOverdue} day(s) overdue",
                'action_url' => route('milestones.show', $milestone),
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'red',
                'milestone_id' => $milestone->id,
                'milestone_title' => $milestone->title,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'due_date' => $milestone->due_date ? \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') : 'Not set',
                'days_overdue' => $daysOverdue,
            ];

            // Notify project team members, manager, and super admin
            $this->notificationService->sendToTeamMembers($project, 'milestone_overdue', $data);
            $this->notificationService->sendToProjectManager($project, 'milestone_overdue', $data);

            $superAdmin = \App\Models\User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                $this->notificationService->sendToUser($superAdmin->id, 'milestone_overdue', $data);
            }
        }
    }
}