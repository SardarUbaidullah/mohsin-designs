<?php

namespace App\Policies;

use App\Models\Tasks;
use App\Models\User;

class TaskCommentPolicy
{
    public function comment(User $user, Tasks $task)
    {
        return $this->canAccessTask($user, $task);
    }

    private function canAccessTask(User $user, Tasks $task)
    {
        // Super admin has full access
        if ($user->role === 'super_admin') return true;

        // Admin can access if they manage the project
        if ($user->role === 'admin' && $task->project->manager_id === $user->id) return true;

        // Users can access if they're assigned to the task
        if ($user->role === 'user' && $task->assigned_to === $user->id) return true;

        // Clients can access if it's their project's task
        if ($user->role === 'client' && $task->project->client_id === $user->client_id) return true;

        return false;
    }
}
