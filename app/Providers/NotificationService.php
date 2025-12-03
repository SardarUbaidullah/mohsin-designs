<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\User;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

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

    // ==================== ROLE-BASED URL GENERATORS ====================

    /**
     * Get task URL based on user role
     */
    private function getTaskUrl($task, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        $role = $user->role;
        $taskId = is_object($task) ? $task->id : $task;
        
        switch ($role) {
            case 'super_admin':
                return route('tasks.show', $taskId);
                
            case 'admin': // manager role
                return route('manager.tasks.show', $taskId);
                
            case 'team_member':
            case 'user': // team member
                return route('team.tasks.show', $taskId);
                
            case 'client':
                return '#';
                
            default:
                return route('tasks.show', $taskId);
        }
    }

    /**
     * Get project URL based on user role
     */
    private function getProjectUrl($project, $user = null)
{
    if (!$user) {
        $user = Auth::user();
    }

    $role = $user->role;
    $projectId = is_object($project) ? $project->id : $project;
    
    // Agar user project team member hai, toh manager.projects.show milega
    // Kyunki project management managers ke through hota hai
    switch ($role) {
        case 'super_admin':
            // Super admin bhi manager.projects.show use karega project dekhne ke liye
            return route('manager.projects.show', $projectId);
            
        case 'admin': // manager role
            return route('manager.projects.show', $projectId);
            
        case 'team_member':
        case 'user': // team member
            // Team member ko bhi manager.projects.show milega project dekhne ke liye
            return route('manager.projects.show', $projectId);
            
        case 'client':
            return route('client.projects.show', $projectId);
            
        default:
            return route('manager.projects.show', $projectId);
    }
}


    /**
     * Get chat URL based on user role and chat type
     */
    private function getChatUrl($chatRoom = null, $sender = null, $project = null, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        $role = $user->role;
        
        if ($chatRoom) {
            if ($chatRoom->type === 'project' && $chatRoom->project) {
                if ($role === 'team_member' || $role === 'user') {
                    return route('team.chat.project', $chatRoom->project->id);
                } else {
                    return route('manager.chat.project', $chatRoom->project->id);
                }
            } elseif ($chatRoom->type === 'direct') {
                if ($role === 'team_member' || $role === 'user') {
                    return route('team.chat.direct', $sender ? $sender->id : '#');
                } else {
                    return route('manager.chat.direct', $sender ? $sender->id : '#');
                }
            }
        }

        if ($project) {
            $projectId = is_object($project) ? $project->id : $project;
            if ($role === 'team_member' || $role === 'user') {
                return route('team.chat.project', $projectId);
            } else {
                return route('manager.chat.project', $projectId);
            }
        }

        if ($sender) {
            $senderId = is_object($sender) ? $sender->id : $sender;
            if ($role === 'team_member' || $role === 'user') {
                return route('team.chat.direct', $senderId);
            } else {
                return route('manager.chat.direct', $senderId);
            }
        }

        if ($role === 'team_member' || $role === 'user') {
            return route('team.chat.index');
        } else {
            return route('manager.chat.index');
        }
    }

    /**
     * Get file download URL based on user role
     */
    private function getFileDownloadUrl($file, $user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }

        $role = $user->role;
        $fileId = is_object($file) ? $file->id : $file;
        
        switch ($role) {
            case 'client':
                return route('client.files.download', $fileId);
                
            default:
                return route('files.download', $fileId);
        }
    }

    // ==================== SPECIFIC NOTIFICATION METHODS ====================

    // 1. CHAT NOTIFICATIONS

    /**
     * Notify new project chat message with role-based URLs
     */
    public function notifyProjectChatMessage($project, $sender, $message, $chatRoom = null)
    {
        try {
            $project->load(['teamMembers', 'manager']);
            $messagePreview = Str::limit($message, 100);

            $usersToNotify = collect();

            // 1. Add team members
            if ($project->teamMembers && $project->teamMembers->isNotEmpty()) {
                $usersToNotify = $usersToNotify->merge($project->teamMembers);
            }

            // 2. Add project manager if exists
            if ($project->manager) {
                $managerExists = $usersToNotify->contains('id', $project->manager->id);
                if (!$managerExists) {
                    $usersToNotify->push($project->manager);
                }
            }

            // 3. Add super admins
            $superAdmins = User::where('role', 'super_admin')->get();
            foreach ($superAdmins as $admin) {
                $adminExists = $usersToNotify->contains('id', $admin->id);
                if (!$adminExists) {
                    $usersToNotify->push($admin);
                }
            }

            // 4. Remove sender from list
            $finalUsers = $usersToNotify->where('id', '!=', $sender->id)->unique('id');

            // Send notifications with role-based URLs
            $sentCount = 0;
            foreach ($finalUsers as $user) {
                $chatUrl = $this->getChatUrl($chatRoom, null, $project, $user);
                
                $data = [
                    'title' => "New Message in {$project->name}",
                    'message' => "{$sender->name}: {$messagePreview}",
                    'action_url' => $chatUrl,
                    'icon' => 'fas fa-comments',
                    'color' => 'blue',
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                    'sender_name' => $sender->name,
                    'sender_id' => $sender->id,
                    'message_preview' => $messagePreview,
                    'has_attachment' => false,
                    'chat_room_id' => $chatRoom ? $chatRoom->id : null,
                    'user_role' => $user->role,
                ];

                $result = $this->sendToUser($user->id, 'project_chat_message', $data);
                if ($result) $sentCount++;
            }

            return $sentCount > 0;

        } catch (\Exception $e) {
            Log::error('❌ Project chat notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify new direct message with role-based URLs
     */
    public function notifyDirectMessage($recipient, $sender, $message, $chatRoom = null)
    {
        try {
            $messagePreview = Str::limit($message, 100);
            
            // Get chat URL based on recipient's role
            $chatUrl = $this->getChatUrl($chatRoom, $sender, null, $recipient);

            $data = [
                'title' => "New Message from {$sender->name}",
                'message' => $messagePreview,
                'action_url' => $chatUrl,
                'icon' => 'fas fa-envelope',
                'color' => 'green',
                'sender_id' => $sender->id,
                'sender_name' => $sender->name,
                'message_preview' => $messagePreview,
                'has_attachment' => false,
                'chat_room_id' => $chatRoom ? $chatRoom->id : null,
                'user_role' => $recipient->role,
            ];

            return $this->sendToUser($recipient->id, 'direct_message', $data);

        } catch (\Exception $e) {
            Log::error('Direct message notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notify mentioned users in chat with role-based URLs
     */
    public function notifyMentionedInChat($mentionedUser, $mentionedBy, $message, $chatRoom, $context)
    {
        $messagePreview = Str::limit($message, 100);
        
        // Get chat URL based on mentioned user's role
        $chatUrl = $this->getChatUrl($chatRoom, null, null, $mentionedUser);

        $data = [
            'title' => 'You Were Mentioned',
            'message' => "{$mentionedBy->name} mentioned you in {$context}: {$messagePreview}",
            'action_url' => $chatUrl,
            'icon' => 'fas fa-at',
            'color' => 'pink',
            'mentioned_by' => $mentionedBy->name,
            'mentioned_by_id' => $mentionedBy->id,
            'message_preview' => $messagePreview,
            'context' => $context,
            'chat_room_id' => $chatRoom->id,
            'user_role' => $mentionedUser->role,
        ];

        return $this->sendToUser($mentionedUser->id, 'mentioned_in_chat', $data);
    }

    /**
     * Notify when message is read with role-based URLs
     */
    public function notifyMessageRead($reader, $chatRoom)
    {
        if ($chatRoom->type === 'direct') {
            $otherParticipant = $chatRoom->participants->where('id', '!=', $reader->id)->first();

            if ($otherParticipant) {
                // Get chat URL based on other participant's role
                $chatUrl = $this->getChatUrl($chatRoom, $reader, null, $otherParticipant);

                $data = [
                    'title' => 'Message Read',
                    'message' => "{$reader->name} has read your messages",
                    'action_url' => $chatUrl,
                    'icon' => 'fas fa-check-double',
                    'color' => 'gray',
                    'reader_name' => $reader->name,
                    'chat_room_id' => $chatRoom->id,
                    'user_role' => $otherParticipant->role,
                ];

                return $this->sendToUser($otherParticipant->id, 'message_read', $data);
            }
        }

        return null;
    }

    // 2. TASK NOTIFICATIONS

    public function notifyTaskAssigned($task, $assignedTo)
    {
        // 1. Assigned user ko uska role-based URL
        $assignedUserTaskUrl = $this->getTaskUrl($task, $assignedTo);
        
        $assignedUserData = [
            'title' => 'New Task Assigned',
            'message' => "You have been assigned a new task: {$task->title}",
            'action_url' => $assignedUserTaskUrl,
            'icon' => 'fas fa-tasks',
            'color' => 'blue',
            'task_id' => $task->id,
            'task_title' => $task->title,
            'assigned_by' => auth()->user()->name,
            'project_name' => $task->project->name,
            'user_role' => $assignedTo->role,
        ];

        // Assigned user ko notification bhejo
        $this->sendToUser($assignedTo->id, 'task_assigned', $assignedUserData);

        // 2. Admins/Managers ke liye ALAG URL (jisne assign kiya aur baaki admins)
        $currentUser = auth()->user(); // Jisne assign kiya
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($admins as $admin) {
            // Har admin ko uska apna role-based URL
            $adminTaskUrl = $this->getTaskUrl($task, $admin);
            
            $adminData = [
                'title' => 'Task Assigned to Team Member',
                'message' => "{$assignedTo->name} has been assigned task: {$task->title}",
                'action_url' => $adminTaskUrl,
                'icon' => 'fas fa-user-plus',
                'color' => 'green',
                'task_id' => $task->id,
                'assigned_to' => $assignedTo->name,
                'assigned_by' => auth()->user()->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'task_assigned_team', $adminData);
        }
    }

    public function notifyTaskUpdated($task, $updater)
    {
        // Task assignee ko URL uske role se
        $assignee = User::find($task->assigned_to);
        
        if ($assignee) {
            $assigneeTaskUrl = $this->getTaskUrl($task, $assignee);
            
            $assigneeData = [
                'title' => 'Task Updated',
                'message' => "Task '{$task->title}' has been updated by {$updater->name}",
                'action_url' => $assigneeTaskUrl,
                'icon' => 'fas fa-edit',
                'color' => 'yellow',
                'task_id' => $task->id,
                'updated_by' => $updater->name,
                'project_name' => $task->project->name,
                'user_role' => $assignee->role,
            ];

            $this->sendToUser($assignee->id, 'task_updated', $assigneeData);
        }

        // Project manager ko URL uske role se (agar assignee nahi hai)
        if ($task->project->manager && (!$assignee || $task->project->manager->id != $assignee->id)) {
            $manager = $task->project->manager;
            $managerTaskUrl = $this->getTaskUrl($task, $manager);
            
            $managerData = [
                'title' => 'Task Updated',
                'message' => "Task '{$task->title}' has been updated by {$updater->name}",
                'action_url' => $managerTaskUrl,
                'icon' => 'fas fa-edit',
                'color' => 'yellow',
                'task_id' => $task->id,
                'updated_by' => $updater->name,
                'project_name' => $task->project->name,
                'user_role' => $manager->role,
            ];

            $this->sendToUser($manager->id, 'task_updated', $managerData);
        }
    }

    public function notifyTaskCompleted($task, $completer)
    {
        // Admins ko notification (har admin ko uska apna URL)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($admins as $admin) {
            $adminTaskUrl = $this->getTaskUrl($task, $admin);
            
            $adminData = [
                'title' => 'Task Completed',
                'message' => "Task '{$task->title}' has been completed by {$completer->name}",
                'action_url' => $adminTaskUrl,
                'icon' => 'fas fa-check-circle',
                'color' => 'green',
                'task_id' => $task->id,
                'completed_by' => $completer->name,
                'project_name' => $task->project->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'task_completed', $adminData);
        }
        
        // Project manager ko notification (agar completer nahi hai)
        if ($task->project->manager && $task->project->manager->id != $completer->id) {
            $manager = $task->project->manager;
            $managerTaskUrl = $this->getTaskUrl($task, $manager);
            
            $managerData = [
                'title' => 'Task Completed',
                'message' => "Task '{$task->title}' has been completed by {$completer->name}",
                'action_url' => $managerTaskUrl,
                'icon' => 'fas fa-check-circle',
                'color' => 'green',
                'task_id' => $task->id,
                'completed_by' => $completer->name,
                'project_name' => $task->project->name,
                'user_role' => $manager->role,
            ];

            $this->sendToUser($manager->id, 'task_completed', $managerData);
        }
    }

    public function notifyTaskDeadlineApproaching($task)
    {
        // Notify assignee
        $assignee = User::find($task->assigned_to);
        if ($assignee) {
            $assigneeTaskUrl = $this->getTaskUrl($task, $assignee);
            
            $assigneeData = [
                'title' => 'Deadline Approaching',
                'message' => "Task '{$task->title}' deadline is approaching",
                'action_url' => $assigneeTaskUrl,
                'icon' => 'fas fa-clock',
                'color' => 'yellow',
                'task_id' => $task->id,
                'due_date' => $task->due_date,
                'project_name' => $task->project->name,
                'user_role' => $assignee->role,
            ];

            $this->sendToUser($assignee->id, 'deadline_approaching', $assigneeData);
        }

        // Notify project manager
        if ($task->project->manager) {
            $manager = $task->project->manager;
            $managerTaskUrl = $this->getTaskUrl($task, $manager);
            
            $managerData = [
                'title' => 'Deadline Approaching',
                'message' => "Task '{$task->title}' deadline is approaching",
                'action_url' => $managerTaskUrl,
                'icon' => 'fas fa-clock',
                'color' => 'yellow',
                'task_id' => $task->id,
                'due_date' => $task->due_date,
                'project_name' => $task->project->name,
                'user_role' => $manager->role,
            ];

            $this->sendToUser($manager->id, 'deadline_approaching', $managerData);
        }
    }

    public function notifyTaskOverdue($task)
    {
        // Notify assignee
        $assignee = User::find($task->assigned_to);
        if ($assignee) {
            $assigneeTaskUrl = $this->getTaskUrl($task, $assignee);
            
            $assigneeData = [
                'title' => 'Task Overdue',
                'message' => "Task '{$task->title}' is overdue",
                'action_url' => $assigneeTaskUrl,
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'red',
                'task_id' => $task->id,
                'due_date' => $task->due_date,
                'project_name' => $task->project->name,
                'user_role' => $assignee->role,
            ];

            $this->sendToUser($assignee->id, 'overdue', $assigneeData);
        }

        // Notify project manager
        if ($task->project->manager) {
            $manager = $task->project->manager;
            $managerTaskUrl = $this->getTaskUrl($task, $manager);
            
            $managerData = [
                'title' => 'Task Overdue',
                'message' => "Task '{$task->title}' is overdue",
                'action_url' => $managerTaskUrl,
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'red',
                'task_id' => $task->id,
                'due_date' => $task->due_date,
                'project_name' => $task->project->name,
                'user_role' => $manager->role,
            ];

            $this->sendToUser($manager->id, 'overdue', $managerData);
        }

        // Notify admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $adminTaskUrl = $this->getTaskUrl($task, $admin);
            
            $adminData = [
                'title' => 'Task Overdue',
                'message' => "Task '{$task->title}' is overdue",
                'action_url' => $adminTaskUrl,
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'red',
                'task_id' => $task->id,
                'due_date' => $task->due_date,
                'project_name' => $task->project->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'overdue', $adminData);
        }
    }

    // 3. PROJECT NOTIFICATIONS

    public function notifyProjectCreated($project, $creator)
    {
        // Admins ko notification (har admin ko uska apna URL)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($admins as $admin) {
            $adminProjectUrl = $this->getProjectUrl($project, $admin);
            
            $adminData = [
                'title' => 'New Project Created',
                'message' => "New project '{$project->name}' has been created by {$creator->name}",
                'action_url' => $adminProjectUrl,
                'icon' => 'fas fa-project-diagram',
                'color' => 'purple',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'created_by' => $creator->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'project_created', $adminData);
        }
    }

    public function notifyProjectUpdated($project, $updater)
    {
        // Team members ko unke role se URLs
        foreach ($project->teamMembers as $teamMember) {
            $projectUrl = $this->getProjectUrl($project, $teamMember);
            
            $teamMemberData = [
                'title' => 'Project Updated',
                'message' => "Project '{$project->name}' has been updated by {$updater->name}",
                'action_url' => $projectUrl,
                'icon' => 'fas fa-sync',
                'color' => 'indigo',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'updated_by' => $updater->name,
                'user_role' => $teamMember->role,
            ];

            $this->sendToUser($teamMember->id, 'project_updated', $teamMemberData);
        }

        // Admins ko ALAG URL
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $adminProjectUrl = $this->getProjectUrl($project, $admin);
            
            $adminData = [
                'title' => 'Project Updated',
                'message' => "Project '{$project->name}' has been updated by {$updater->name}",
                'action_url' => $adminProjectUrl,
                'icon' => 'fas fa-sync',
                'color' => 'indigo',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'updated_by' => $updater->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'project_updated', $adminData);
        }
    }

    public function notifyTeamMemberAdded($project, $teamMember, $addedBy)
    {
        // Team member ko uska role-based URL
        $teamMemberProjectUrl = $this->getProjectUrl($project, $teamMember);

        $teamMemberData = [
            'title' => 'Added to Project',
            'message' => "You have been added to project '{$project->name}' by {$addedBy->name}",
            'action_url' => $teamMemberProjectUrl,
            'icon' => 'fas fa-user-plus',
            'color' => 'green',
            'project_id' => $project->id,
            'project_name' => $project->name,
            'added_by' => $addedBy->name,
            'user_role' => $teamMember->role,
        ];

        $this->sendToUser($teamMember->id, 'team_member_added', $teamMemberData);

        // Admins ko ALAG URL
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $adminProjectUrl = $this->getProjectUrl($project, $admin);
            
            $adminData = [
                'title' => 'Team Member Added',
                'message' => "{$teamMember->name} has been added to project '{$project->name}'",
                'action_url' => $adminProjectUrl,
                'icon' => 'fas fa-users',
                'color' => 'blue',
                'project_id' => $project->id,
                'team_member' => $teamMember->name,
                'added_by' => $addedBy->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'team_member_added', $adminData);
        }
    }

    // 4. FILE NOTIFICATIONS

    public function notifyFileUploaded($file, $uploader, $project)
    {
        // Team members ko unke role se URLs
        foreach ($project->teamMembers as $teamMember) {
            $projectUrl = $this->getProjectUrl($project, $teamMember);
            
            $teamMemberData = [
                'title' => 'New File Uploaded',
                'message' => "New file '{$file->original_name}' has been uploaded to project '{$project->name}'",
                'action_url' => $projectUrl . '#files',
                'icon' => 'fas fa-file-upload',
                'color' => 'pink',
                'file_id' => $file->id,
                'file_name' => $file->original_name,
                'uploaded_by' => $uploader->name,
                'project_name' => $project->name,
                'user_role' => $teamMember->role,
            ];

            $this->sendToUser($teamMember->id, 'file_uploaded', $teamMemberData);
        }

        // Admins ko ALAG URL
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $adminProjectUrl = $this->getProjectUrl($project, $admin);
            
            $adminData = [
                'title' => 'New File Uploaded',
                'message' => "New file '{$file->original_name}' has been uploaded to project '{$project->name}'",
                'action_url' => $adminProjectUrl . '#files',
                'icon' => 'fas fa-file-upload',
                'color' => 'pink',
                'file_id' => $file->id,
                'file_name' => $file->original_name,
                'uploaded_by' => $uploader->name,
                'project_name' => $project->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'file_uploaded', $adminData);
        }
    }

    // 5. COMMENT NOTIFICATIONS

    public function notifyNewComment($comment, $commentedOn, $commentedBy)
    {
        $commentedOnType = class_basename($commentedOn);
        $commentedOnName = $commentedOn->title ?? $commentedOn->name ?? 'Item';

        // Notify relevant users based on what was commented on
        if ($commentedOnType === 'Task') {
            // Notify task assignee
            $assignee = User::find($commentedOn->assigned_to);
            if ($assignee) {
                $taskUrl = $this->getTaskUrl($commentedOn, $assignee);
                
                $assigneeData = [
                    'title' => 'New Comment',
                    'message' => "New comment on Task '{$commentedOnName}' by {$commentedBy->name}",
                    'action_url' => $taskUrl . '#comment-' . $comment->id,
                    'icon' => 'fas fa-comment',
                    'color' => 'teal',
                    'comment_id' => $comment->id,
                    'commented_on_type' => $commentedOnType,
                    'commented_on_name' => $commentedOnName,
                    'commented_by' => $commentedBy->name,
                    'user_role' => $assignee->role,
                ];

                $this->sendToUser($assignee->id, 'new_comment', $assigneeData);
            }

            // Notify project manager
            if ($commentedOn->project->manager) {
                $manager = $commentedOn->project->manager;
                $managerTaskUrl = $this->getTaskUrl($commentedOn, $manager);
                
                $managerData = [
                    'title' => 'New Comment',
                    'message' => "New comment on Task '{$commentedOnName}' by {$commentedBy->name}",
                    'action_url' => $managerTaskUrl . '#comment-' . $comment->id,
                    'icon' => 'fas fa-comment',
                    'color' => 'teal',
                    'comment_id' => $comment->id,
                    'commented_on_type' => $commentedOnType,
                    'commented_on_name' => $commentedOnName,
                    'commented_by' => $commentedBy->name,
                    'user_role' => $manager->role,
                ];

                $this->sendToUser($manager->id, 'new_comment', $managerData);
            }
        } elseif ($commentedOnType === 'Project') {
            // Notify team members
            foreach ($commentedOn->teamMembers as $teamMember) {
                $projectUrl = $this->getProjectUrl($commentedOn, $teamMember);
                
                $teamMemberData = [
                    'title' => 'New Comment',
                    'message' => "New comment on Project '{$commentedOnName}' by {$commentedBy->name}",
                    'action_url' => $projectUrl . '#comments',
                    'icon' => 'fas fa-comment',
                    'color' => 'teal',
                    'comment_id' => $comment->id,
                    'commented_on_type' => $commentedOnType,
                    'commented_on_name' => $commentedOnName,
                    'commented_by' => $commentedBy->name,
                    'user_role' => $teamMember->role,
                ];

                $this->sendToUser($teamMember->id, 'new_comment', $teamMemberData);
            }
        }

        // Notify admins
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $adminUrl = $commentedOnType === 'Task' 
                ? $this->getTaskUrl($commentedOn, $admin) . '#comment-' . $comment->id
                : $this->getProjectUrl($commentedOn, $admin) . '#comments';

            $adminData = [
                'title' => 'New Comment',
                'message' => "New comment on {$commentedOnType} '{$commentedOnName}' by {$commentedBy->name}",
                'action_url' => $adminUrl,
                'icon' => 'fas fa-comment',
                'color' => 'teal',
                'comment_id' => $comment->id,
                'commented_on_type' => $commentedOnType,
                'commented_on_name' => $commentedOnName,
                'commented_by' => $commentedBy->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'new_comment', $adminData);
        }
    }

    // 6. APPROVAL NOTIFICATIONS

    public function notifyApprovalRequired($approvable, $requestedBy)
    {
        $approvableType = class_basename($approvable);
        $approvableName = $approvable->title ?? $approvable->name ?? 'Item';

        // Admins ko notification (har admin ko uska apna URL)
        $admins = User::whereIn('role', ['admin', 'super_admin'])->get();
        
        foreach ($admins as $admin) {
            $approvalUrl = $approvableType === 'Task' 
                ? $this->getTaskUrl($approvable, $admin)
                : $this->getProjectUrl($approvable, $admin);

            $adminData = [
                'title' => 'Approval Required',
                'message' => "Approval required for {$approvableType} '{$approvableName}'",
                'action_url' => $approvalUrl,
                'icon' => 'fas fa-check-double',
                'color' => 'red',
                'approvable_type' => $approvableType,
                'approvable_name' => $approvableName,
                'requested_by' => $requestedBy->name,
                'user_role' => $admin->role,
            ];

            $this->sendToUser($admin->id, 'approval_required', $adminData);
        }
    }
}