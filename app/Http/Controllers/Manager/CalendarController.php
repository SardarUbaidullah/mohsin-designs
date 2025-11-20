<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Projects as Project;
use App\Models\Tasks as Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $projects = $this->getProjectsBasedOnRole($user);

        Log::info('Calendar page accessed', [
            'user_id' => $user->id,
            'role' => $user->role,
            'projects_count' => $projects->count()
        ]);

        return view('manager.calendar.index', compact('projects'));
    }

    public function getEvents(Request $request)
    {
        try {
            $user = auth()->user();

            Log::info('Calendar events requested', [
                'user_id' => $user->id,
                'role' => $user->role,
                'request_params' => $request->all()
            ]);

            if ($request->has('upcoming')) {
                $start = now()->startOfDay();
                $end = now()->addDays(7)->endOfDay();
            } else {
                $start = Carbon::parse($request->start ?? now()->startOfMonth())->startOfDay();
                $end = Carbon::parse($request->end ?? now()->endOfMonth())->endOfDay();
            }

            Log::info('Date range for events', [
                'start' => $start,
                'end' => $end,
                'start_formatted' => $start->format('Y-m-d H:i:s'),
                'end_formatted' => $end->format('Y-m-d H:i:s')
            ]);

            $tasks = $this->getTasksBasedOnRole($user, $start, $end);

            $events = [];

            foreach ($tasks as $task) {
                $events[] = $this->formatTaskEvent($task);
            }

            if (in_array($user->role, ['super_admin', 'admin'])) {
                $projects = $this->getProjectDeadlinesBasedOnRole($user, $start, $end);

                foreach ($projects as $project) {
                    $events[] = $this->formatProjectEvent($project);
                }
            }

            Log::info('Final events prepared', [
                'user_id' => $user->id,
                'total_events' => count($events),
                'task_events' => $tasks->count(),
                'task_statuses' => $tasks->pluck('status')->toArray(),
                'date_range' => $start->format('Y-m-d') . ' to ' . $end->format('Y-m-d')
            ]);

            return response()->json($events);

        } catch (\Exception $e) {
            Log::error('Calendar events error', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to load calendar events'], 500);
        }
    }

    private function getProjectsBasedOnRole($user)
    {
        $query = Project::query();

        switch ($user->role) {
            case 'super_admin':
                return $query->get();

            case 'admin':
                return $query->where(function($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->orWhereHas('teamMembers', function($query) use ($user) {
                          $query->where('user_id', $user->id);
                      });
                })->get();

            case 'user':
                return $query->whereHas('teamMembers', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->get();

            default:
                return collect();
        }
    }

    private function getTasksBasedOnRole($user, $start, $end)
    {
        $query = Task::with(['project', 'user']);

        Log::info('Base tasks query - getting ALL tasks including NULL due dates');

        switch ($user->role) {
            case 'super_admin':
                $tasks = $query->get();
                break;

            case 'admin':
                $tasks = $query->whereHas('project', function($query) use ($user) {
                    $query->where('manager_id', $user->id)
                          ->orWhereHas('teamMembers', function($q) use ($user) {
                              $q->where('user_id', $user->id);
                          });
                })->get();
                break;

            case 'user':
                $tasks = $query->where('assigned_to', $user->id)->get();
                break;

            default:
                $tasks = collect();
        }

        $filteredTasks = $tasks->filter(function($task) use ($start, $end) {
            if (!$task->due_date) {
                return true;
            }

            $dueDate = Carbon::parse($task->due_date);
            return $dueDate->between($start, $end);
        });

        Log::info('Tasks retrieved from database', [
            'total_tasks' => $tasks->count(),
            'filtered_tasks' => $filteredTasks->count(),
            'tasks_with_null_due_date' => $tasks->where('due_date', null)->count(),
            'tasks_details' => $filteredTasks->map(function($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'due_date' => $task->due_date,
                    'status' => $task->status,
                    'priority' => $task->priority,
                    'assigned_to' => $task->assigned_to
                ];
            })->toArray(),
            'status_counts' => $filteredTasks->groupBy('status')->map->count()
        ]);

        return $filteredTasks;
    }

    private function getProjectDeadlinesBasedOnRole($user, $start, $end)
    {
        $query = Project::whereNotNull('due_date')
                       ->whereBetween('due_date', [$start, $end]);

        switch ($user->role) {
            case 'super_admin':
                return $query->get();

            case 'admin':
                return $query->where('manager_id', $user->id)->get();

            default:
                return collect();
        }
    }

    private function formatTaskEvent($task)
    {
        $isCompleted = $task->status === 'done';

        if (!$task->due_date) {
            $dueDate = now()->format('Y-m-d');
            $isNoDateTask = true;
        } else {
            $dueDate = $task->due_date->format('Y-m-d');
            $isNoDateTask = false;
        }
           // FIX: Get the assigned user's name by their ID and show "YOU" if it's the current user
    $assignedUserName = 'Unassigned';
    if ($task->assigned_to) {
        // Load the assigned user relationship if not already loaded
        if (!$task->relationLoaded('assignedUser')) {
            $task->load('assignedUser');
        }

        // Check if assignedUser relationship exists and has name
        if ($task->assignedUser) {
            // Check if the assigned user is the current authenticated user
            if ($task->assigned_to == auth()->id()) {
                $assignedUserName = 'YOU';
            } else {
                $assignedUserName = $task->assignedUser->name;
            }
        } else {
            // Fallback: Try to find user by ID
            $assignedUser = \App\Models\User::find($task->assigned_to);
            if ($assignedUser) {
                // Check if the assigned user is the current authenticated user
                if ($task->assigned_to == auth()->id()) {
                    $assignedUserName = 'YOU';
                } else {
                    $assignedUserName = $assignedUser->name;
                }
            }
        }
    }


        Log::debug('Formatting task event', [
            'task_id' => $task->id,
            'title' => $task->title,
            'status' => $task->status,
            'due_date' => $task->due_date,
            'formatted_due_date' => $dueDate,
            'is_completed' => $isCompleted,
            'is_no_date_task' => $isNoDateTask
        ]);

        return [
            'id' => $task->id,
            'title' => $task->title,
            'start' => $dueDate,
            'end' => $dueDate,
            'priority' => $task->priority,
            'status' => $task->status,
            'is_completed' => $isCompleted,
            'has_due_date' => !$isNoDateTask,
            'extendedProps' => [
                'project' => $task->project->name,
                'project_id' => $task->project->id,
                'priority' => $task->priority,
                'status' => $task->status,
                'assigned_to' => $assignedUserName,
                'description' =>  $task->description,
                'type' => 'task',
                'is_completed' => $isCompleted,
                'has_due_date' => !$isNoDateTask,
                'original_due_date' => $task->due_date ? $task->due_date->format('Y-m-d') : null
            ]
        ];
    }

    private function formatProjectEvent($project)
    {
         $manager = \App\Models\User::find($project->manager_id);
    $assignedName = $manager ? ($project->manager_id == auth()->id() ? 'YOU' : $manager->name) : 'Unassigned';
        return [
            'id' => 'project-' . $project->id,
            'title' => $project->name,
            'start' => $project->due_date->format('Y-m-d'),
            'end' => $project->due_date->format('Y-m-d'),
            'priority' => 'medium',
            'status' => 'project',
            'is_completed' => false,
            'has_due_date' => true,
            'extendedProps' => [
                'project' => $project->name,
                'project_id' => $project->id,
                'type' => 'project_deadline',
                'description' => $project->description,
                'is_completed' => false,
                'has_due_date' => true,
                'assigned_to' => $assignedName
            ]
        ];
    }

    public function getUpcomingTasks()
    {
        try {
            $user = auth()->user();
            $start = now()->startOfDay();
            $end = now()->addDays(7)->endOfDay();

            $tasks = $this->getTasksBasedOnRole($user, $start, $end)
                         ->take(5)
                         ->map(function($task) {
                             return [
                                 'id' => $task->id,
                                 'title' => $task->title,
                                 'due_date' => $task->due_date ? $task->due_date->format('M d') : 'No date',
                                 'project' => $task->project->name,
                                 'priority' => $task->priority,
                                 'status' => $task->status,
                                 'is_completed' => $task->status === 'done',
                                 'has_due_date' => !is_null($task->due_date)
                             ];
                         });

            Log::info('Upcoming tasks prepared', [
                'user_id' => $user->id,
                'upcoming_count' => $tasks->count(),
                'upcoming_tasks' => $tasks->toArray()
            ]);

            return response()->json($tasks);

        } catch (\Exception $e) {
            Log::error('Upcoming tasks error', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to load upcoming tasks'], 500);
        }
    }
}
