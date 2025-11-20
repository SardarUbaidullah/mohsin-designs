<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Comments extends Component
{
    public $commentable;
    public $commentableType;
    public $showInternal;

    public function __construct($commentable, $commentableType = 'project', $showInternal = true)
    {
        $this->commentable = $commentable;
        $this->commentableType = $commentableType;
        $this->showInternal = $showInternal;
    }

    public function getStoreRoute()
    {
        return match($this->commentableType) {
            'project' => route('comments.project.store', $this->commentable),
            'task' => route('comments.task.store', $this->commentable),
            'file' => route('comments.file.store', $this->commentable),
            default => route('comments.project.store', $this->commentable)
        };
    }

    public function getUserColor($user)
    {
        $colors = [
            'super_admin' => 'bg-purple-500',
            'admin' => 'bg-red-500',
            'manager' => 'bg-orange-500',
            'user' => 'bg-blue-500',
            'client' => 'bg-green-500'
        ];
        return $colors[$user->role] ?? 'bg-gray-500';
    }

    public function getRoleBadgeColor($role)
    {
        $colors = [
            'super_admin' => 'bg-purple-100 text-purple-800',
            'admin' => 'bg-red-100 text-red-800',
            'manager' => 'bg-orange-100 text-orange-800',
            'user' => 'bg-blue-100 text-blue-800',
            'client' => 'bg-green-100 text-green-800'
        ];
        return $colors[$role] ?? 'bg-gray-100 text-gray-800';
    }

    public function render()
    {
        return view('components.comments');
    }
}
