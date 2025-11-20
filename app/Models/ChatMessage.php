<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = ['chat_room_id', 'user_id', 'message', 'attachment', 'attachment_name'];

    protected $appends = ['formatted_time'];


    // Chat room relationship
    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    // User relationship (message sender)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('M j, Y g:i A');
    }

    public function getIsOwnMessageAttribute()
    {
        return $this->user_id === auth()->id();
    }

    public function hasAttachment()
    {
        return !is_null($this->attachment);
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    // Scope for unread messages
    public function scopeUnread($query, $userId = null)
    {
        $userId = $userId ?? auth()->id();

        return $query->whereNull('read_at')
                    ->where('user_id', '!=', $userId);
    }

}
