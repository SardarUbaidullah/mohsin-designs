<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Projects;
use Illuminate\Support\Facades\Auth;
use App\Models\Tasks;
use App\Models\User;
use App\Models\Milestones;
use App\Models\Comment;
use App\Models\TimeLogs;
use App\Models\TaskSubtasks;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

        public function index()
    {
        if (!Auth::user()) {
            return view('home');
        }

       $user = Auth::user();

switch ($user->role) {
    case 'super_admin':
        return view('admin.dashboard');

    case 'admin':
        return $this->managerDashboard($user);

    case 'manager':
        return $this->managerDashboard($user); // Add manager case

    case 'user':
        return $this->teamDashboard($user);

    case 'client':
        return $this->clientDashboard($user); // Add client case

    default:
        return view('home');
}
    }



    private function clientDashboard($user)
{
    // Get client's projects and data for dashboard
    $projects = Projects::where('client_id', $user->client_id)
        ->withCount(['tasks', 'completedTasks'])

        ->get();

    $recentActivities = $this->getClientRecentActivities($user);
    $upcomingDeadlines = $this->getClientUpcomingDeadlines($user);

    return view('Client.dashboard', compact('projects', 'recentActivities', 'upcomingDeadlines'));
}

  private function getClientRecentActivities($user)
    {
        // Get recent comments and activities for client's projects
        $projectComments = Comment::whereHasMorph(
            'commentable',
            [Projects::class], // Use Projects::class instead of Project::class
            function($query) use ($user) {
                $query->where('client_id', $user->client_id);
            }
        )->with(['user', 'commentable'])->latest()->limit(10)->get();

        $taskComments = Comment::whereHasMorph(
            'commentable',
            [Tasks::class],
            function($query) use ($user) {
                $query->whereHas('project', function($q) use ($user) {
                    $q->where('client_id', $user->client_id);
                });
            }
        )->with(['user', 'commentable.project'])->latest()->limit(10)->get();

        return $projectComments->merge($taskComments)->sortByDesc('created_at')->take(10);
    }

    private function getClientUpcomingDeadlines($user)
    {
          $tasks = Tasks::whereHas('project', function($query) use ($user) {
        $query->where('client_id', $user->client_id);
    })->where('due_date', '>=', now())->orderBy('due_date')->limit(5)->get();

    // return $milestones->merge($tasks)->sortBy('due_date')->take(5);
    return $tasks->sortBy('due_date')->take(5); // Just return tasks
    }
      private function teamDashboard($user)
    {
        // Calculate user stats
        $totalTasks = Tasks::where('assigned_to', $user->id)->count();
        $completedTasks = Tasks::where('assigned_to', $user->id)->where('status', 'done')->count();
        $pendingTasks = Tasks::where('assigned_to', $user->id)->whereIn('status', ['todo', 'in_progress'])->count();

        $userStats = [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'pending_tasks' => $pendingTasks,
            'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0,
        ];

        // Get recent tasks
        $tasks = Tasks::where('assigned_to', $user->id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming deadlines
        $upcomingDeadlines = Tasks::where('assigned_to', $user->id)
            ->whereIn('status', ['todo', 'in_progress'])
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->orderBy('due_date', 'asc')
            ->limit(3)
            ->get();

        return view('team.index', compact('userStats', 'tasks', 'upcomingDeadlines'));
    }

    private function managerDashboard($user)
    {
        // Get projects based on user role
        $projects = Projects::where('manager_id', $user->id)->get();

        // Get statistics
        $activeProjects = $projects->where('status', 'in_progress')->count();
        $pendingTasks = Tasks::whereIn('project_id', $projects->pluck('id'))
                            ->where('status', '!=', 'done')
                            ->count();

        // Get team members (users assigned to tasks in manager's projects)
        $teamMembers = User::whereHas('assignedTasks', function ($q) use ($projects) {
            $q->whereIn('project_id', $projects->pluck('id'));
        })->distinct()->get();

        // Calculate completion rate
        $totalTasks = Tasks::whereIn('project_id', $projects->pluck('id'))->count();
        $completedTasks = Tasks::whereIn('project_id', $projects->pluck('id'))
                              ->where('status', 'done')
                              ->count();
        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Get recent activities
        $recentTasks = Tasks::whereIn('project_id', $projects->pluck('id'))
                           ->with('user')
                           ->latest()
                           ->take(5)
                           ->get();

        // Get upcoming deadlines
        $upcomingDeadlines = Tasks::whereIn('project_id', $projects->pluck('id'))
                                 ->where('due_date', '>=', now())
                                 ->where('status', '!=', 'done')
                                 ->with('project')
                                 ->orderBy('due_date')
                                 ->take(5)
                                 ->get();

        return view('manager.dashboard', compact(
            'projects',
            'activeProjects',
            'pendingTasks',
            'teamMembers',
            'completionRate',
            'recentTasks',
            'upcomingDeadlines',
            'completedTasks',
            'totalTasks'
        ));
    }
}
