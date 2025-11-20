<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tasks as Task;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeTrackingController extends Controller
{
    // Start timer for a task
    public function startTimer(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'description' => 'nullable|string|max:500'
        ]);

        $user = auth()->user();

        // Check if user already has a running timer
        $existingTimer = TimeLog::where('user_id', $user->id)
            ->running()
            ->first();

        if ($existingTimer) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a running timer. Please stop it first.'
            ]);
        }

        // Create new timer
        $timeLog = TimeLog::create([
            'task_id' => $request->task_id,
            'user_id' => $user->id,
            'description' => $request->description,
            'start_time' => now(),
            'is_running' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Timer started successfully',
            'time_log' => $timeLog
        ]);
    }

    // Stop timer
    public function stopTimer(Request $request)
    {
        $request->validate([
            'time_log_id' => 'required|exists:time_logs,id'
        ]);

        $timeLog = TimeLog::where('user_id', auth()->id())
            ->where('id', $request->time_log_id)
            ->running()
            ->firstOrFail();

        $endTime = now();
        $duration = $endTime->diffInMinutes($timeLog->start_time);

        $timeLog->update([
            'end_time' => $endTime,
            'duration_minutes' => $duration,
            'is_running' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Timer stopped successfully',
            'duration' => $timeLog->formatted_duration,
            'time_log' => $timeLog
        ]);
    }

    // Get current running timer for user
    public function getRunningTimer()
    {
        $timer = TimeLog::with('task')
            ->where('user_id', auth()->id())
            ->running()
            ->first();

        return response()->json([
            'has_running_timer' => !is_null($timer),
            'timer' => $timer
        ]);
    }

    // Get time logs for a task
    public function getTaskTimeLogs($taskId)
    {
        $timeLogs = TimeLog::with('user')
            ->where('task_id', $taskId)
            ->completed()
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'time_logs' => $timeLogs,
            'total_time' => $timeLogs->sum('duration_minutes')
        ]);
    }

    // Manual time entry
    public function manualEntry(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'description' => 'required|string|max:500',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1'
        ]);

        $timeLog = TimeLog::create([
            'task_id' => $request->task_id,
            'user_id' => auth()->id(),
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'is_running' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Time entry added successfully',
            'time_log' => $timeLog
        ]);
    }

    // Delete time log
    public function deleteTimeLog($id)
    {
        $timeLog = TimeLog::where('user_id', auth()->id())
            ->findOrFail($id);

        $timeLog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Time entry deleted successfully'
        ]);
    }
}
