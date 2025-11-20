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

    // Get time tracking summary
    public function getTimeSummary(Request $request)
    {
        try {
            $dateRange = $request->get('range', '7'); // 7, 30, 90, 365

            $startDate = match($dateRange) {
                '7' => Carbon::now()->subDays(7),
                '30' => Carbon::now()->subDays(30),
                '90' => Carbon::now()->subDays(90),
                '365' => Carbon::now()->subDays(365),
                default => Carbon::now()->subDays(30)
            };

            // FIX 1: Prevent negative time by using ABS and ensuring positive duration
            $totalTime = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->where('start_time', '>=', $startDate)
                ->sum(DB::raw('ABS(duration_minutes)'));

            // Time by user - FIXED: Use ABS to prevent negative time
            $timeByUser = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->with(['user' => function($query) {
                    $query->select('id', 'name', 'email');
                }])
                ->select('user_id', DB::raw('SUM(ABS(duration_minutes)) as total_minutes'))
                ->where('start_time', '>=', $startDate)
                ->groupBy('user_id')
                ->get()
                ->map(function($item) {
                    $totalMinutes = abs($item->total_minutes ?? 0); // Ensure positive
                    return [
                        'user' => $item->user,
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_time' => $this->formatMinutes($totalMinutes)
                    ];
                });

            // Time by project - FIXED: Use ABS to prevent negative time
            $timeByProject = TimeLog::where('time_logs.is_running', false)
                ->whereNotNull('time_logs.end_time')
                ->join('tasks', 'time_logs.task_id', '=', 'tasks.id')
                ->join('projects', 'tasks.project_id', '=', 'projects.id')
                ->select(
                    'projects.id as project_id',
                    'projects.name as project_name',
                    DB::raw('SUM(ABS(time_logs.duration_minutes)) as total_minutes')
                )
                ->where('time_logs.start_time', '>=', $startDate)
                ->groupBy('projects.id', 'projects.name')
                ->get()
                ->map(function($item) {
                    $totalMinutes = abs($item->total_minutes ?? 0); // Ensure positive
                    return [
                        'project' => [
                            'id' => $item->project_id,
                            'name' => $item->project_name
                        ],
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_time' => $this->formatMinutes($totalMinutes)
                    ];
                });

            // Time by task - FIXED: Use ABS to prevent negative time
            $timeByTask = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->with(['task' => function($query) {
                    $query->select('id', 'title', 'project_id')->with('project:id,name');
                }])
                ->select('task_id', DB::raw('SUM(ABS(duration_minutes)) as total_minutes'))
                ->where('start_time', '>=', $startDate)
                ->groupBy('task_id')
                ->orderBy('total_minutes', 'desc')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    $totalMinutes = abs($item->total_minutes ?? 0); // Ensure positive
                    return [
                        'task' => $item->task,
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_time' => $this->formatMinutes($totalMinutes)
                    ];
                });

            // Daily time trends (last 14 days) - FIXED: Use ABS
            $dailyTrends = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->select(
                    DB::raw('DATE(start_time) as date'),
                    DB::raw('SUM(ABS(duration_minutes)) as total_minutes')
                )
                ->where('start_time', '>=', Carbon::now()->subDays(14))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->map(function($item) {
                    return [
                        'date' => $item->date,
                        'total_minutes' => $item->total_minutes,
                        'total_hours' => round($item->total_minutes / 60, 2)
                    ];
                });

            // Additional stats
            $totalTasksTracked = TimeLog::where('is_running', false)
                ->whereNotNull('end_time')
                ->where('start_time', '>=', $startDate)
                ->distinct('task_id')
                ->count('task_id');

            $teamMembers = $timeByUser->count();
            $avgDailyTime = $totalTime > 0 ? round($totalTime / (int)$dateRange, 2) : 0;

            return response()->json([
                'summary' => [
                    'total_minutes' => $totalTime,
                    'total_hours' => round($totalTime / 60, 2),
                    'formatted_total_time' => $this->formatMinutes($totalTime),
                    'period' => $dateRange . ' days',
                    'total_tasks_tracked' => $totalTasksTracked,
                    'team_members' => $teamMembers,
                    'avg_daily_minutes' => $avgDailyTime,
                    'avg_daily_formatted' => $this->formatMinutes($avgDailyTime)
                ],
                'time_by_user' => $timeByUser,
                'time_by_project' => $timeByProject,
                'time_by_task' => $timeByTask,
                'daily_trends' => $dailyTrends
            ]);

        } catch (\Exception $e) {
            \Log::error('Time summary error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load time summary',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Detailed time report with filters
    public function getDetailedReport(Request $request)
    {
        try {
            $query = TimeLog::with([
                'task' => function($query) {
                    $query->select('id', 'title', 'project_id')->with('project:id,name');
                },
                'user' => function($query) {
                    $query->select('id', 'name', 'email');
                }
            ])
            ->where('is_running', false)
            ->whereNotNull('end_time');

            // Apply filters
            if ($request->has('user_id') && $request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            if ($request->has('project_id') && $request->project_id) {
                $query->whereHas('task', function($q) use ($request) {
                    $q->where('project_id', $request->project_id);
                });
            }

            if ($request->has('start_date') && $request->start_date) {
                $query->where('start_time', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->where('start_time', '<=', $request->end_date . ' 23:59:59');
            }

            // FIX 2: Pagination - ensure proper pagination
            $timeLogs = $query->orderBy('start_time', 'desc')
                ->paginate(25);

            // Transform data for response - FIXED: Use ABS to prevent negative time
            $timeLogs->getCollection()->transform(function($timeLog) {
                $durationMinutes = abs($timeLog->duration_minutes); // Ensure positive duration
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
                    'end_time' => $timeLog->end_time->format('Y-m-d H:i:s'),
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
                'error' => 'Failed to load detailed report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Project-based time report - FIXED: Use ABS
    public function getProjectTimeReport(Request $request)
    {
        try {
            $projectId = $request->get('project_id');

            $query = TimeLog::with([
                'task' => function($query) {
                    $query->select('id', 'title', 'project_id');
                },
                'user' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->whereHas('task', function($q) use ($projectId) {
                if ($projectId) {
                    $q->where('project_id', $projectId);
                }
            })
            ->where('is_running', false)
            ->whereNotNull('end_time');

            // Date range filter
            if ($request->has('start_date') && $request->start_date) {
                $query->where('start_time', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->where('start_time', '<=', $request->end_date . ' 23:59:59');
            }

            $timeLogs = $query->orderBy('start_time', 'desc')->get();

            // Group by task and user for summary - FIXED: Use ABS
            $taskSummary = $timeLogs->groupBy('task_id')->map(function($logs, $taskId) {
                $task = $logs->first()->task;
                $totalMinutes = $logs->sum(function($log) {
                    return abs($log->duration_minutes);
                });

                return [
                    'task' => $task,
                    'total_minutes' => $totalMinutes,
                    'total_hours' => round($totalMinutes / 60, 2),
                    'formatted_time' => $this->formatMinutes($totalMinutes),
                    'time_entries_count' => $logs->count()
                ];
            })->values();

            $userSummary = $timeLogs->groupBy('user_id')->map(function($logs, $userId) {
                $user = $logs->first()->user;
                $totalMinutes = $logs->sum(function($log) {
                    return abs($log->duration_minutes);
                });

                return [
                    'user' => $user,
                    'total_minutes' => $totalMinutes,
                    'total_hours' => round($totalMinutes / 60, 2),
                    'formatted_time' => $this->formatMinutes($totalMinutes),
                    'tasks_worked_on' => $logs->unique('task_id')->count()
                ];
            })->values();

            return response()->json([
                'time_logs' => $timeLogs->take(50)->map(function($log) {
                    $log->duration_minutes = abs($log->duration_minutes);
                    return $log;
                })->values(),
                'task_summary' => $taskSummary,
                'user_summary' => $userSummary,
                'overall_stats' => [
                    'total_time_minutes' => $timeLogs->sum(function($log) {
                        return abs($log->duration_minutes);
                    }),
                    'total_time_hours' => round($timeLogs->sum(function($log) {
                        return abs($log->duration_minutes);
                    }) / 60, 2),
                    'total_tasks' => $timeLogs->unique('task_id')->count(),
                    'total_users' => $timeLogs->unique('user_id')->count(),
                    'total_entries' => $timeLogs->count()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Project time report error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load project report'], 500);
        }
    }

    // User performance report - FIXED: Use ABS
    public function getUserPerformanceReport(Request $request)
    {
        try {
            $dateRange = $request->get('range', '30');
            $startDate = Carbon::now()->subDays($dateRange);

            $users = User::whereIn('role', ['admin', 'user'])
                ->with(['timeLogs' => function($query) use ($startDate) {
                    $query->where('is_running', false)
                          ->whereNotNull('end_time')
                          ->where('start_time', '>=', $startDate)
                          ->with('task.project');
                }])
                ->get()
                ->map(function($user) {
                    $timeLogs = $user->timeLogs;
                    $totalMinutes = $timeLogs->sum(function($log) {
                        return abs($log->duration_minutes);
                    });
                    $totalTasks = $timeLogs->unique('task_id')->count();
                    $totalProjects = $timeLogs->unique(function($log) {
                        return $log->task->project_id ?? null;
                    })->count();

                    $avgTimePerTask = $totalTasks > 0 ? round($totalMinutes / $totalTasks, 2) : 0;

                    return [
                        'user' => $user->only(['id', 'name', 'email']),
                        'total_minutes' => $totalMinutes,
                        'total_hours' => round($totalMinutes / 60, 2),
                        'formatted_total_time' => $this->formatMinutes($totalMinutes),
                        'tasks_worked_on' => $totalTasks,
                        'projects_worked_on' => $totalProjects,
                        'avg_minutes_per_task' => $avgTimePerTask,
                        'time_entries_count' => $timeLogs->count(),
                        'recent_activity' => $timeLogs->sortByDesc('start_time')->take(5)->values()
                    ];
                })
                ->sortByDesc('total_minutes')
                ->values();

            return response()->json([
                'users' => $users,
                'period' => $dateRange . ' days',
                'summary' => [
                    'total_users' => $users->count(),
                    'total_time_minutes' => $users->sum('total_minutes'),
                    'total_time_hours' => round($users->sum('total_minutes') / 60, 2),
                    'avg_time_per_user' => $users->count() > 0 ? round($users->sum('total_minutes') / $users->count(), 2) : 0
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('User performance report error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load user performance report'], 500);
        }
    }

    // FIX 3: Export time report - Working export functionality
    public function exportReport(Request $request)
    {
        try {
            $type = $request->get('type', 'detailed');
            $format = $request->get('format', 'json');

            // Get the data based on type
            switch ($type) {
                case 'summary':
                    $data = $this->getTimeSummary($request)->getData(true);
                    break;
                case 'detailed':
                    $data = $this->getDetailedReport($request)->getData(true);
                    break;
                case 'project':
                    $data = $this->getProjectTimeReport($request)->getData(true);
                    break;
                case 'user_performance':
                    $data = $this->getUserPerformanceReport($request)->getData(true);
                    break;
                default:
                    $data = $this->getTimeSummary($request)->getData(true);
            }

            // Prepare export data
            $exportData = [
                'type' => $type,
                'exported_at' => now()->format('Y-m-d H:i:s'),
                'data' => $data
            ];

            // Return different formats
            if ($format === 'csv') {
                return $this->exportToCsv($exportData, $type);
            } elseif ($format === 'json') {
                return response()->json([
                    'success' => true,
                    'data' => $exportData,
                    'type' => $type,
                    'exported_at' => now()->format('Y-m-d H:i:s')
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $exportData,
                    'type' => $type,
                    'exported_at' => now()->format('Y-m-d H:i:s')
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Export report error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to export report',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Helper method for CSV export
    private function exportToCsv($data, $type)
    {
        $filename = "time_report_{$type}_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data, $type) {
            $file = fopen('php://output', 'w');

            // Add headers based on report type
            if ($type === 'detailed' && isset($data['data']['time_logs']['data'])) {
                fputcsv($file, ['Task', 'User', 'Project', 'Duration', 'Start Time', 'End Time', 'Date']);

                foreach ($data['data']['time_logs']['data'] as $log) {
                    fputcsv($file, [
                        $log['task_name'],
                        $log['user_name'],
                        $log['project_name'],
                        $log['formatted_duration'],
                        $log['start_time'],
                        $log['end_time'],
                        $log['date']
                    ]);
                }
            } elseif ($type === 'summary') {
                fputcsv($file, ['Metric', 'Value']);
                fputcsv($file, ['Total Time', $data['data']['summary']['formatted_total_time']]);
                fputcsv($file, ['Tasks Tracked', $data['data']['summary']['total_tasks_tracked']]);
                fputcsv($file, ['Team Members', $data['data']['summary']['team_members']]);
                fputcsv($file, ['Average Daily', $data['data']['summary']['avg_daily_formatted']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function formatMinutes($minutes)
    {
        // Ensure minutes is positive
        $minutes = abs($minutes);

        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$mins}m";
        }
        return "{$mins}m";
    }
}
