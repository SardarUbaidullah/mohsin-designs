<?php

namespace App\Traits;

use App\Providers\NotificationService;

trait HasNotifications
{
    /**
     * Send a custom notification to this user
     */
    public function sendNotification($type, $data)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->sendToUser($this->id, $type, $data);
    }

    /**
     * Notify user about task assignment
     */
    public function notifyTaskAssigned($task)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyTaskAssigned($task, $this);
    }

    /**
     * Notify user about task update
     */
    public function notifyTaskUpdated($task, $updater)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyTaskUpdated($task, $updater);
    }

    /**
     * Notify about task completion
     */
    public function notifyTaskCompleted($task)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyTaskCompleted($task, $this);
    }

    /**
     * Notify about project creation
     */
    public function notifyProjectCreated($project)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyProjectCreated($project, $this);
    }

    /**
     * Notify about file upload
     */
    public function notifyFileUploaded($file, $project)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyFileUploaded($file, $this, $project);
    }

    /**
     * Notify about new comment
     */
    public function notifyNewComment($comment, $commentedOn)
    {
        $notificationService = app(NotificationService::class);
        return $notificationService->notifyNewComment($comment, $commentedOn, $this);
    }

    /**
     * Get user's unread notifications count
     */
    public function unreadNotificationsCount()
    {
        return \App\Models\Notification::forUser($this->id)->unread()->count();
    }

    /**
     * Get user's recent notifications
     */
    public function recentNotifications($limit = 10)
    {
        return \App\Models\Notification::forUser($this->id)->recent($limit)->get();
    }
}
