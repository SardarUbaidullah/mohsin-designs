<?php

namespace App\Policies;

use App\Models\Projects;
use App\Models\User;

class ProjectCommentPolicy
{
    public function comment(User $user, Projects $project)
    {
        return $this->canAccessProject($user, $project);
    }

    private function canAccessProject(User $user, $project)
    {
        // Same logic as in CommentPolicy
        if ($user->role === 'super_admin') return true;
        if ($user->role === 'admin' && $project->manager_id === $user->id) return true;
        if ($user->role === 'user') {
            return \App\Models\Tasks::where('project_id', $project->id)
                ->where('assigned_to', $user->id)
                ->exists();
        }
        if ($user->role === 'client' && $project->client_id === $user->client_id) return true;
        return false;
    }
}
