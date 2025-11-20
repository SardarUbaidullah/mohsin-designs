<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.room.{roomId}', function ($user, $roomId) {
    return \App\Models\ChatRoom::where('id', $roomId)
        ->whereHas('participants', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->exists();
});

// Add user channel for notifications
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
