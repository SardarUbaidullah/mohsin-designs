<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'type', 'project_id', 'created_by'];

     // Project relationship (for project chats)
    public function project()
    {
        return $this->belongsTo(Projects::class);
    }

    // Creator relationship
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Messages relationship
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    // Participants relationship
    public function participants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    // Users relationship through participants
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
                    ->withTimestamps()
                    ->withPivot('last_read_at');
    }

    // Latest message relationship
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }

    // Helper methods
    public function isProjectChat()
    {
        return $this->type === 'project';
    }

    public function isDirectChat()
    {
        return $this->type === 'direct';
    }

    public function unreadMessagesCount($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (!$participant) return 0;

        return $this->messages()
            ->where('user_id', '!=', $userId)
            ->where('created_at', '>', $participant->last_read_at ?? $this->created_at)
            ->count();
    }

    public function getOtherUserAttribute()
    {
        if (!$this->isDirectChat()) return null;

        return $this->users()->where('users.id', '!=', auth()->id())->first();
    }

    // Scope for project rooms
    public function scopeProjectRooms($query)
    {
        return $query->where('type', 'project');
    }

    // Scope for direct rooms
    public function scopeDirectRooms($query)
    {
        return $query->where('type', 'direct');
    }

    // In ChatRoom model
public static function getUnreadMessagesCount($userId)
{
    return ChatRoom::whereHas('participants', function($query) use ($userId) {
        $query->where('user_id', $userId);
    })
    ->with(['messages' => function($query) use ($userId) {
        $query->where('user_id', '!=', $userId)
              ->whereNull('read_at');
    }])
    ->get()
    ->sum(function($room) {
        return $room->messages->count();
    });
}
}
