<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use App\Models\Tasks as Task;
use App\Models\Projects as Project;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TimeReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.time-reports');
    }

    // 1. Get Time Summary Report
    public function getTimeSummary(Request $request)
    {
        try {
            $dateRange = $request->get('range', '30'); // Default to 30 days

            $startDate = match($dateRange) {
                '7' => Carbon::now()->subDays(7),
                '30' => Carbon::now()->subDays(30),
                '90' => Carbon::now()->subDays(90),
                '365' => Carbon::now()->subDays(365),
                'all' => null, // No date filter for "all"
                default => Carbon::now()->subDays(30)
            };

            // Build base query
            $query = TimeLog::where('is_running', false)
                ->whereNotNull('end_time');

            if ($dateRange !== 'all' && $startDate) {
                $query->where('start_time', '>=', $startDate);
            }

            // Calculate total minutes (use calculated duration if duration_minutes is 0)
            $timeLogs = $query->get();
            $totalMinutes = 0;
            
            foreach ($timeLogs as $log) {
                $totalMinutes += $this->calculateDurationMinutes($log);
            }
            
            $totalHours = round($totalMinutes / 60, 2);

            // Time by user
            $userQuery = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->with(['user' => function($q) {
                    $q->select('id', 'name');
                }]);

            if ($dateRange !== 'all' && $startDate) {
                $userQuery->where('start_time', '>=', $startDate);
            }

            $userLogs = $userQuery->get();
            
            $timeByUser = $userLogs->groupBy('user_id')
                ->map(function($group, $userId) {
                    $firstLog = $group->first();
                    $user = $firstLog->user;
                    
                    $totalMinutes = 0;
                    foreach ($group as $log) {
                        $totalMinutes += $this->calculateDurationMinutes($log);
                    }
                    
                    return [
                        'user' => $user ? ['name' => $user->name] : ['name' => 'Unknown'],
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_time' => $this->formatMinutes($totalMinutes)
                    ];
                })
                ->filter(fn($item) => $item['total_minutes'] > 0)
                ->sortByDesc('total_minutes')
                ->values();

            // Time by project
            $projectQuery = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->whereHas('task')
                ->with(['task.project' => function($q) {
                    $q->select('id', 'name');
                }]);

            if ($dateRange !== 'all' && $startDate) {
                $projectQuery->where('start_time', '>=', $startDate);
            }

            $projectLogs = $projectQuery->get();
            
            $timeByProject = $projectLogs->groupBy(function($item) {
                    return $item->task->project->id ?? null;
                })
                ->map(function($group, $projectId) {
                    if (!$projectId) return null;
                    
                    $firstItem = $group->first();
                    $project = $firstItem->task->project ?? null;
                    
                    if (!$project) return null;
                    
                    $totalMinutes = 0;
                    foreach ($group as $log) {
                        $totalMinutes += $this->calculateDurationMinutes($log);
                    }
                    
                    return [
                        'project' => [
                            'id' => $project->id,
                            'name' => $project->name
                        ],
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_time' => $this->formatMinutes($totalMinutes)
                    ];
                })
                ->filter(fn($item) => $item && $item['total_minutes'] > 0)
                ->sortByDesc('total_minutes')
                ->values();

            // Time by task (top 10)
            $taskQuery = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->with(['task' => function($q) {
                    $q->select('id', 'title', 'project_id')->with('project:id,name');
                }]);

            if ($dateRange !== 'all' && $startDate) {
                $taskQuery->where('start_time', '>=', $startDate);
            }

            $taskLogs = $taskQuery->get();
            
            $timeByTask = $taskLogs->groupBy('task_id')
                ->map(function($group, $taskId) {
                    $firstLog = $group->first();
                    $task = $firstLog->task;
                    
                    $totalMinutes = 0;
                    foreach ($group as $log) {
                        $totalMinutes += $this->calculateDurationMinutes($log);
                    }
                    
                    return [
                        'task' => $task ? [
                            'title' => $task->title,
                            'project' => $task->project
                        ] : null,
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_time' => $this->formatMinutes($totalMinutes)
                    ];
                })
                ->filter(fn($item) => $item['total_minutes'] > 0)
                ->sortByDesc('total_minutes')
                ->take(10)
                ->values();

            // Additional stats
            $totalTasksTracked = $query->distinct('task_id')->count('task_id');
            $teamMembers = $timeByUser->count();
            $days = $dateRange === 'all' ? 30 : (int)$dateRange; // Use 30 days for "all" calculation
            $avgDailyTime = $days > 0 ? round($totalMinutes / $days, 2) : 0;

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_minutes' => $totalMinutes,
                    'total_hours' => $totalHours,
                    'formatted_total_time' => $this->formatMinutes($totalMinutes),
                    'period' => $dateRange === 'all' ? 'All time' : 'Last ' . $dateRange . ' days',
                    'total_tasks_tracked' => $totalTasksTracked,
                    'team_members' => $teamMembers,
                    'avg_daily_minutes' => $avgDailyTime,
                    'avg_daily_formatted' => $this->formatMinutes($avgDailyTime)
                ],
                'time_by_user' => $timeByUser,
                'time_by_project' => $timeByProject,
                'time_by_task' => $timeByTask,
            ]);

        } catch (\Exception $e) {
            \Log::error('Time summary error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load time summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 2. Get Project Duration Report - SIMPLIFIED VERSION
    public function getProjectDurationReport(Request $request)
    {
        try {
            $dateRange = $request->get('range', 'all');
            
            $projects = Project::when($dateRange !== 'all' && is_numeric($dateRange), function($query) use ($dateRange) {
                $startDate = Carbon::now()->subDays($dateRange);
                return $query->where('created_at', '>=', $startDate);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($project) {
                $createdAt = Carbon::parse($project->created_at);
                $updatedAt = Carbon::parse($project->updated_at);
                
                // Calculate project duration in a readable format
                $durationInSeconds = $createdAt->diffInSeconds($updatedAt);
                
                // Format duration to be human readable
                if ($durationInSeconds < 60) {
                    $formattedDuration = $durationInSeconds . ' seconds';
                } elseif ($durationInSeconds < 3600) {
                    $minutes = floor($durationInSeconds / 60);
                    $seconds = $durationInSeconds % 60;
                    $formattedDuration = $minutes . ' min ' . $seconds . ' sec';
                } elseif ($durationInSeconds < 86400) {
                    $hours = floor($durationInSeconds / 3600);
                    $minutes = floor(($durationInSeconds % 3600) / 60);
                    $formattedDuration = $hours . ' hours ' . $minutes . ' min';
                } else {
                    $days = floor($durationInSeconds / 86400);
                    $hours = floor(($durationInSeconds % 86400) / 3600);
                    $formattedDuration = $days . ' days ' . $hours . ' hours';
                }
                
                // Determine project status based on update time
                $daysSinceUpdate = $updatedAt->diffInDays(Carbon::now());
                if ($daysSinceUpdate <= 7) {
                    $activityStatus = 'Recent';
                } elseif ($daysSinceUpdate <= 30) {
                    $activityStatus = 'Active';
                } else {
                    $activityStatus = 'Stale';
                }
                
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'description' => $project->description,
                    'status' => $project->status,
                    'created_at' => $createdAt->format('Y-m-d H:i:s'),
                    'updated_at' => $updatedAt->format('Y-m-d H:i:s'),
                    'duration' => [
                        'total_hours' => $createdAt->diffInHours($updatedAt),
                        'formatted_duration' => $formattedDuration,
                        'days_between' => $createdAt->diffInDays($updatedAt)
                    ],
                    'activity' => [
                        'activity_status' => $activityStatus
                    ]
                ];
            });

            // Summary statistics
            $summary = [
                'total_projects' => $projects->count(),
                'avg_duration_hours' => $projects->avg('duration.total_hours') ?? 0
            ];

            return response()->json([
                'success' => true,
                'projects' => $projects,
                'summary' => $summary,
                'filters' => [
                    'date_range' => $dateRange
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Project duration report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load project duration report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 3. Get Detailed Report
    public function getDetailedReport(Request $request)
    {
        try {
            $dateRange = $request->get('range', '30');
            
            $query = TimeLog::with([
                'task' => function($q) {
                    $q->select('id', 'title', 'project_id')->with('project:id,name');
                },
                'user' => function($q) {
                    $q->select('id', 'name');
                }
            ])
            ->where('is_running', false)
            ->whereNotNull('end_time');

            // Apply date range
            if ($dateRange !== 'all') {
                $startDate = Carbon::now()->subDays($dateRange);
                $query->where('start_time', '>=', $startDate);
            }

            // Apply other filters
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('project_id') && $request->project_id) {
                $query->whereHas('task', function($q) use ($request) {
                    $q->where('project_id', $request->project_id);
                });
            }

            $timeLogs = $query->orderBy('start_time', 'desc')->paginate(25);

            // Transform data
            $timeLogs->getCollection()->transform(function($timeLog) {
                // Calculate duration in minutes
                $durationMinutes = $this->calculateDurationMinutes($timeLog);
                $hours = floor($durationMinutes / 60);
                $minutes = $durationMinutes % 60;
                $formattedDuration = $hours > 0 ? "{$hours}h {$minutes}m" : "{$minutes}m";

                return [
                    'id' => $timeLog->id,
                    'task_name' => $timeLog->task->title ?? 'Unknown Task',
                    'project_name' => $timeLog->task->project->name ?? 'Unknown Project',
                    'user_name' => $timeLog->user->name ?? 'Unknown User',
                    'description' => $timeLog->description,
                    'start_time' => $timeLog->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $timeLog->end_time ? $timeLog->end_time->format('Y-m-d H:i:s') : null,
                    'duration_minutes' => $durationMinutes,
                    'duration_hours' => round($durationMinutes / 60, 2),
                    'formatted_duration' => $formattedDuration,
                    'date' => $timeLog->start_time->format('Y-m-d')
                ];
            });

            // Get filter options
            $users = User::whereIn('role', ['admin', 'user'])->select('id', 'name')->get();
            $projects = Project::select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'time_logs' => $timeLogs,
                'filters' => [
                    'users' => $users,
                    'projects' => $projects,
                    'applied' => $request->all()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Detailed report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Failed to load detailed report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate duration in minutes from a TimeLog
     * Uses duration_minutes if > 0, otherwise calculates from start_time and end_time
     */
    private function calculateDurationMinutes(TimeLog $timeLog)
    {
        // If duration_minutes is set and > 0, use it
        if ($timeLog->duration_minutes > 0) {
            return $timeLog->duration_minutes;
        }
        
        // Otherwise calculate from start_time and end_time
        if ($timeLog->start_time && $timeLog->end_time) {
            $start = Carbon::parse($timeLog->start_time);
            $end = Carbon::parse($timeLog->end_time);
            
            // Calculate difference in minutes
            $durationMinutes = $start->diffInMinutes($end);
            
            // Ensure minimum 1 minute if there's any duration
            if ($durationMinutes > 0) {
                return $durationMinutes;
            }
            
            // If less than 1 minute but there's a difference, return 1 minute
            $durationSeconds = $start->diffInSeconds($end);
            if ($durationSeconds > 0) {
                return 1; // Minimum 1 minute
            }
        }
        
        return 0;
    }

    // Helper method to format minutes
    private function formatMinutes($minutes)
    {
        $minutes = max(0, $minutes);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }
}