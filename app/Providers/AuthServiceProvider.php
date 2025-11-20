<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Files;
use App\Models\Comment;
use App\Policies\ProjectCommentPolicy;
use App\Policies\TaskCommentPolicy;
use App\Policies\FileCommentPolicy;
use App\Policies\CommentPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Projects::class => ProjectCommentPolicy::class,
        Tasks::class => TaskCommentPolicy::class,
        Files::class => FileCommentPolicy::class,
        Comment::class => CommentPolicy::class, // Add this line
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
