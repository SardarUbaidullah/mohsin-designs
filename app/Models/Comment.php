<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'commentable_type',
        'commentable_id',
        'type',
        'is_internal'
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ==================== RELATIONSHIPS ====================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }
    // ==================== SCOPES ====================

    public function scopePublic($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeVisibleToClient($query)
    {
        return $query->where('is_internal', false);
    }

    public function scopeFromUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProject($query, $projectId)
    {
        return $query->whereHasMorph('commentable', [Project::class], function($q) use ($projectId) {
            $q->where('id', $projectId);
        });
    }

    public function scopeForTask($query, $taskId)
    {
        return $query->whereHasMorph('commentable', [Task::class], function($q) use ($taskId) {
            $q->where('id', $taskId);
        });
    }

    // ==================== HELPER METHODS ====================

    public function isInternal(): bool
    {
        return $this->is_internal;
    }

    public function isPublic(): bool
    {
        return !$this->is_internal;
    }

    public function getCommentableTypeNameAttribute(): string
    {
        return class_basename($this->commentable_type);
    }

    public function isVisibleToClient(): bool
    {
        return !$this->is_internal;
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at->format('M j, Y g:i A');
    }

    public function getShortFormattedTimeAttribute(): string
    {
        return $this->created_at->format('M j, g:i A');
    }

    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function isFromClient(): bool
    {
        return $this->user->role === 'client';
    }

    public function isFromTeamMember(): bool
    {
        return in_array($this->user->role, ['user', 'manager', 'admin', 'super_admin']);
    }

    public function canBeEditedBy(User $user): bool
    {
        // Users can edit their own comments
        if ($this->user_id === $user->id) {
            return true;
        }

        // Admins and Super Admins can edit any comment
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return true;
        }

        // Managers can edit comments in their projects
        if ($user->isManager() && $this->commentable_type === Project::class) {
            return $this->commentable->manager_id === $user->id;
        }

        return false;
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->canBeEditedBy($user);
    }

    public function getExcerptAttribute($length = 100): string
    {
        if (strlen($this->content) <= $length) {
            return $this->content;
        }

        return substr($this->content, 0, $length) . '...';
    }

    public function hasAttachments(): bool
    {
        // You can extend this if you add file attachments to comments
        return false;
    }

    public function getCommentableProject(): ?Project
    {
        if ($this->commentable_type === Project::class) {
            return $this->commentable;
        }

        if ($this->commentable_type === Task::class && $this->commentable) {
            return $this->commentable->project;
        }

        return null;
    }

    public function getClientVisibilityAttribute(): string
    {
        return $this->is_internal ? 'Internal (Team Only)' : 'Public (Visible to Client)';
    }

    // ==================== EVENT HANDLERS ====================

    protected static function boot()
    {
        parent::boot();

        // Auto-set type based on commentable type
        static::creating(function ($comment) {
            if (empty($comment->type)) {
                $comment->type = strtolower(class_basename($comment->commentable_type));
            }
        });

        // Ensure client comments are always public
        static::saving(function ($comment) {
            if ($comment->user && $comment->user->isClient()) {
                $comment->is_internal = false;
            }
        });
    }

      // Authorization methods


  

}
