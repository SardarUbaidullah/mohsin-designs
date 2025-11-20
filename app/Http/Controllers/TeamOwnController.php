<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tasks;
use App\Models\Projects;

class TeamOwnController extends Controller
{
    public function index()
    {
        $user = Auth::user();

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
       $tasks = Tasks::where('assigned_to', auth()->id())
        ->with('project')
        ->latest()
        ->take(5)
        ->get();
        // Get upcoming deadlines
    $upcomingDeadlines = Tasks::where('assigned_to', auth()->id())
        ->where('due_date', '>=', now())
        ->where('status', '!=', 'done')
        ->with('project')
        ->orderBy('due_date')
        ->take(5)
        ->get();


        return view('team.index', compact('userStats', 'tasks', 'upcomingDeadlines'));
    }

    public function tasks(Request $request)
{
    $status = $request->get('status', 'all');

    $query = Tasks::where('assigned_to', auth()->id());

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    $tasks = $query->with('project')->latest()->paginate(10);

    // Counts for stats
    $totalCount = Tasks::where('assigned_to', auth()->id())->count();
    $todoCount = Tasks::where('assigned_to', auth()->id())->where('status', 'todo')->count();
    $inProgressCount = Tasks::where('assigned_to', auth()->id())->where('status', 'in_progress')->count();
    $doneCount = Tasks::where('assigned_to', auth()->id())->where('status', 'done')->count();

    return view('team.tasks', compact(
        'tasks',
        'totalCount',
        'todoCount',
        'inProgressCount',
        'doneCount'
    ));
}

    public function projects()
    {
        $projects = Projects::whereHas('tasks', function($query) {
            $query->where('assigned_to', Auth::id());
        })->with(['tasks' => function($query) {
            $query->where('assigned_to', Auth::id());
        }])->get();

        return view('team.projects', compact('projects'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('team.profile', compact('user'));
    }
    // Add this method to TeamOwnController



 public function complete(Request $request, $taskId)
    {
        \Log::info('Complete task request', [
            'task_id' => $taskId,
            'user_id' => auth()->id()
        ]);

        try {
            // Find the task
            $task = Tasks::findOrFail($taskId);

            \Log::info('Task found', [
                'task_id' => $task->id,
                'assigned_to' => $task->assigned_to,
                'current_user' => auth()->id()
            ]);

            // Check if user is authorized to complete this task
            if ($task->assigned_to != auth()->id()) {
                \Log::warning('Unauthorized task completion attempt', [
                    'task_id' => $task->id,
                    'assigned_to' => $task->assigned_to,
                    'current_user' => auth()->id()
                ]);

                return response()->json([
                    'success' => false,
                    'error' => 'You are not authorized to complete this task.'
                ], 403);
            }

            // Update task status
            $task->update([
                'status' => 'done',
                'completed_at' => now()
            ]);

            \Log::info('Task completed successfully', [
                'task_id' => $task->id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully! 🎉',
                'task_id' => $task->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Task completion error', [
                'error' => $e->getMessage(),
                'task_id' => $taskId,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to complete task: ' . $e->getMessage()
            ], 500);
        }
    }

     public function showTask($id)
    {
        $task = Tasks::with(['project', 'project.manager', 'assignedTo'])
                    ->where('assigned_to', auth()->id())
                    ->findOrFail($id);

        return view('team.task-show', compact('task'));
    }

    /**
     * Complete a task from detail page (AJAX)
     */
    public function completeTask(Request $request, $taskId)
    {
        try {
            $task = Tasks::where('assigned_to', auth()->id())->find($taskId);

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'error' => 'Task not found or you are not authorized.'
                ], 404);
            }

            if ($task->status === 'done') {
                return response()->json([
                    'success' => false,
                    'error' => 'Task is already completed.'
                ], 400);
            }

            // Update task status to 'done'
            $task->update([
                'status' => 'done',
                'completed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task marked as done successfully! ✅',
                'task_id' => $task->id,
                'new_status' => 'done'
            ]);

        } catch (\Exception $e) {
            \Log::error('Task completion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update task status
     */
    public function updateStatus(Request $request, $taskId)
    {
        try {
            $request->validate([
                'status' => 'required|in:todo,in_progress,done'
            ]);

            $task = Tasks::where('assigned_to', auth()->id())->find($taskId);

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'error' => 'Task not found or you are not authorized.'
                ], 404);
            }

            $task->update([
                'status' => $request->status,
                'completed_at' => $request->status === 'done' ? now() : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task status updated successfully!',
                'task_id' => $task->id,
                'new_status' => $request->status
            ]);

        } catch (\Exception $e) {
            \Log::error('Task status update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
