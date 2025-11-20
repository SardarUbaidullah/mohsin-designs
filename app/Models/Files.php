<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Files extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'task_id',
        'user_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'version',
        'parent_id',
        'description',
        'is_public',
        'accessible_users'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'is_public' => 'boolean',
        'accessible_users' => 'array',
    ];

    // ... existing relationships remain the same ...

    /**
     * Check if a user can access this file
     */
   public function canUserAccess($userId)
{
    // If file has a project, use project-based access
    if ($this->project_id) {
        return $this->canAccessProjectFile($userId);
    }

    // For general files, check access control
    return $this->canAccessGeneralFile($userId);
}

    private function canAccessProjectFile($userId)
    {
        $user = User::find($userId);

        if ($user->role === 'super_admin') {
            return true;
        }

        if ($user->role === 'admin') {
            return $this->project && $this->project->manager_id === $userId;
        }

        if ($user->role === 'user') {
            return Tasks::where('project_id', $this->project_id)
                ->where('assigned_to', $userId)
                ->exists();
        }

        return false;
    }

  private function canAccessGeneralFile($userId)
{
    $user = User::find($userId);

    // Super admin always has access
    if ($user->role === 'super_admin') {
        return true;
    }

    // Uploader always has access
    if ($this->user_id === $userId) {
        return true;
    }

    // Check if file is public
    if ($this->is_public) {
        return true;
    }

    // Check explicit access from JSON array - convert to integers for comparison
    $accessibleUsers = $this->accessible_users ?? [];

    // Ensure we're comparing integers
    $accessibleUsers = array_map('intval', $accessibleUsers);
    $userId = (int)$userId;

    // Debug accessible users
    \Log::info('Checking access for file:', [
        'file_id' => $this->id,
        'file_name' => $this->file_name,
        'user_id' => $userId,
        'accessible_users' => $accessibleUsers,
        'is_user_in_array' => in_array($userId, $accessibleUsers)
    ]);

    return in_array($userId, $accessibleUsers);
}

    /**
     * Get users who have explicit access to this file
     */
    public function getAccessibleUsers()
    {
        if (empty($this->accessible_users)) {
            return collect();
        }

        return User::whereIn('id', $this->accessible_users)->get();
    }

    /**
     * Add user to accessible users list
     */
    public function grantAccess($userId)
    {
        $accessibleUsers = $this->accessible_users ?? [];

        if (!in_array($userId, $accessibleUsers)) {
            $accessibleUsers[] = $userId;
            $this->accessible_users = $accessibleUsers;
            $this->save();
        }
    }

    /**
     * Remove user from accessible users list
     */
    public function revokeAccess($userId)
    {
        $accessibleUsers = $this->accessible_users ?? [];

        $accessibleUsers = array_filter($accessibleUsers, function($id) use ($userId) {
            return $id != $userId;
        });

        $this->accessible_users = array_values($accessibleUsers); // Reindex array
        $this->save();
    }

    /**
     * Check if specific user has explicit access
     */
    public function hasUserAccess($userId)
    {
        $accessibleUsers = $this->accessible_users ?? [];
        return in_array($userId, $accessibleUsers);
    }
    public function project()
    {
        return $this->belongsTo(Projects::class, 'project_id');
    }

    public function task()
    {
        return $this->belongsTo(Tasks::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // FIXED: Relationship for versions (child files)
    public function versions()
    {
        return $this->hasMany(Files::class, 'parent_id')->orderBy('version', 'desc');
    }

    // FIXED: Relationship for parent file
    public function parent()
    {
        return $this->belongsTo(Files::class, 'parent_id');
    }

    // Scope for latest versions only (files that don't have parent_id set)
    public function scopeLatestVersions($query)
    {
        return $query->whereNull('parent_id');
    }

    // FIXED: Get all versions including current file
    public function getAllVersions()
    {
        if ($this->parent_id) {
            // If this is a child version, get all siblings and parent
            return Files::where('parent_id', $this->parent_id)
                ->orWhere('id', $this->parent_id)
                ->orderBy('version', 'desc')
                ->get();
        } else {
            // If this is a parent, get all children
            return Files::where('parent_id', $this->id)
                ->orWhere('id', $this->id)
                ->orderBy('version', 'desc')
                ->get();
        }
    }

    // FIXED: Get total versions count including current file
    public function getTotalVersionsCountAttribute()
    {
        if ($this->parent_id) {
            // If this is a child version, count parent + all siblings
            return Files::where('parent_id', $this->parent_id)
                ->orWhere('id', $this->parent_id)
                ->count();
        } else {
            // If this is a parent, count all children + self
            return Files::where('parent_id', $this->id)
                ->orWhere('id', $this->id)
                ->count();
        }
    }

    // Get file extension
    public function getExtensionAttribute()
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    // Check if file is an image
    public function getIsImageAttribute()
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    // Check if file is PDF
    public function getIsPdfAttribute()
    {
        return $this->mime_type === 'application/pdf';
    }

    // Get readable file size
    public function getReadableSizeAttribute()
    {
        if (!$this->file_size) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    // FIXED: Check if this is the latest version
    public function getIsLatestVersionAttribute()
    {
        if ($this->parent_id) {
            // This is a child version, so it's not the latest
            return false;
        }

        // This is a parent, check if it has any newer versions
        $latestVersion = Files::where('parent_id', $this->id)->max('version');
        return !$latestVersion || $this->version >= $latestVersion;
    }




    /**
 * Check if current user can access the file
 */
private function canAccessFile($file)
{
    $user = auth()->user();

    if ($user->role === 'super_admin') {
        return true;
    }

    if ($user->role === 'admin') {
        // Manager can access files from projects they manage
        return $file->project && $file->project->manager_id === $user->id;
    }

    if ($user->role === 'user') {
        // Team member can access files from projects where they have tasks
        if (!$file->project) {
            return false; // Team members can't access general files
        }

        return Tasks::where('project_id', $file->project_id)
            ->where('assigned_to', $user->id)
            ->exists();
    }

    return false;
}

/**
 * Get projects accessible by current user
 */
private function getAccessibleProjects()
{
    $user = auth()->user();

    if ($user->role === 'super_admin') {
        return Projects::all();
    }

    if ($user->role === 'admin') {
        return Projects::where('manager_id', $user->id)->get();
    }

    if ($user->role === 'user') {
        return Projects::whereHas('tasks', function($q) use ($user) {
            $q->where('assigned_to', $user->id);
        })->get();
    }

    return collect();
}

/**
 * Get tasks accessible by current user
 */
private function getAccessibleTasks()
{
    $user = auth()->user();

    if ($user->role === 'super_admin') {
        return Tasks::all();
    }

    if ($user->role === 'admin') {
        $projectIds = Projects::where('manager_id', $user->id)->pluck('id');
        return Tasks::whereIn('project_id', $projectIds)->get();
    }

    if ($user->role === 'user') {
        return Tasks::where('assigned_to', $user->id)->get();
    }

    return collect();
}
}
