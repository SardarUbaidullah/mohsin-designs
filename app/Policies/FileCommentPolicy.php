<?php

namespace App\Policies;

use App\Models\Files;
use App\Models\User;

class FileCommentPolicy
{
    public function comment(User $user, Files $file)
    {
        return $this->canAccessFile($user, $file);
    }

    private function canAccessFile(User $user, Files $file)
    {
        // Super admin has full access
        if ($user->role === 'super_admin') return true;

        // Admin can access if they manage the project
        if ($user->role === 'admin' && $file->project->manager_id === $user->id) return true;

        // Users can access if they have tasks in the project
        if ($user->role === 'user') {
            return \App\Models\Tasks::where('project_id', $file->project_id)
                ->where('assigned_to', $user->id)
                ->exists();
        }

        // Clients can access if it's their project's file
        if ($user->role === 'client' && $file->project->client_id === $user->client_id) return true;

        return false;
    }
}
