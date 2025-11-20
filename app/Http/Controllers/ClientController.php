<?php
// app/Http/Controllers/ClientController.php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Files;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function dashboard()
    {
        $client = auth()->user();

         $projects = Projects::where('client_id', $client->client_id)
        ->with(['manager'])
        ->withCount([
            'tasks',
            'tasks as completed_tasks_count' => function($query) {
                $query->where('status', 'done'); // or 'completed' depending on your status values
            }
        ])
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
        $recentActivities = $this->getRecentActivities($client);
        $upcomingDeadlines = $this->getUpcomingDeadlines($client);

        return view('Client.dashboard', compact('projects', 'recentActivities', 'upcomingDeadlines'));
    }

   public function projects()
{
    $client = auth()->user();
    
    $projects = Projects::where('client_id', $client->client_id)
        ->with(['manager', 'teamMembers'])
        ->withCount([
            'tasks',
            'tasks as completed_tasks_count' => function($query) {
                $query->where('status', 'done'); // or 'completed' based on your system
            }
        ])
        ->orderBy('created_at', 'desc')
        ->get();

    // Calculate total team members across all projects
    $totalTeamMembers = 0;
    foreach ($projects as $project) {
        $totalTeamMembers += $project->teamMembers ? $project->teamMembers->count() : 0;
    }

    return view('Client.projects.index', compact('projects', 'totalTeamMembers'));
}
  public function projectShow(Projects $project)
{
    $client = auth()->user();

    if ($project->client_id !== $client->client_id) {
        abort(403, 'Access denied');
    }

    $project->load([
        'manager',
        'tasks.assignedTo',
        'tasks.comments.user',
        'files',
        'comments.user'
    ]);

    return view('Client.projects.show', compact('project'));
}
    public function addProjectComment(Request $request, Projects $project)
    {
        $client = auth()->user();

        if ($project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Comment::create([
            'content' => $request->content,
            'user_id' => $client->id,
            'commentable_type' => Projects::class,
            'commentable_id' => $project->id,
            'is_internal' => false
        ]);

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function addTaskComment(Request $request, Tasks $task)
    {
        $client = auth()->user();

        if ($task->project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Comment::create([
            'content' => $request->content,
            'user_id' => $client->id,
            'commentable_type' => Tasks::class,
            'commentable_id' => $task->id,
            'is_internal' => false
        ]);

        return redirect()->back()->with('success', 'Comment added successfully');
    }

    public function downloadFile(Files $file)
    {
        $client = auth()->user();

        if ($file->project->client_id !== $client->client_id) {
            abort(403, 'Access denied');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

   private function getRecentActivities($client)
{
    // Get project comments
    $projectComments = Comment::whereHasMorph(
        'commentable',
        [Projects::class],
        function($query) use ($client) {
            $query->where('client_id', $client->client_id);
        }
    )->with(['user', 'commentable'])->latest()->limit(10)->get();

    // Get task comments
    $taskComments = Comment::whereHasMorph(
        'commentable',
        [Tasks::class],
        function($query) use ($client) {
            $query->whereHas('project', function($q) use ($client) {
                $q->where('client_id', $client->client_id);
            });
        }
    )->with(['user', 'commentable.project'])->latest()->limit(10)->get();

    // Merge and format activities
    $activities = $projectComments->merge($taskComments)
        ->sortByDesc('created_at')
        ->take(5)
        ->map(function($comment) {
            return [
                'type' => 'comment',
                'message' => $comment->user->name . ' commented on ' . 
                    ($comment->commentable_type === Projects::class ? 
                     'project "' . $comment->commentable->name . '"' : 
                     'task "' . $comment->commentable->title . '" in project "' . $comment->commentable->project->name . '"'),
                'time' => $comment->created_at,
                'user' => $comment->user
            ];
        });

    return $activities;
}
    private function getUpcomingDeadlines($client)
    {
        return Tasks::whereHas('project', function($query) use ($client) {
            $query->where('client_id', $client->client_id);
        })
        ->where('due_date', '>=', now())
        ->whereIn('status', ['todo', 'in_progress'])
        ->orderBy('due_date')
        ->limit(5)
        ->get();
    }
}
