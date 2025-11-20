<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast; // ✅ correct one

class ChatMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $messageData;

    // ✅ Force auto-increment integer IDs
    public $incrementingId = true;

    public function __construct($messageData)
    {
        $this->messageData = $messageData;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->messageData
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->messageData
        ]);
    }
}
