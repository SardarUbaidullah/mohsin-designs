<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Projects as Project;
use App\Models\Tasks as Task;
use App\Models\User;
use App\Models\Teams;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function quickStats()
    {
        try {
            Log::info('Quick Stats API Called');

            $totalProjects = Project::count();
            $completedTasks = Task::where('status', 'completed')->orWhere('status', 'done')->count();
            $activeTeam = User::where('role', 'user')->count();
            $totalTasks = Task::count();
            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            Log::info('Quick Stats Results:', [
                'projects' => $totalProjects,
                'completed_tasks' => $completedTasks,
                'active_team' => $activeTeam,
                'completion_rate' => $completionRate
            ]);

            return response()->json([
                'total_projects' => $totalProjects,
                'completed_tasks' => $completedTasks,
                'active_team' => $activeTeam,
                'avg_performance' => $completionRate . '%',
                'completion_rate' => $completionRate
            ]);

        } catch (\Exception $e) {
            Log::error('Quick Stats Error: ' . $e->getMessage());
            return response()->json([
                'total_projects' => 0,
                'completed_tasks' => 0,
                'active_team' => 0,
                'avg_performance' => '0%',
                'completion_rate' => 0
            ], 500);
        }
    }

    public function getReportData($type)
    {
        try {
            Log::info("Report Data API Called: {$type}");

            switch ($type) {
                case 'progress':
                    $data = $this->getProgressData();
                    break;
                case 'workload':
                    $data = $this->getWorkloadData();
                    break;
                case 'performance':
                    $data = $this->getPerformanceData();
                    break;
                default:
                    return response()->json(['error' => 'Invalid report type'], 400);
            }

            Log::info("Report Data for {$type}:", $data);
            return response()->json($data);

        } catch (\Exception $e) {
            Log::error("Report Data Error [{$type}]: " . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load report data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function getProgressData()
    {
        // Project Progress - handle different possible status values
        $totalProjects = Project::count();
        $completedProjects = Project::where('status', 'completed')
            ->orWhere('status', 'done')
            ->orWhere('status', 'finished')
            ->count();
        $inProgressProjects = Project::where('status', 'in_progress')
            ->orWhere('status', 'pending')
            ->orWhere('status', 'active')
            ->count();
        $planningProjects = Project::where('status', 'planning')
            ->orWhere('status', 'draft')
            ->orWhere('status', 'new')
            ->count();

        // If we still have unclassified projects, put them in planning
        $classifiedProjects = $completedProjects + $inProgressProjects + $planningProjects;
        if ($classifiedProjects < $totalProjects) {
            $planningProjects += ($totalProjects - $classifiedProjects);
        }

        // Task Progress - handle different possible status values
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')
            ->orWhere('status', 'done')
            ->orWhere('status', 'finished')
            ->count();
        $inProgressTasks = Task::where('status', 'in_progress')
            ->orWhere('status', 'pending')
            ->orWhere('status', 'active')
            ->count();
        $todoTasks = Task::where('status', 'todo')
            ->orWhere('status', 'new')
            ->orWhere('status', 'open')
            ->count();

        // If we still have unclassified tasks, put them in todo
        $classifiedTasks = $completedTasks + $inProgressTasks + $todoTasks;
        if ($classifiedTasks < $totalTasks) {
            $todoTasks += ($totalTasks - $classifiedTasks);
        }

        // Recent Projects with fallback
        $recentProjects = Project::with(['tasks' => function($query) {
            $query->select('id', 'project_id', 'status');
        }])->latest()->take(5)->get()->map(function($project) {
            return [
                'name' => $project->name ?? 'Unnamed Project',
                'status' => $project->status ?? 'planning',
                'total_tasks' => $project->tasks->count(),
                'completed_tasks' => $project->tasks->whereIn('status', ['completed', 'done', 'finished'])->count()
            ];
        });

        return [
            'projects' => [
                'total' => $totalProjects,
                'completed' => $completedProjects,
                'in_progress' => $inProgressProjects,
                'planning' => $planningProjects,
                'completion_rate' => $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0
            ],
            'tasks' => [
                'total' => $totalTasks,
                'completed' => $completedTasks,
                'in_progress' => $inProgressTasks,
                'todo' => $todoTasks,
                'completion_rate' => $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0
            ],
            'recent_projects' => $recentProjects
        ];
    }

  private function getWorkloadData()
{
    // Get BOTH users and admins for workload
    $users = User::whereIn('role', ['admin', 'user'])->get();

    if ($users->isEmpty()) {
        Log::warning('No users found for workload data');
        return [
            'team_workload' => [],
            'summary' => [
                'total_team_members' => 0,
                'total_assigned_tasks' => 0,
                'total_managed_projects' => 0,
                'total_completed' => 0,
                'total_in_progress' => 0
            ]
        ];
    }

    $teamWorkload = $users->map(function($user) {
        if ($user->role === 'user') {
            // For users: show task workload
            return $this->getUserTaskWorkload($user);
        } else {
            // For admins: show project workload (using manager_id)
            return $this->getAdminProjectWorkload($user);
        }
    });

    // Calculate summaries
    $totalAssignedTasks = $teamWorkload->where('user.role', 'user')->sum('total_items');
    $totalManagedProjects = $teamWorkload->where('user.role', 'admin')->sum('total_items');
    $totalCompleted = $teamWorkload->sum('completed_items');
    $totalInProgress = $teamWorkload->sum('in_progress_items');

    return [
        'team_workload' => $teamWorkload,
        'summary' => [
            'total_team_members' => $users->count(),
            'total_assigned_tasks' => $totalAssignedTasks,
            'total_managed_projects' => $totalManagedProjects,
            'total_completed' => $totalCompleted,
            'total_in_progress' => $totalInProgress
        ]
    ];
}

private function getUserTaskWorkload($user)
{
    $totalTasks = Task::where('assigned_to', $user->id)->count();
    $completedTasks = Task::where('assigned_to', $user->id)
        ->whereIn('status', ['completed', 'done', 'finished'])
        ->count();
    $inProgressTasks = Task::where('assigned_to', $user->id)
        ->whereIn('status', ['in_progress', 'pending', 'active'])
        ->count();
    $todoTasks = Task::where('assigned_to', $user->id)
        ->whereIn('status', ['todo', 'new', 'open'])
        ->count();

    // If statuses don't match, calculate todo as difference
    $calculatedTodo = $totalTasks - $completedTasks - $inProgressTasks;
    if ($calculatedTodo > $todoTasks) {
        $todoTasks = max(0, $calculatedTodo);
    }

    $workloadLevel = $this->getWorkloadLevel($inProgressTasks, 'task');

    return [
        'user' => [
            'id' => $user->id,
            'name' => $user->name ?? 'User ' . $user->id,
            'role' => $user->role
        ],
        'workload_type' => 'task_based',
        'total_items' => $totalTasks,
        'completed_items' => $completedTasks,
        'in_progress_items' => $inProgressTasks,
        'todo_items' => $todoTasks,
        'workload_level' => $workloadLevel,
    ];
}

private function getAdminProjectWorkload($user)
{
    // For admins: get projects they manage (using manager_id)
    $totalProjects = Project::where('manager_id', $user->id)->count();

    $completedProjects = Project::where('manager_id', $user->id)
        ->whereIn('status', ['completed', 'done', 'finished'])
        ->count();

    $inProgressProjects = Project::where('manager_id', $user->id)
        ->whereIn('status', ['in_progress', 'pending', 'active'])
        ->count();

    $planningProjects = Project::where('manager_id', $user->id)
        ->whereIn('status', ['planning', 'draft', 'new'])
        ->count();

    // Calculate workload level based on in-progress projects
    $workloadLevel = $this->getWorkloadLevel($inProgressProjects, 'project');

    return [
        'user' => [
            'id' => $user->id,
            'name' => $user->name ?? 'Admin ' . $user->id,
            'role' => $user->role
        ],
        'workload_type' => 'project_based',
        'total_items' => $totalProjects,
        'completed_items' => $completedProjects,
        'in_progress_items' => $inProgressProjects,
        'todo_items' => $planningProjects,
        'workload_level' => $workloadLevel,
    ];
}

private function getPerformanceData()
{
    // Get BOTH admins and users
    $users = User::whereIn('role', ['admin', 'user'])->get();

    if ($users->isEmpty()) {
        Log::warning('No users found for performance data');
        return [
            'user_performance' => [],
            'quality_metrics' => [
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'in_progress_tasks' => 0,
                'team_productivity' => 0
            ]
        ];
    }

    $userPerformance = $users->map(function($user) {
        if ($user->role === 'user') {
            return $this->getUserTaskPerformance($user);
        } else {
            return $this->getAdminProjectPerformance($user);
        }
    })->filter();

    // Calculate team productivity for ALL users (both roles)
    $teamProductivity = $userPerformance->isNotEmpty()
        ? round($userPerformance->avg('completion_rate'), 2)
        : 0;

    // Get task metrics for ALL tasks
    $totalTasks = Task::count();
    $completedTasks = Task::whereIn('status', ['completed', 'done', 'finished'])->count();
    $inProgressTasks = Task::whereIn('status', ['in_progress', 'pending', 'active'])->count();

    return [
        'user_performance' => $userPerformance->values(), // This includes BOTH users and admins
        'quality_metrics' => [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'team_productivity' => $teamProductivity
        ]
    ];
}
private function getAdminProjectPerformance($user)
{
    try {
        // For admins, analyze based on projects they manage (using manager_id)
        $totalProjects = Project::where('manager_id', $user->id)->count();

        $completedProjects = Project::where('manager_id', $user->id)
            ->whereIn('status', ['completed', 'done', 'finished'])
            ->count();

        $completionRate = $totalProjects > 0 ? round(($completedProjects / $totalProjects) * 100, 2) : 0;

        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name ?? 'Admin ' . $user->id,
                'role' => $user->role
            ],
            'total_projects' => $totalProjects,
            'completed_projects' => $completedProjects,
            'completion_rate' => $completionRate,
            'performance_level' => $this->getPerformanceLevel($completionRate),
            'performance_type' => 'project_based'
        ];
    } catch (\Exception $e) {
        Log::error("Admin Project Performance Error for user {$user->id}: " . $e->getMessage());
        return null;
    }
}


    private function getUserTaskPerformance($user)
    {
        try {
            $totalTasks = Task::where('assigned_to', $user->id)->count();
            $completedTasks = Task::where('assigned_to', $user->id)
                ->whereIn('status', ['completed', 'done', 'finished'])
                ->count();

            $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 2) : 0;

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name ?? 'User ' . $user->id,
                    'role' => $user->role
                ],
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'completion_rate' => $completionRate,
                'performance_level' => $this->getPerformanceLevel($completionRate),
                'performance_type' => 'task_based'
            ];
        } catch (\Exception $e) {
            Log::error("User Task Performance Error for user {$user->id}: " . $e->getMessage());
            return null;
        }
    }

//    private function getAdminProjectWorkload($user)
// {
//     // For admins: get projects they manage (using manager_id)
//     $totalProjects = Project::where('manager_id', $user->id)->count();

//     $completedProjects = Project::where('manager_id', $user->id)
//         ->whereIn('status', ['completed', 'done', 'finished'])
//         ->count();

//     $inProgressProjects = Project::where('manager_id', $user->id)
//         ->whereIn('status', ['in_progress', 'pending', 'active'])
//         ->count();

//     $planningProjects = Project::where('manager_id', $user->id)
//         ->whereIn('status', ['planning', 'draft', 'new'])
//         ->count();

//     // Calculate workload level based on in-progress projects
//     $workloadLevel = $this->getWorkloadLevel($inProgressProjects);

//     return [
//         'user' => [
//             'id' => $user->id,
//             'name' => $user->name ?? 'Admin ' . $user->id,
//             'role' => $user->role
//         ],
//         'workload_type' => 'project_based',
//         'total_items' => $totalProjects,
//         'completed_items' => $completedProjects,
//         'in_progress_items' => $inProgressProjects,
//         'todo_items' => $planningProjects,
//         'workload_level' => $workloadLevel,
//     ];
// }

   private function getWorkloadLevel($inProgressCount, $type = 'task')
{
    if ($type === 'project') {
        // For projects - different thresholds
        if ($inProgressCount == 0) return 'Low';
        if ($inProgressCount <= 1) return 'Normal';
        if ($inProgressCount <= 3) return 'High';
        return 'Overloaded';
    } else {
        // For tasks - original thresholds
        if ($inProgressCount == 0) return 'Low';
        if ($inProgressCount <= 2) return 'Normal';
        if ($inProgressCount <= 5) return 'High';
        return 'Overloaded';
    }
}

    private function getPerformanceLevel($completionRate)
    {
        if ($completionRate >= 90) return 'Excellent';
        if ($completionRate >= 75) return 'Very Good';
        if ($completionRate >= 60) return 'Good';
        if ($completionRate >= 40) return 'Average';
        return 'Needs Improvement';
    }
}
