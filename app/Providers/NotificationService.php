<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str; // Add this import

class NotificationService extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService($app);
        });
    }

    public function boot(): void
    {
        //
    }

    // Send notification to single user
    public function sendToUser($userId, $type, $data)
    {
        try {
            Log::info('Attempting to send notification to user:', [
                'user_id' => $userId,
                'type' => $type,
                'data_title' => $data['title'] ?? 'No title'
            ]);

            $notification = Notification::create([
                'type' => $type,
                'notifiable_id' => $userId,
                'notifiable_type' => 'App\Models\User',
                'data' => $data,
            ]);

            // Load the notification with user relationship
            $notification->load('user');

            Log::info('Notification created successfully:', [
                'notification_id' => $notification->id,
                'user_id' => $userId
            ]);

            // Broadcast real-time notification
            broadcast(new NotificationSent($notification->toArray()));

            Log::info('Notification broadcasted');

            return $notification;
        } catch (\Exception $e) {
            Log::error('Notification send error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'type' => $type
            ]);
            return null;
        }
    }

    // Send notification to multiple users
    public function sendToUsers($userIds, $type, $data)
    {
        $notifications = [];

        foreach ($userIds as $userId) {
            $notifications[] = $this->sendToUser($userId, $type, $data);
        }

        return $notifications;
    }

    // Send to all admins
    public function sendToAdmins($type, $data)
    {
        $adminIds = User::whereIn('role', ['admin', 'super_admin'])->pluck('id');
        return $this->sendToUsers($adminIds, $type, $data);
    }

    // Send to all users except specified
    public function sendToAllExcept($exceptUserId, $type, $data)
    {
        $userIds = User::where('id', '!=', $exceptUserId)->pluck('id');
        return $this->sendToUsers($userIds, $type, $data);
    }

    // Send to team members of a project
    public function sendToTeamMembers($project, $type, $data)
    {
        $teamMemberIds = $project->teamMembers->pluck('id');
        return $this->sendToUsers($teamMemberIds, $type, $data);
    }

    // Send to project manager
    public function sendToProjectManager($project, $type, $data)
    {
        if ($project->manager) {
            return $this->sendToUser($project->manager->id, $type, $data);
        }
        return null;
    }

    // ==================== SPECIFIC NOTIFICATION METHODS ====================

    // 1. CHAT NOTIFICATIONS
       // ==================== CHAT NOTIFICATION METHODS ====================

/**
 * Notify new project chat message
 */
/**
 * Notify new project chat message - SUPER ADMIN INCLUDED
 */
public function notifyProjectChatMessage($project, $sender, $message, $chatRoom = null)
{
    try {
        // ✅ FIX: FORCE LOAD RELATIONSHIPS
        $project->load(['teamMembers', 'manager']);

        $messagePreview = Str::limit($message, 100);

        $data = [
            'title' => "New Message in {$project->name}",
            'message' => "{$sender->name}: {$messagePreview}",
            'action_url' => $this->getChatActionUrl($chatRoom, null, $project),
            'icon' => 'fas fa-comments',
            'color' => 'blue',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'sender_name' => $sender->name,
            'sender_id' => $sender->id,
            'message_preview' => $messagePreview,
            'has_attachment' => false,
            'chat_room_id' => $chatRoom ? $chatRoom->id : null,
        ];

        Log::info('🔍 DEBUG: Project relationships loaded', [
            'team_members_count' => $project->teamMembers ? $project->teamMembers->count() : 0,
            'manager_exists' => !is_null($project->manager),
            'project_id' => $project->id
        ]);

        // ✅ GET ALL PROJECT MEMBERS (Team Members + Manager + Super Admins)
        $usersToNotify = collect();

        // 1. Add team members
        if ($project->teamMembers && $project->teamMembers->isNotEmpty()) {
            $usersToNotify = $usersToNotify->merge($project->teamMembers);
            Log::info('🔍 Team members found:', [
                'count' => $project->teamMembers->count(),
                'members' => $project->teamMembers->pluck('name', 'id')->toArray()
            ]);
        } else {
            Log::warning('🔍 No team members found for project');
        }

        // 2. Add project manager if exists and not already in list
        if ($project->manager) {
            $managerExists = $usersToNotify->contains('id', $project->manager->id);
            if (!$managerExists) {
                $usersToNotify->push($project->manager);
                Log::info('🔍 Manager added:', [
                    'manager_id' => $project->manager->id,
                    'manager_name' => $project->manager->name
                ]);
            }
        } else {
            Log::warning('🔍 No manager found for project');
        }

        // 3. ✅ ADD SUPER ADMINS TO NOTIFICATION LIST
        $superAdmins = User::where('role', 'super_admin')->get();
        if ($superAdmins->isNotEmpty()) {
            Log::info('🔍 Super admins found:', [
                'count' => $superAdmins->count(),
                'admins' => $superAdmins->pluck('name', 'id')->toArray()
            ]);

            foreach ($superAdmins as $admin) {
                $adminExists = $usersToNotify->contains('id', $admin->id);
                if (!$adminExists) {
                    $usersToNotify->push($admin);
                    Log::info("🔍 Super admin added: {$admin->name} (ID: {$admin->id})");
                }
            }
        }

        // 4. Remove sender from list
        $finalUsers = $usersToNotify->where('id', '!=', $sender->id)->unique('id');

        Log::info('🔍 DEBUG: Final notification recipients', [
            'total_users' => $finalUsers->count(),
            'user_ids' => $finalUsers->pluck('id')->toArray(),
            'user_names' => $finalUsers->pluck('name')->toArray(),
            'includes_super_admins' => $finalUsers->where('role', 'super_admin')->count()
        ]);

        // Send notifications
        $sentCount = 0;
        foreach ($finalUsers as $user) {
            $result = $this->sendToUser($user->id, 'project_chat_message', $data);
            if ($result) {
                $sentCount++;
                Log::info("✅ Notification sent to {$user->name} (ID: {$user->id}, Role: {$user->role})");
            } else {
                Log::error("❌ Failed to send notification to {$user->name} (ID: {$user->id})");
            }
        }

        Log::info("🎯 Project notifications completed: {$sentCount}/{$finalUsers->count()} sent");

        return $sentCount > 0;

    } catch (\Exception $e) {
        Log::error('❌ Project chat notification error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Notify new direct message
 */
   public function notifyDirectMessage($recipient, $sender, $message, $chatRoom = null)
    {
        try {
            $messagePreview = Str::limit($message, 100);

            $data = [
                'title' => "New Message from {$sender->name}",
                'message' => $messagePreview,
                'action_url' => $this->getChatActionUrl($chatRoom, $sender),
                'icon' => 'fas fa-envelope',
                'color' => 'green',
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'message_preview' => $messagePreview,
                'has_attachment' => false,
                'chat_room_id' => $chatRoom ? $chatRoom->id : null,
            ];

            Log::info('Sending direct message notification', [
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'chat_room_id' => $chatRoom ? $chatRoom->id : null
            ]);

            return $this->sendToUser($recipient->id, 'direct_message', $data);

        } catch (\Exception $e) {
            Log::error('Direct message notification error: ' . $e->getMessage());
            return false;
        }
    }



    private function getChatActionUrl($chatRoom = null, $sender = null, $project = null)
    {
        if ($chatRoom) {
            if ($chatRoom->type === 'project' && $chatRoom->project) {
                return route('manager.chat.project', $chatRoom->project->id);
            } elseif ($chatRoom->type === 'direct') {
                return route('manager.chat.direct', $sender ? $sender->id : '#');
            }
        }

        if ($project) {
            return route('manager.chat.project', $project->id);
        }

        if ($sender) {
            return route('manager.chat.direct', $sender->id);
        }

        return '#';
    }

/**
 * Notify mentioned users in chat
 */
public function notifyMentionedInChat($mentionedUser, $mentionedBy, $message, $chatRoom, $context)
{
    $messagePreview = \Str::limit($message, 100);

    $data = [
        'title' => 'You Were Mentioned',
        'message' => "{$mentionedBy->name} mentioned you in {$context}: {$messagePreview}",
        'action_url' => $this->getChatRoomUrl($chatRoom),
        'icon' => 'fas fa-at',
        'color' => 'pink',
        'mentioned_by' => $mentionedBy->name,
        'mentioned_by_id' => $mentionedBy->id,
        'message_preview' => $messagePreview,
        'context' => $context,
        'chat_room_id' => $chatRoom->id,
    ];

    return $this->sendToUser($mentionedUser->id, 'mentioned_in_chat', $data);
}

/**
 * Get chat room URL for notifications
 */
private function getChatRoomUrl($chatRoom)
{
    if ($chatRoom->type === 'project' && $chatRoom->project) {
        return route('manager.chat.project', $chatRoom->project);
    } elseif ($chatRoom->type === 'direct') {
        $otherParticipant = $chatRoom->participants->where('id', '!=', auth()->id())->first();
        return $otherParticipant ? route('manager.chat.direct', $otherParticipant) : '#';
    }

    return '#';
}

    /**
     * Notify when message is read
     */
    public function notifyMessageRead($reader, $chatRoom)
    {
        if ($chatRoom->type === 'direct') {
            $otherParticipant = $chatRoom->participants->where('id', '!=', $reader->id)->first();

            if ($otherParticipant) {
                $data = [
                    'title' => 'Message Read',
                    'message' => "{$reader->name} has read your messages",
                    'action_url' => route('manager.chat.direct', $reader),
                    'icon' => 'fas fa-check-double',
                    'color' => 'gray',
                    'reader_name' => $reader->name,
                    'chat_room_id' => $chatRoom->id,
                ];

                return $this->sendToUser($otherParticipant->id, 'message_read', $data);
            }
        }

        return null;
    }

    /**
     * Get chat room URL for notifications
     */


    // 2. TASK NOTIFICATIONS
    public function notifyTaskAssigned($task, $assignedTo)
    {
        $data = [
            'title' => 'New Task Assigned',
            'message' => "You have been assigned a new task: {$task->title}",
            'action_url' => route('manager.tasks.show', $task),
            'icon' => 'fas fa-tasks',
            'color' => 'blue',
            'task_id' => $task->id,
            'task_title' => $task->title,
            'assigned_by' => auth()->user()->name,
            'project_name' => $task->project->name,
        ];

        $this->sendToUser($assignedTo->id, 'task_assigned', $data);

        // Notify admins about task assignment
        $adminData = [
            'title' => 'Task Assigned to Team Member',
            'message' => "{$assignedTo->name} has been assigned task: {$task->title}",
            'action_url' => route('manager.tasks.show', $task),
            'icon' => 'fas fa-user-plus',
            'color' => 'green',
            'task_id' => $task->id,
            'assigned_to' => $assignedTo->name,
            'assigned_by' => auth()->user()->name,
        ];

        $this->sendToAdmins('task_assigned_team', $adminData);
    }

    public function notifyTaskUpdated($task, $updater)
    {
        $data = [
            'title' => 'Task Updated',
            'message' => "Task '{$task->title}' has been updated by {$updater->name}",
            'action_url' => route('manager.tasks.show', $task),
            'icon' => 'fas fa-edit',
            'color' => 'yellow',
            'task_id' => $task->id,
            'updated_by' => $updater->name,
            'project_name' => $task->project->name,
        ];

        // Notify task assignee and admins
        $userIds = [$task->assigned_to];
        if ($task->project->manager) {
            $userIds[] = $task->project->manager->id;
        }

        $this->sendToUsers(array_unique($userIds), 'task_updated', $data);
    }

    public function notifyTaskCompleted($task, $completer)
    {
        $data = [
            'title' => 'Task Completed',
            'message' => "Task '{$task->title}' has been completed by {$completer->name}",
            'action_url' => route('manager.tasks.show', $task),
            'icon' => 'fas fa-check-circle',
            'color' => 'green',
            'task_id' => $task->id,
            'completed_by' => $completer->name,
            'project_name' => $task->project->name,
        ];

        $this->sendToAdmins('task_completed', $data);
        if ($task->project->manager && $task->project->manager->id != $completer->id) {
            $this->sendToUser($task->project->manager->id, 'task_completed', $data);
        }
    }

    public function notifyTaskDeadlineApproaching($task)
    {
        $data = [
            'title' => 'Deadline Approaching',
            'message' => "Task '{$task->title}' deadline is approaching",
            'action_url' => route('manager.tasks.show', $task),
            'icon' => 'fas fa-clock',
            'color' => 'yellow',
            'task_id' => $task->id,
            'due_date' => $task->due_date,
            'project_name' => $task->project->name,
        ];

        $this->sendToUser($task->assigned_to, 'deadline_approaching', $data);
        $this->sendToProjectManager($task->project, 'deadline_approaching', $data);
    }

    public function notifyTaskOverdue($task)
    {
        $data = [
            'title' => 'Task Overdue',
            'message' => "Task '{$task->title}' is overdue",
            'action_url' => route('manager.tasks.show', $task),
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'red',
            'task_id' => $task->id,
            'due_date' => $task->due_date,
            'project_name' => $task->project->name,
        ];

        $this->sendToUser($task->assigned_to, 'overdue', $data);
        $this->sendToProjectManager($task->project, 'overdue', $data);
        $this->sendToAdmins('overdue', $data);
    }

    // 3. PROJECT NOTIFICATIONS
    public function notifyProjectCreated($project, $creator)
    {
        $data = [
            'title' => 'New Project Created',
            'message' => "New project '{$project->name}' has been created by {$creator->name}",
            'action_url' => route('manager.projects.show', $project),
            'icon' => 'fas fa-project-diagram',
            'color' => 'purple',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'created_by' => $creator->name,
        ];

        $this->sendToAdmins('project_created', $data);
    }

    public function notifyProjectUpdated($project, $updater)
    {
        $data = [
            'title' => 'Project Updated',
            'message' => "Project '{$project->name}' has been updated by {$updater->name}",
            'action_url' => route('manager.projects.show', $project),
            'icon' => 'fas fa-sync',
            'color' => 'indigo',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'updated_by' => $updater->name,
        ];

        $this->sendToTeamMembers($project, 'project_updated', $data);
        $this->sendToAdmins('project_updated', $data);
    }

    public function notifyTeamMemberAdded($project, $teamMember, $addedBy)
    {
        $data = [
            'title' => 'Added to Project',
            'message' => "You have been added to project '{$project->name}' by {$addedBy->name}",
            'action_url' => route('manager.projects.show', $project),
            'icon' => 'fas fa-user-plus',
            'color' => 'green',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'added_by' => $addedBy->name,
        ];

        $this->sendToUser($teamMember->id, 'team_member_added', $data);

        // Notify admins
        $adminData = [
            'title' => 'Team Member Added',
            'message' => "{$teamMember->name} has been added to project '{$project->name}'",
            'action_url' => route('manager.projects.show', $project),
            'icon' => 'fas fa-users',
            'color' => 'blue',
            'project_id' => $project->id,
            'team_member' => $teamMember->name,
            'added_by' => $addedBy->name,
        ];

        $this->sendToAdmins('team_member_added', $adminData);
    }

    // 4. FILE NOTIFICATIONS
    public function notifyFileUploaded($file, $uploader, $project)
    {
        $data = [
            'title' => 'New File Uploaded',
            'message' => "New file '{$file->original_name}' has been uploaded to project '{$project->name}'",
            'action_url' => route('manager.projects.show', $project) . '#files',
            'icon' => 'fas fa-file-upload',
            'color' => 'pink',
            'file_id' => $file->id,
            'file_name' => $file->original_name,
            'uploaded_by' => $uploader->name,
            'project_name' => $project->name,
        ];

        $this->sendToTeamMembers($project, 'file_uploaded', $data);
        $this->sendToAdmins('file_uploaded', $data);
    }

    // 5. COMMENT NOTIFICATIONS
    public function notifyNewComment($comment, $commentedOn, $commentedBy)
    {
        $commentedOnType = class_basename($commentedOn);
        $commentedOnName = $commentedOn->title ?? $commentedOn->name ?? 'Item';

        $data = [
            'title' => 'New Comment',
            'message' => "New comment on {$commentedOnType} '{$commentedOnName}' by {$commentedBy->name}",
            'action_url' => $this->getCommentActionUrl($commentedOn, $comment),
            'icon' => 'fas fa-comment',
            'color' => 'teal',
            'comment_id' => $comment->id,
            'commented_on_type' => $commentedOnType,
            'commented_on_name' => $commentedOnName,
            'commented_by' => $commentedBy->name,
        ];

        // Notify relevant users based on what was commented on
        if ($commentedOnType === 'Task') {
            $this->sendToUser($commentedOn->assigned_to, 'new_comment', $data);
            $this->sendToProjectManager($commentedOn->project, 'new_comment', $data);
        } elseif ($commentedOnType === 'Project') {
            $this->sendToTeamMembers($commentedOn, 'new_comment', $data);
        }

        $this->sendToAdmins('new_comment', $data);
    }

    public function notifyMention($mentionedUser, $mentionedBy, $context, $contextUrl)
    {
        $data = [
            'title' => 'You were mentioned',
            'message' => "You were mentioned by {$mentionedBy->name} in {$context}",
            'action_url' => $contextUrl,
            'icon' => 'fas fa-at',
            'color' => 'orange',
            'mentioned_by' => $mentionedBy->name,
            'mentioned_by_id' => $mentionedBy->id,
            'context' => $context,
        ];

        $this->sendToUser($mentionedUser->id, 'mention', $data);
    }

    // 6. APPROVAL NOTIFICATIONS
    public function notifyApprovalRequired($approvable, $requestedBy)
    {
        $approvableType = class_basename($approvable);
        $approvableName = $approvable->title ?? $approvable->name ?? 'Item';

        $data = [
            'title' => 'Approval Required',
            'message' => "Approval required for {$approvableType} '{$approvableName}'",
            'action_url' => $this->getApprovalActionUrl($approvable),
            'icon' => 'fas fa-check-double',
            'color' => 'red',
            'approvable_type' => $approvableType,
            'approvable_name' => $approvableName,
            'requested_by' => $requestedBy->name,
        ];

        $this->sendToAdmins('approval_required', $data);
    }

    // Helper methods for URLs
    private function getCommentActionUrl($commentedOn, $comment)
    {
        $type = class_basename($commentedOn);

        if ($type === 'Task') {
            return route('manager.tasks.show', $commentedOn) . '#comment-' . $comment->id;
        } elseif ($type === 'Project') {
            return route('manager.projects.show', $commentedOn) . '#comments';
        }

        return '#';
    }

    private function getApprovalActionUrl($approvable)
    {
        $type = class_basename($approvable);

        if ($type === 'Task') {
            return route('manager.tasks.show', $approvable);
        } elseif ($type === 'Project') {
            return route('manager.projects.show', $approvable);
        }

        return '#';
    }
}
