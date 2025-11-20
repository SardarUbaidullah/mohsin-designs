<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;

    protected $fillable = ['chat_room_id', 'user_id', 'last_read_at'];

    public function room()
    {
        return $this->belongsTo(ChatRoom::class, 'chat_room_id');
    }

    // User relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update(['last_read_at' => now()]);

        // Also mark all messages in this room as read
        $this->room->messages()
            ->where('user_id', '!=', $this->user_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function unreadCount()
    {
        return $this->room->messages()
            ->where('user_id', '!=', $this->user_id)
            ->where('created_at', '>', $this->last_read_at ?? $this->created_at)
            ->count();
    }
}
