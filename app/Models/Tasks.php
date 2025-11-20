<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'start_date',
        'due_date',
        'milestone_id'
    ];

    protected $dates = [
        'due_date',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
    'due_date' => 'datetime',

];



    public function milestone()
    {
        return $this->belongsTo(Milestones::class);
    }


    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

public function subtasks()
{
    return $this->hasMany(task_subtasks::class, 'task_id');
}


 public function comments()
{
    return $this->morphMany(Comment::class, 'commentable');
}

public function publicComments()
{
    return $this->morphMany(Comment::class, 'commentable')->public();
}

public function internalComments()
{
    return $this->morphMany(Comment::class, 'commentable')->internal();
}

public function files()
    {
        return $this->hasMany(File::class);
    }


public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        public function assignedTasks()
{
    return $this->belongsTo(User::class, 'manager_id');
}




    // Assignee relationship


    // Creator relationship


    // Helper methods
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'gray',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'done' => 'green',
            'in_progress' => 'blue',
            'todo' => 'gray',
            default => 'gray',
        };
    }




     protected static function boot()
    {
        parent::boot();

        // Automatically add user to project chat when task is assigned
        static::created(function ($task) {
            $task->addUserToProjectChat();
        });

        // Also handle when task is updated (assigned_to changes)
        static::updated(function ($task) {
            $task->addUserToProjectChat();
        });
    }

    /**
     * Add assigned user to project chat room
     */
    public function addUserToProjectChat()
    {
        // Check if task has an assigned user and project
        if ($this->assigned_to && $this->project) {
            $project = $this->project;
            $assignedUser = $this->assignedTo;

            // Get or create project chat room
            $chatRoom = ChatRoom::firstOrCreate(
                ['project_id' => $project->id, 'type' => 'project'],
                [
                    'name' => $project->name . ' Chat',
                    'description' => 'Project discussion group',
                    'created_by' => $project->manager_id
                ]
            );

            // Add assigned user to chat room if not already added
            $chatRoom->participants()->firstOrCreate(['user_id' => $assignedUser->id]);

            // Also ensure manager is in the chat room
            if ($project->manager) {
                $chatRoom->participants()->firstOrCreate(['user_id' => $project->manager->id]);
            }

            \Log::info("User added to project chat", [
                'user_id' => $assignedUser->id,
                'user_name' => $assignedUser->name,
                'project_id' => $project->id,
                'project_name' => $project->name,
                'chat_room_id' => $chatRoom->id
            ]);
        }
    }

    /**
     * Relationship with assigned user
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relationship with project
     */
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }



    // Relationship with assigned user
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Alias for assignedUser (if you want to use both)

     public function timeLogs()
    {
        return $this->hasMany(TimeLog::class, 'task_id'); // Explicitly specify foreign key
    }





    // Get total time spent
    public function getTotalTimeSpentAttribute()
    {
        return $this->timeLogs()->where('is_running', false)->sum('duration_minutes');
    }

    public function getFormattedTotalTimeAttribute()
    {
        $totalMinutes = $this->total_time_spent;
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }

    // Get active timer
    public function getActiveTimerAttribute()
    {
        return $this->timeLogs()->where('is_running', true)->first();
    }
}
