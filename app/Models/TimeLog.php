<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'description',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_running',
        'billable',
        'hourly_rate'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'billable' => 'boolean',
        'hourly_rate' => 'decimal:2'
    ];

    // Relationships
    public function task()
    {
        return $this->belongsTo(Tasks::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeRunning($query)
    {
        return $query->where('is_running', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_running', false)->whereNotNull('end_time');
    }

    public function scopeBillable($query)
    {
        return $query->where('billable', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('start_time', [now()->startOfMonth(), now()->endOfMonth()]);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->whereHas('task', function($q) use ($projectId) {
            $q->where('project_id', $projectId);
        });
    }

    // Accessors
    public function getDurationHoursAttribute()
    {
        return round($this->duration_minutes / 60, 2);
    }

    public function getFormattedDurationAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    public function getBillableAmountAttribute()
    {
        if (!$this->billable || !$this->hourly_rate) {
            return 0;
        }
        return round(($this->duration_minutes / 60) * $this->hourly_rate, 2);
    }

    public function getEfficiencyScoreAttribute()
    {
        // Calculate efficiency based on task complexity and time taken
        // This is a simplified version - you can enhance it based on your business logic
        $expectedTime = $this->task->estimated_hours * 60 ?? 120; // Default 2 hours

        if ($expectedTime <= 0) return 100;

        $efficiency = ($expectedTime / max($this->duration_minutes, 1)) * 100;
        return min(100, round($efficiency));
    }

    // Methods
    public static function getTotalBillableAmount($userId = null, $startDate = null, $endDate = null)
    {
        $query = self::billable()->completed();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->get()->sum('billable_amount');
    }

    public static function getUserProductivityScore($userId, $days = 30)
    {
        $startDate = now()->subDays($days);

        $userLogs = self::completed()
            ->forUser($userId)
            ->where('start_time', '>=', $startDate)
            ->with('task')
            ->get();

        if ($userLogs->isEmpty()) return 0;

        $totalEfficiency = $userLogs->sum('efficiency_score');
        $completedTasks = $userLogs->unique('task_id')->count();

        return round(($totalEfficiency / $userLogs->count() + $completedTasks) / 2);
    }

    public static function getProjectTimeReport($projectId, $startDate = null, $endDate = null)
    {
        $query = self::completed()->forProject($projectId);

        if ($startDate && $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        return $query->get()
            ->groupBy('user_id')
            ->map(function($userLogs, $userId) {
                $user = $userLogs->first()->user;
                return [
                    'user' => $user,
                    'total_minutes' => $userLogs->sum('duration_minutes'),
                    'total_hours' => round($userLogs->sum('duration_minutes') / 60, 2),
                    'tasks_worked_on' => $userLogs->unique('task_id')->count(),
                    'billable_amount' => $userLogs->sum('billable_amount'),
                    'efficiency_score' => round($userLogs->avg('efficiency_score'))
                ];
            })
            ->values();
    }

    // Auto-stop timer when task is marked done
    public static function stopRunningTimersForTask($taskId)
    {
        return self::where('task_id', $taskId)
            ->running()
            ->update([
                'end_time' => now(),
                'duration_minutes' => DB::raw('TIMESTAMPDIFF(MINUTE, start_time, NOW())'),
                'is_running' => false
            ]);
    }

    // Get weekly summary for a user
    public static function getWeeklySummary($userId)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return self::completed()
            ->forUser($userId)
            ->whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->selectRaw('
                DAYNAME(start_time) as day,
                SUM(duration_minutes) as total_minutes,
                COUNT(*) as entries,
                SUM(billable_amount) as billable_total
            ')
            ->groupBy(DB::raw('DAYNAME(start_time)'))
            ->get();
    }
}
