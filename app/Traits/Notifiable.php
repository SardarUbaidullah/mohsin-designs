<?php

namespace App\Traits;

use App\Services\NotificationService;

trait Notifiable
{
    public function notify($type, $data)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->sendToUser($this->id, $type, $data);
    }

    public function notifyTaskAssigned($task)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyTaskAssigned($task, $this);
    }

    public function notifyTaskUpdated($task, $updater)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyTaskUpdated($task, $updater);
    }

    // Add other specific notification methods as needed
}
