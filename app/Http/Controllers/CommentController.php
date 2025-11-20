<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\Files;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\NotificationService;

class CommentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // ==================== STORE METHODS ====================

    public function storeProjectComment(Request $request, Projects $project)
    {
        // Use 'create' instead of 'comment' or check if your policy has 'comment' method
        $this->authorize('create', [Comment::class, $project]);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'sometimes|boolean'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'commentable_type' => Projects::class,
            'commentable_id' => $project->id,
            'is_internal' => $this->determineInternalStatus($validated['is_internal'] ?? null)
        ]);

        // NOTIFICATION: New project comment
        $this->notifyProjectComment($comment, $project, Auth::user());

        return $this->redirectBackWithSuccess('Comment added successfully');
    }

    public function storeTaskComment(Request $request, Tasks $task)
    {
        $this->authorize('create', [Comment::class, $task]);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'sometimes|boolean'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'commentable_type' => Tasks::class,
            'commentable_id' => $task->id,
            'is_internal' => $this->determineInternalStatus($validated['is_internal'] ?? null)
        ]);

        // NOTIFICATION: New task comment
        $this->notifyTaskComment($comment, $task, Auth::user());

        return $this->redirectBackWithSuccess('Comment added successfully');
    }

    public function storeFileComment(Request $request, Files $file)
    {
        $this->authorize('create', [Comment::class, $file]);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'is_internal' => 'sometimes|boolean'
        ]);

        $comment = Comment::create([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'commentable_type' => Files::class,
            'commentable_id' => $file->id,
            'is_internal' => $this->determineInternalStatus($validated['is_internal'] ?? null)
        ]);

        // NOTIFICATION: New file comment
        $this->notifyFileComment($comment, $file, Auth::user());

        return $this->redirectBackWithSuccess('Comment added successfully');
    }

    // ==================== UPDATE & DELETE ====================

    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $oldContent = $comment->content;
        $comment->update(['content' => $validated['content']]);

        // NOTIFICATION: Comment updated
        $this->notifyCommentUpdated($comment, Auth::user(), $oldContent);

        return $this->redirectBackWithSuccess('Comment updated successfully');
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        // Store comment info for notification before deletion
        $commentData = [
            'content' => $comment->content,
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
            'user_name' => $comment->user->name
        ];

        // NOTIFICATION: Comment deleted
        $this->notifyCommentDeleted($comment, Auth::user());

        $comment->delete();

        return $this->redirectBackWithSuccess('Comment deleted successfully');
    }

    // ==================== HELPER METHODS ====================

    private function determineInternalStatus($requestedStatus)
    {
        $user = Auth::user();

        // Clients can only post public comments
        if ($user->role === 'client') {
            return false;
        }

        // For team members, use requested status or default to public for safety
        return $requestedStatus ?? false;
    }

    private function redirectBackWithSuccess($message)
    {
        return redirect()->back()->with('success', $message);
    }

    // ==================== NOTIFICATION METHODS ====================

    /**
     * Notify when new project comment is added
     */
    private function notifyProjectComment($comment, $project, $commentedBy)
    {
        $isInternal = $comment->is_internal;
        $commentPreview = $this->getCommentPreview($comment->content);

        // Notify project manager (if not the commenter)
        if ($project->manager_id !== $commentedBy->id) {
            $this->notificationService->sendToUser($project->manager_id, 'project_comment_added', [
                'title' => 'New Comment on Project',
                'message' => "{$commentedBy->name} commented on project '{$project->name}': {$commentPreview}",
                'action_url' => route('projects.show', $project) . '#comments',
                'icon' => 'fas fa-comment',
                'color' => 'blue',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'commented_by' => $commentedBy->name,
                'comment_preview' => $commentPreview,
                'is_internal' => $isInternal,
            ]);
        }

        // Notify team members (except commenter)
        $teamMembersToNotify = $project->teamMembers->where('id', '!=', $commentedBy->id);

        foreach ($teamMembersToNotify as $member) {
            // Skip if internal comment and user doesn't have access to internal comments
            if ($isInternal && !$this->canViewInternalComments($member)) {
                continue;
            }

            $this->notificationService->sendToUser($member->id, 'project_comment_added', [
                'title' => 'New Project Comment',
                'message' => "{$commentedBy->name} commented on project '{$project->name}': {$commentPreview}",
                'action_url' => route('projects.show', $project) . '#comments',
                'icon' => 'fas fa-comment',
                'color' => 'blue',
                'project_id' => $project->id,
                'project_name' => $project->name,
                'commented_by' => $commentedBy->name,
                'comment_preview' => $commentPreview,
                'is_internal' => $isInternal,
            ]);
        }

        // Notify mentioned users
        $this->notifyMentionedUsers($comment, $project, $commentedBy, 'project');
    }

    /**
     * Notify when new task comment is added
     */
    private function notifyTaskComment($comment, $task, $commentedBy)
    {
        $isInternal = $comment->is_internal;
        $commentPreview = $this->getCommentPreview($comment->content);

        // Notify task assignee (if not the commenter)
        if ($task->assigned_to && $task->assigned_to !== $commentedBy->id) {
            $this->notificationService->sendToUser($task->assigned_to, 'task_comment_added', [
                'title' => 'New Comment on Your Task',
                'message' => "{$commentedBy->name} commented on your task '{$task->title}': {$commentPreview}",
                'action_url' => route('tasks.show', $task) . '#comments',
                'icon' => 'fas fa-tasks',
                'color' => 'green',
                'task_id' => $task->id,
                'task_title' => $task->title,
                'commented_by' => $commentedBy->name,
                'comment_preview' => $commentPreview,
                'is_internal' => $isInternal,
            ]);
        }

        // Notify task creator (if not the commenter and not the assignee)
        if ($task->created_by && $task->created_by !== $commentedBy->id && $task->created_by !== $task->assigned_to) {
            $this->notificationService->sendToUser($task->created_by, 'task_comment_added', [
                'title' => 'New Comment on Task',
                'message' => "{$commentedBy->name} commented on task '{$task->title}': {$commentPreview}",
                'action_url' => route('tasks.show', $task) . '#comments',
                'icon' => 'fas fa-tasks',
                'color' => 'green',
                'task_id' => $task->id,
                'task_title' => $task->title,
                'commented_by' => $commentedBy->name,
                'comment_preview' => $commentPreview,
                'is_internal' => $isInternal,
            ]);
        }

        // Notify project manager (if not the commenter)
        if ($task->project->manager_id !== $commentedBy->id) {
            $this->notificationService->sendToUser($task->project->manager_id, 'task_comment_added', [
                'title' => 'New Comment on Task',
                'message' => "{$commentedBy->name} commented on task '{$task->title}' in project '{$task->project->name}': {$commentPreview}",
                'action_url' => route('tasks.show', $task) . '#comments',
                'icon' => 'fas fa-tasks',
                'color' => 'green',
                'task_id' => $task->id,
                'task_title' => $task->title,
                'project_name' => $task->project->name,
                'commented_by' => $commentedBy->name,
                'comment_preview' => $commentPreview,
                'is_internal' => $isInternal,
            ]);
        }

        // Notify mentioned users
        $this->notifyMentionedUsers($comment, $task, $commentedBy, 'task');
    }

    /**
     * Notify when new file comment is added
     */
    private function notifyFileComment($comment, $file, $commentedBy)
    {
        $isInternal = $comment->is_internal;
        $commentPreview = $this->getCommentPreview($comment->content);

        $context = $file->project ? "project '{$file->project->name}'" : "general files";

        if ($file->project) {
            // Project file comment - notify project team
            $this->notificationService->sendToTeamMembers($file->project, 'file_comment_added', [
                'title' => 'New Comment on File',
                'message' => "{$commentedBy->name} commented on file '{$file->file_name}': {$commentPreview}",
                'action_url' => route('files.show', $file) . '#comments',
                'icon' => 'fas fa-file',
                'color' => 'purple',
                'file_name' => $file->file_name,
                'project_name' => $file->project->name,
                'commented_by' => $commentedBy->name,
                'comment_preview' => $commentPreview,
                'is_internal' => $isInternal,
            ]);
        } else {
            // General file comment - notify users with access
            $accessibleUserIds = $file->getAccessibleUsers();
            foreach ($accessibleUserIds as $userId) {
                if ($userId != $commentedBy->id) {
                    $this->notificationService->sendToUser($userId, 'file_comment_added', [
                        'title' => 'New Comment on Shared File',
                        'message' => "{$commentedBy->name} commented on file '{$file->file_name}': {$commentPreview}",
                        'action_url' => route('files.show', $file) . '#comments',
                        'icon' => 'fas fa-file',
                        'color' => 'purple',
                        'file_name' => $file->file_name,
                        'commented_by' => $commentedBy->name,
                        'comment_preview' => $commentPreview,
                        'is_internal' => $isInternal,
                    ]);
                }
            }
        }

        // Notify mentioned users
        $this->notifyMentionedUsers($comment, $file, $commentedBy, 'file');
    }

    /**
     * Notify when comment is updated
     */
    private function notifyCommentUpdated($comment, $updatedBy, $oldContent)
    {
        $commentable = $comment->commentable;
        $commentPreview = $this->getCommentPreview($comment->content);
        $oldCommentPreview = $this->getCommentPreview($oldContent);

        $notificationData = [
            'title' => 'Comment Updated',
            'message' => "{$updatedBy->name} updated their comment: {$commentPreview}",
            'action_url' => $this->getCommentableUrl($commentable) . '#comments',
            'icon' => 'fas fa-edit',
            'color' => 'yellow',
            'commented_by' => $updatedBy->name,
            'comment_preview' => $commentPreview,
            'old_comment_preview' => $oldCommentPreview,
        ];

        // Add context-specific data
        if ($commentable instanceof Projects) {
            $notificationData['project_id'] = $commentable->id;
            $notificationData['project_name'] = $commentable->name;
            $this->notificationService->sendToTeamMembers($commentable, 'comment_updated', $notificationData);
        } elseif ($commentable instanceof Tasks) {
            $notificationData['task_id'] = $commentable->id;
            $notificationData['task_title'] = $commentable->title;
            // Notify task assignee and creator
            $usersToNotify = array_unique([$commentable->assigned_to, $commentable->created_by]);
            foreach ($usersToNotify as $userId) {
                if ($userId && $userId != $updatedBy->id) {
                    $this->notificationService->sendToUser($userId, 'comment_updated', $notificationData);
                }
            }
        } elseif ($commentable instanceof Files) {
            $notificationData['file_name'] = $commentable->file_name;
            // Notify users with file access
            $accessibleUserIds = $commentable->getAccessibleUsers();
            foreach ($accessibleUserIds as $userId) {
                if ($userId != $updatedBy->id) {
                    $this->notificationService->sendToUser($userId, 'comment_updated', $notificationData);
                }
            }
        }
    }

    /**
     * Notify when comment is deleted
     */
    private function notifyCommentDeleted($comment, $deletedBy)
    {
        $commentable = $comment->commentable;
        $commentPreview = $this->getCommentPreview($comment->content);

        $notificationData = [
            'title' => 'Comment Deleted',
            'message' => "{$deletedBy->name} deleted a comment: {$commentPreview}",
            'action_url' => $this->getCommentableUrl($commentable),
            'icon' => 'fas fa-trash',
            'color' => 'red',
            'deleted_by' => $deletedBy->name,
            'comment_preview' => $commentPreview,
        ];

        // Add context-specific data
        if ($commentable instanceof Projects) {
            $notificationData['project_id'] = $commentable->id;
            $notificationData['project_name'] = $commentable->name;
            $this->notificationService->sendToTeamMembers($commentable, 'comment_deleted', $notificationData);
        } elseif ($commentable instanceof Tasks) {
            $notificationData['task_id'] = $commentable->id;
            $notificationData['task_title'] = $commentable->title;
            // Notify task assignee and creator
            $usersToNotify = array_unique([$commentable->assigned_to, $commentable->created_by]);
            foreach ($usersToNotify as $userId) {
                if ($userId && $userId != $deletedBy->id) {
                    $this->notificationService->sendToUser($userId, 'comment_deleted', $notificationData);
                }
            }
        } elseif ($commentable instanceof Files) {
            $notificationData['file_name'] = $commentable->file_name;
            // Notify users with file access
            $accessibleUserIds = $commentable->getAccessibleUsers();
            foreach ($accessibleUserIds as $userId) {
                if ($userId != $deletedBy->id) {
                    $this->notificationService->sendToUser($userId, 'comment_deleted', $notificationData);
                }
            }
        }
    }

    /**
     * Notify mentioned users in comments
     */
    private function notifyMentionedUsers($comment, $commentable, $commentedBy, $type)
    {
        $mentionedUsers = $this->extractMentionedUsers($comment->content);

        if (empty($mentionedUsers)) {
            return;
        }

        $commentPreview = $this->getCommentPreview($comment->content);

        foreach ($mentionedUsers as $username) {
            $user = User::where('name', $username)->first();

            if ($user && $user->id !== $commentedBy->id) {
                $context = $this->getMentionContext($type, $commentable);

                $this->notificationService->sendToUser($user->id, 'mentioned_in_comment', [
                    'title' => 'You Were Mentioned',
                    'message' => "{$commentedBy->name} mentioned you in a comment on {$context}: {$commentPreview}",
                    'action_url' => $this->getCommentableUrl($commentable) . '#comments',
                    'icon' => 'fas fa-at',
                    'color' => 'pink',
                    'mentioned_by' => $commentedBy->name,
                    'comment_preview' => $commentPreview,
                    'context' => $context,
                ]);
            }
        }
    }

    /**
     * Extract mentioned users from comment content (@username format)
     */
    private function extractMentionedUsers($content)
    {
        preg_match_all('/@([\w\-\.]+)/', $content, $matches);
        return array_unique($matches[1] ?? []);
    }

    /**
     * Get comment preview (truncated)
     */
    private function getCommentPreview($content, $length = 100)
    {
        if (strlen($content) <= $length) {
            return $content;
        }

        return substr($content, 0, $length) . '...';
    }

    /**
     * Get URL for commentable resource
     */
    private function getCommentableUrl($commentable)
    {
        if ($commentable instanceof Projects) {
            return route('projects.show', $commentable);
        } elseif ($commentable instanceof Tasks) {
            return route('tasks.show', $commentable);
        } elseif ($commentable instanceof Files) {
            return route('files.show', $commentable);
        }

        return url('/');
    }

    /**
     * Get context for mention notification
     */
    private function getMentionContext($type, $commentable)
    {
        switch ($type) {
            case 'project':
                return "project '{$commentable->name}'";
            case 'task':
                return "task '{$commentable->title}'";
            case 'file':
                return "file '{$commentable->file_name}'";
            default:
                return 'a resource';
        }
    }

    /**
     * Check if user can view internal comments
     */
    private function canViewInternalComments($user)
    {
        return in_array($user->role, ['super_admin', 'admin', 'manager']);
    }

    // ==================== API METHODS (Optional) ====================

    public function getComments(Request $request)
    {
        $request->validate([
            'commentable_type' => 'required|string',
            'commentable_id' => 'required|integer'
        ]);

        $commentable = $this->getCommentableModel(
            $request->commentable_type,
            $request->commentable_id
        );

        if (!$commentable) {
            return response()->json(['error' => 'Resource not found'], 404);
        }

        // Check if user can view the commentable resource
        $this->authorize('view', $commentable);

        $user = Auth::user();
        $comments = $commentable->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($comment) use ($user) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                        'role' => $comment->user->role,
                        'avatar' => $this->getUserAvatar($comment->user)
                    ],
                    'is_internal' => $comment->is_internal,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'can_edit' => $user->can('update', $comment),
                    'can_delete' => $user->can('delete', $comment)
                ];
            });

        return response()->json(['comments' => $comments]);
    }

    private function getCommentableModel($type, $id)
    {
        switch ($type) {
            case 'project':
                return Projects::find($id);
            case 'task':
                return Tasks::find($id);
            case 'file':
                return Files::find($id);
            default:
                return null;
        }
    }

    private function getUserAvatar($user)
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
