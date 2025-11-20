<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Milestones extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'due_date',
        'status',
    ];

    protected $dates = [
        'due_date',
    ];

    public function project()
    {
        return $this->belongsTo(Projects::class);
    }

     public function tasks()
    {
        return $this->hasMany(Tasks::class, 'milestone_id'); // Explicitly specify foreign key
    }

   public function completedTasks()
    {
        return $this->hasMany(Tasks::class, 'milestone_id')->where('status', 'done');
    }

    public function getProgressAttribute()
    {
        $totalTasks = $this->tasks()->count();
        $completedTasks = $this->completedTasks()->count();

        return $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
    }

    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status != 'completed';
    }
}
