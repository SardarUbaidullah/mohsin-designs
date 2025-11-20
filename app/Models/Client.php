<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // A client can have multiple users (login accounts)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // A client can have multiple projects
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    // Get active projects
    public function activeProjects()
    {
        return $this->projects()->where('status', 'active');
    }

    // Get completed projects
    public function completedProjects()
    {
        return $this->projects()->where('status', 'completed');
    }

    // Scope for active clients
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Check if client is active
    public function isActive()
    {
        return $this->status === 'active';
    }
}
