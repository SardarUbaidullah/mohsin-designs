<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageData;

    public function __construct($messageData)
    {
        $this->messageData = $messageData;
    }

    public function broadcastOn()
    {
        // ✅ USE PresenceChannel for both direct and group chats
        return new PresenceChannel('chat.room.' . $this->messageData['chat_room_id']);
    }

    public function broadcastAs()
    {
        // ✅ USE SAME EVENT NAME FOR BOTH
        return 'message.sent';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->messageData
        ];
    }
}
