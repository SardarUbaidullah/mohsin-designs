<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Files;

class CommentPolicy
{
    public function view(User $user, Comment $comment)
    {
        // Users can view comments if they have access to the commentable item
        return $this->canAccessCommentable($user, $comment->commentable);
    }

    public function create(User $user)
    {
        // All authenticated users can create comments
        return true;
    }

    public function update(User $user, Comment $comment)
    {
        // Users can update their own comments
        // Admins can update any comments
        return $user->id === $comment->user_id ||
               in_array($user->role, ['super_admin', 'admin']);
    }

    public function delete(User $user, Comment $comment)
    {
        // Users can delete their own comments
        // Admins can delete any comments
        return $user->id === $comment->user_id ||
               in_array($user->role, ['super_admin', 'admin']);
    }

    private function canAccessCommentable(User $user, $commentable)
    {
        if (!$commentable) {
            return false;
        }

        if ($user->role === 'super_admin') {
            return true;
        }

        if ($commentable instanceof Projects) {
            return $this->canAccessProject($user, $commentable);
        }

        if ($commentable instanceof Tasks) {
            return $this->canAccessTask($user, $commentable);
        }

        if ($commentable instanceof Files) {
            return $this->canAccessFile($user, $commentable);
        }

        return false;
    }

    private function canAccessProject(User $user, $project)
    {
        if ($user->role === 'admin' && $project->manager_id === $user->id) {
            return true;
        }

        if ($user->role === 'user') {
            return Tasks::where('project_id', $project->id)
                ->where('assigned_to', $user->id)
                ->exists();
        }

        if ($user->role === 'client' && $project->client_id === $user->client_id) {
            return true;
        }

        return false;
    }

    private function canAccessTask(User $user, $task)
    {
        // Load project relationship if not already loaded
        if (!$task->relationLoaded('project')) {
            $task->load('project');
        }

        if ($user->role === 'admin' && $task->project->manager_id === $user->id) {
            return true;
        }

        if ($user->role === 'user' && $task->assigned_to === $user->id) {
            return true;
        }

        if ($user->role === 'client' && $task->project->client_id === $user->client_id) {
            return true;
        }

        return false;
    }

    private function canAccessFile(User $user, $file)
    {
        // If file belongs to a project, check project access
        if ($file->project_id) {
            if (!$file->relationLoaded('project')) {
                $file->load('project');
            }
            return $this->canAccessProject($user, $file->project);
        }

        // If file belongs to a task, check task access
        if ($file->task_id) {
            if (!$file->relationLoaded('task')) {
                $file->load('task');
            }
            return $this->canAccessTask($user, $file->task);
        }

        // General files - super_admin and admin can access
        return in_array($user->role, ['super_admin', 'admin']);
    }
}
