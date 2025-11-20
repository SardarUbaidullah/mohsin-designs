<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class task_subtasks extends Model
{
    use HasFactory;

    protected $table = 'task_subtasks';

    protected $fillable = [
        'task_id',
        'title',
        'status',
    ];

    public function task()
    {
        return $this->belongsTo(Tasks::class);
    }

    
}
