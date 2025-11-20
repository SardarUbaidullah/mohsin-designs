<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable as LaravelNotifiable;
use App\Traits\HasNotifications;

class User extends Authenticatable
{
    use LaravelNotifiable, HasNotifications, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'client_id',
        'profile_photo_path',
        'department',
        'status',
        'deactivated_at',
        'can_create_project',
    ];

    protected $attributes = [
        'department' => 'Not Assigned',
        'can_create_project' => false,
        'status' => 'active' // Add default status
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deactivated_at' => 'datetime',
        'can_create_project' => 'boolean'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==================== STATUS METHODS ====================

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for inactive users
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Check if user is active
    public function isActive()
    {
        return $this->status === 'active';
    }

    // Check if user is inactive
    public function isInactive()
    {
        return $this->status === 'inactive';
    }

    // Activate user
    public function activate()
    {
        $this->update([
            'status' => 'active',
            'deactivated_at' => null
        ]);
    }

    // Deactivate user
    public function deactivate()
    {
        $this->update([
            'status' => 'inactive',
            'deactivated_at' => now()
        ]);
    }

    // Toggle status
    public function toggleStatus()
    {
        if ($this->isActive()) {
            $this->deactivate();
        } else {
            $this->activate();
        }
        return $this;
    }

    // ==================== SCOPES ====================

    public function scopeDistinctDepartments($query)
    {
        return $query->select('department')
                    ->whereNotNull('department')
                    ->where('department', '!=', 'Not Assigned')
                    ->distinct()
                    ->orderBy('department')
                    ->pluck('department');
    }
    
    public function scopeCanCreateProjects($query)
    {
        return $query->where('role', 'admin')
                    ->where('can_create_project', true);
    }

    // ==================== RELATIONSHIPS ====================

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function managedProjects()
    {
        return $this->hasMany(Projects::class, 'manager_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Tasks::class, 'assigned_to');
    }

    public function createdTasks()
    {
        return $this->hasMany(Tasks::class, 'created_by');
    }

    // Chat Relationships
    public function chatRooms()
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_participants', 'user_id', 'chat_room_id')
                    ->withTimestamps();
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function chatParticipants()
    {
        return $this->hasMany(ChatParticipant::class);
    }

    // Project Relationships
    public function teamProjects()
    {
        return $this->belongsToMany(Projects::class, 'project_team_members', 'user_id', 'project_id')
                    ->withTimestamps();
    }

    // Comments Relationship
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // ==================== ROLE METHODS ====================

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManager()
    {
        return $this->role === 'manager';
    }

    public function isTeamMember()
    {
        return $this->role === 'user';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    // ==================== ACCESS CONTROL METHODS ====================

    public function canAccessProject(Projects $project)
    {
        // Inactive users cannot access anything
        if ($this->isInactive()) {
            return false;
        }

        // Super Admin & Admin can access all projects
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        // Manager can access projects they manage
        if ($this->isManager() && $project->manager_id === $this->id) {
            return true;
        }

        // Team Member can access projects they're assigned to
        if ($this->isTeamMember() && $project->teamMembers->contains('id', $this->id)) {
            return true;
        }

        // Client can access their own projects
        if ($this->isClient() && $project->client_id === $this->client_id) {
            return true;
        }

        return false;
    }

    public function canMessage(User $recipient)
    {
        // Inactive users cannot message anyone
        if ($this->isInactive()) {
            return false;
        }

        // Cannot message inactive users
        if ($recipient->isInactive()) {
            return false;
        }

        // Cannot message yourself
        if ($this->id === $recipient->id) {
            return false;
        }

        // Super Admin can message anyone
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin can message anyone except Super Admin
        if ($this->isAdmin()) {
            return !$recipient->isSuperAdmin();
        }

        // Manager can message anyone except Super Admin
        if ($this->isManager()) {
            return !$recipient->isSuperAdmin();
        }

        // Team Member can only message Managers, Admin, and Super Admin
        if ($this->isTeamMember()) {
            return $recipient->isManager() || $recipient->isAdmin() || $recipient->isSuperAdmin();
        }

        // Client can only message their project managers and admins
        if ($this->isClient()) {
            // Client can message admins and super admins
            if ($recipient->isAdmin() || $recipient->isSuperAdmin()) {
                return true;
            }

            // Client can message managers who manage their projects
            if ($recipient->isManager()) {
                return $recipient->managedProjects()
                    ->where('client_id', $this->client_id)
                    ->exists();
            }

            return false;
        }

        return false;
    }

    public function canAccessChat(ChatRoom $chatRoom)
    {
        // Inactive users cannot access chats
        if ($this->isInactive()) {
            return false;
        }

        // Super Admin & Admin can access all chats
        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return true;
        }

        if ($chatRoom->type === 'project') {
            return $this->canAccessProject($chatRoom->project);
        }

        if ($chatRoom->type === 'direct') {
            return $chatRoom->participants->contains('id', $this->id) &&
                   $this->canMessage($chatRoom->getOtherParticipant($this));
        }

        return false;
    }

    // ==================== CLIENT METHODS ====================

    public function belongsToClient()
    {
        return !is_null($this->client_id);
    }

    public function getClientNameAttribute()
    {
        return $this->client ? $this->client->name : null;
    }

    // Get projects accessible to this user
    public function getAccessibleProjects()
    {
        // Inactive users cannot access any projects
        if ($this->isInactive()) {
            return collect();
        }

        if ($this->isSuperAdmin() || $this->isAdmin()) {
            return Projects::all();
        }

        if ($this->isManager()) {
            return $this->managedProjects()->get();
        }

        if ($this->isTeamMember()) {
            return $this->teamProjects()->get();
        }

        if ($this->isClient()) {
            return Projects::where('client_id', $this->client_id)->get();
        }

        return collect();
    }

    // Get users this user can message
    public function getMessageableUsers()
    {
        // Inactive users cannot message anyone
        if ($this->isInactive()) {
            return collect();
        }

        $users = User::where('id', '!=', $this->id)->active()->get();

        return $users->filter(function($user) {
            return $this->canMessage($user);
        });
    }

    // ==================== SCOPES ====================

    public function scopeClients($query)
    {
        return $query->where('role', 'client');
    }

    public function scopeTeamMembers($query)
    {
        return $query->where('role', 'user');
    }

    public function scopeManagers($query)
    {
        return $query->where('role', 'manager');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeSuperAdmins($query)
    {
        return $query->where('role', 'super_admin');
    }

    // ==================== ADDITIONAL HELPER METHODS ====================

    public function getRoleDisplayName()
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'manager' => 'Manager',
            'user' => 'Team Member',
            'client' => 'Client',
            default => ucfirst($this->role)
        };
    }

    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">Active</span>',
            'inactive' => '<span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">Inactive</span>',
            default => '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-medium">Unknown</span>'
        };
    }

    public function getStatusDisplayName()
    {
        return match($this->status) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            default => 'Unknown'
        };
    }
}