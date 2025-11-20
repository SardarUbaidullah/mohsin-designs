<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany; // Import Laravel's MorphMany


class Projects extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'start_date',
        'due_date',
        'status',
        'created_by',
        'manager_id',
        'budget',
        'client_id',
        'priority',
        'category_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'budget' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================
public function category()
{
    return $this->belongsTo(Category::class);
}


    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Team Members relationship (corrected)
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_team_members', 'project_id', 'user_id')
                    ->withTimestamps();
    }

    // Tasks relationship (corrected model name)
    public function tasks(): HasMany
    {
        return $this->hasMany(Tasks::class, 'project_id');
    }

    // Completed tasks
    public function completedTasks(): HasMany
    {
        return $this->hasMany(Tasks::class, 'project_id')->where('status', 'completed');
    }

    // Pending tasks
    public function pendingTasks(): HasMany
    {
        return $this->hasMany(Tasks::class, 'project_id')->where('status', '!=', 'completed');
    }

    // Comments relationship
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




    public function discussions(): HasMany
    {
        return $this->hasMany(Discussion::class);
    }

     public function files()
    {
        return $this->hasMany(Files::class, 'project_id'); // Explicitly specify foreign key
    }

     public function milestones()
    {
        return $this->hasMany(Milestones::class, 'project_id'); // Explicitly specify foreign key
    }

    public function chatRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class);
    }

    // ==================== SCOPES ====================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOnHold($query)
    {
        return $query->where('status', 'on_hold');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByManager($query, $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())->where('status', '!=', 'completed');
    }

    // ==================== HELPER METHODS ====================

    public function getProgressAttribute(): float
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function hasTeamMember($userId): bool
    {
        return $this->teamMembers()->where('user_id', $userId)->exists();
    }

    public function hasAccess(User $user): bool
    {
        // Super Admin & Admin have access to all projects
        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        // Manager has access if they manage the project or are team member
        if ($user->isManager()) {
            return $this->manager_id === $user->id || $this->hasTeamMember($user->id);
        }

        // Team Member has access if they're in the team
        if ($user->isTeamMember()) {
            return $this->hasTeamMember($user->id);
        }

        // Client has access if it's their project
        if ($user->isClient()) {
            return $this->client_id === $user->client_id;
        }

        return false;
    }



    public function addTeamMember(User $user, string $role = 'team_member'): void
    {
        $this->teamMembers()->syncWithoutDetaching([$user->id => ['role' => $role]]);
    }

    public function removeTeamMember(User $user): void
    {
        $this->teamMembers()->detach($user->id);
    }

    public function getRemainingDaysAttribute(): ?int
    {
        if (!$this->due_date) {
            return null;
        }

        return now()->diffInDays($this->due_date, false);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'active' => 'bg-blue-100 text-blue-800',
            'on_hold' => 'bg-yellow-100 text-yellow-800',
            'pending' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'high' => 'bg-red-100 text-red-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-green-100 text-green-800',
            'urgent' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    // ==================== STATISTICS METHODS ====================

    public function getTasksCountByStatus(): array
    {
        return [
            'total' => $this->tasks()->count(),
            'completed' => $this->tasks()->where('status', 'completed')->count(),
            'in_progress' => $this->tasks()->where('status', 'in_progress')->count(),
            'pending' => $this->tasks()->where('status', 'pending')->count(),
        ];
    }

    public function getCompletionRate(): float
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return ($completedTasks / $totalTasks) * 100;
    }

    // ==================== CLIENT PORTAL METHODS ====================

    public function getClientVisibleData(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'progress' => $this->progress,
            'start_date' => $this->start_date?->format('M d, Y'),
            'due_date' => $this->due_date?->format('M d, Y'),
            'remaining_days' => $this->remaining_days,
            'manager' => $this->manager?->only(['id', 'name', 'email']),
            'team_members_count' => $this->teamMembers->count(),
            'tasks_count' => $this->tasks()->count(),
            'completed_tasks_count' => $this->tasks()->where('status', 'completed')->count(),
        ];
    }



     protected static function boot()
    {
        parent::boot();

        // Automatically create chat room when project is created
        static::created(function ($project) {
            $project->createProjectChatRoom();
        });

        // Automatically update chat room when project is updated
        static::updated(function ($project) {
            $project->syncChatRoomParticipants();
        });
    }

    /**
     * Create project chat room
     */
    public function createProjectChatRoom()
    {
        $chatRoom = ChatRoom::firstOrCreate(
            ['project_id' => $this->id, 'type' => 'project'],
            [
                'name' => $this->name . ' Chat',
                'description' => 'Project discussion group',
                'created_by' => $this->manager_id
            ]
        );

        // Add all project members to chat room
        $allMembers = $this->getAllMembers();
        foreach ($allMembers as $member) {
            $chatRoom->participants()->firstOrCreate(['user_id' => $member->id]);
        }

        return $chatRoom;
    }

    /**
     * Sync chat room participants when project team changes
     */
    public function syncChatRoomParticipants()
    {
        $chatRoom = ChatRoom::where('project_id', $this->id)
            ->where('type', 'project')
            ->first();

        if ($chatRoom) {
            $allMembers = $this->getAllMembers();
            $currentParticipantIds = $chatRoom->participants()->pluck('user_id')->toArray();
            $newMemberIds = $allMembers->pluck('id')->toArray();

            // Add new members
            foreach ($allMembers as $member) {
                if (!in_array($member->id, $currentParticipantIds)) {
                    $chatRoom->participants()->firstOrCreate(['user_id' => $member->id]);
                }
            }

            // Remove members who are no longer in project (except manager)
            foreach ($currentParticipantIds as $participantId) {
                if (!in_array($participantId, $newMemberIds) && $participantId != $this->manager_id) {
                    $chatRoom->participants()->where('user_id', $participantId)->delete();
                }
            }
        }
    }

    /**
     * Get all project members (manager + team members)
     */
    public function getAllMembers()
    {
        $members = collect([$this->manager]);
        if ($this->teamMembers) {
            $members = $members->merge($this->teamMembers);
        }
        return $members->filter()->unique('id');
    }

    // Relationship with chat rooms
    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class, 'project_id')->where('type', 'project');
    }
}
