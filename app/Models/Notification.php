<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'notifiable_id', 'notifiable_type', 'data', 'read_at'];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // Scopes
     public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('notifiable_id', $userId)
                    ->where('notifiable_type', 'App\Models\User');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread(Builder $query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRecent($query, $limit = 15)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
        return $this;
    }

    public function isRead()
    {
        return !is_null($this->read_at);
    }

    // Dynamic attributes from data
    public function getTitleAttribute()
    {
        return $this->data['title'] ?? 'New Notification';
    }

    public function getMessageAttribute()
    {
        return $this->data['message'] ?? '';
    }

    public function getActionUrlAttribute()
    {
        return $this->data['action_url'] ?? '#';
    }

    public function getIconAttribute()
    {
        $icons = [
            'chat_message' => 'fas fa-comments',
            'task_assigned' => 'fas fa-tasks',
            'task_updated' => 'fas fa-edit',
            'task_completed' => 'fas fa-check-circle',
            'project_created' => 'fas fa-project-diagram',
            'project_updated' => 'fas fa-sync',
            'file_uploaded' => 'fas fa-file-upload',
            'new_comment' => 'fas fa-comment',
            'mention' => 'fas fa-at',
            'approval_required' => 'fas fa-check-double',
            'deadline_approaching' => 'fas fa-clock',
            'overdue' => 'fas fa-exclamation-triangle',
            'team_member_added' => 'fas fa-user-plus',
            'status_changed' => 'fas fa-exchange-alt',
        ];

        return $this->data['icon'] ?? $icons[$this->type] ?? 'fas fa-bell';
    }

    public function getColorAttribute()
    {
        $colors = [
            'chat_message' => 'green',
            'task_assigned' => 'blue',
            'task_updated' => 'yellow',
            'task_completed' => 'green',
            'project_created' => 'purple',
            'project_updated' => 'indigo',
            'file_uploaded' => 'pink',
            'new_comment' => 'teal',
            'mention' => 'orange',
            'approval_required' => 'red',
            'deadline_approaching' => 'yellow',
            'overdue' => 'red',
            'team_member_added' => 'green',
            'status_changed' => 'blue',
        ];

        return $this->data['color'] ?? $colors[$this->type] ?? 'blue';
    }

    public function getBadgeColorAttribute()
    {
        $colors = [
            'green' => 'bg-green-100 text-green-800',
            'blue' => 'bg-blue-100 text-blue-800',
            'yellow' => 'bg-yellow-100 text-yellow-800',
            'red' => 'bg-red-100 text-red-800',
            'purple' => 'bg-purple-100 text-purple-800',
            'indigo' => 'bg-indigo-100 text-indigo-800',
            'pink' => 'bg-pink-100 text-pink-800',
            'teal' => 'bg-teal-100 text-teal-800',
            'orange' => 'bg-orange-100 text-orange-800',
        ];

        return $colors[$this->color] ?? 'bg-gray-100 text-gray-800';
    }
}
