<?php

namespace App\Http\Controllers;

use App\Models\Files;
use App\Models\Projects;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Providers\NotificationService;

class FileController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    // GET /files
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query for files
        $query = Files::with(['project','task','user','versions'])
            ->withCount(['versions as child_versions_count'])
            ->latestVersions()
            ->latest();

        // Role-based file access control
        if ($user->role === 'user') {
            // Team Member: Can see files from projects where they have tasks + accessible general files
            $query->where(function($q) use ($user) {
                // Project files where user has tasks
                $q->whereHas('project.tasks', function($taskQuery) use ($user) {
                    $taskQuery->where('assigned_to', $user->id);
                })->orWhere(function($q) use ($user) {
                    // General files that are accessible to this user
                    $q->whereNull('project_id')
                      ->where(function($generalQuery) use ($user) {
                          $generalQuery->where('is_public', true)
                                      ->orWhere('user_id', $user->id) // Uploader
                                      ->orWhereJsonContains('accessible_users', $user->id); // Explicit access
                      });
                });
            });
        } elseif ($user->role === 'admin') {
            // Manager: Can see files from projects they manage + accessible general files
            $query->where(function($q) use ($user) {
                // Project files from managed projects
                $q->whereHas('project', function($projectQuery) use ($user) {
                    $projectQuery->where('manager_id', $user->id);
                })->orWhere(function($q) use ($user) {
                    // General files that are accessible to this user
                    $q->whereNull('project_id')
                      ->where(function($generalQuery) use ($user) {
                          $generalQuery->where('is_public', true)
                                      ->orWhere('user_id', $user->id) // Uploader
                                      ->orWhereJsonContains('accessible_users', $user->id); // Explicit access
                      });
                });
            });
        }
        // Super Admin can see all files (no additional conditions)

        // Apply filters if any (for super admin and managers)
        if ($user->role !== 'user' && $request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($user->role !== 'user' && $request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($user->role !== 'user' && $request->filled('type')) {
            if ($request->type === 'project') {
                $query->whereNotNull('project_id');
            } elseif ($request->type === 'general') {
                $query->whereNull('project_id');
            }
        }

        $files = $query->get();

        // Calculate stats based on accessible files
        $totalFiles = $files->count();
        $projectFiles = $files->whereNotNull('project_id')->count();
        $generalFiles = $files->whereNull('project_id')->count();

        // Calculate total versions across accessible files
        $accessibleFileIds = $files->pluck('id');
        $totalVersions = Files::whereIn('id', $accessibleFileIds)
            ->orWhereIn('parent_id', $accessibleFileIds)
            ->count();

        // Get projects and tasks based on user role
        if ($user->role === 'super_admin') {
            $projects = Projects::all();
            $tasks = Tasks::all();
        } elseif ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
            $tasks = Tasks::whereIn('project_id', $projects->pluck('id'))->get();
        } else {
            // Team member: get projects where they have tasks and tasks assigned to them
            $projects = Projects::whereHas('tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
            $tasks = Tasks::where('assigned_to', $user->id)->get();
        }

        return view('admin.files.index', compact(
            'files',
            'projects',
            'tasks',
            'totalFiles',
            'projectFiles',
            'generalFiles',
            'totalVersions'
        ));
    }

    // GET /files/create
    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $projects = Projects::all();
            $tasks = Tasks::all();
        } elseif ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
            $tasks = Tasks::whereIn('project_id', $projects->pluck('id'))->get();
        } else {
            // Team member: get projects where they have tasks and tasks assigned to them
            $projects = Projects::whereHas('tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
            $tasks = Tasks::where('assigned_to', $user->id)->get();
        }

        $users = User::all();

        return view('admin.files.create', compact('projects', 'tasks', 'users'));
    }

    // POST /files
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'task_id'    => ['nullable','exists:tasks,id'],
            'user_id'    => ['required','exists:users,id'],
            'file_path'  => ['required','file','max:10240'],
            'file_name'  => ['nullable','string','max:255'],
            'version'    => ['nullable','integer','min:1'],
            'description' => ['nullable','string','max:500'],
            'is_public'  => ['nullable','boolean'],
        ]);

        // Role-based validation for project/task assignment
        if ($validated['project_id']) {
            $project = Projects::find($validated['project_id']);

            if ($user->role === 'admin' && $project->manager_id !== $user->id) {
                return redirect()->back()->with('error', 'You can only upload files to projects you manage.');
            }

            if ($user->role === 'user') {
                // Team member can only upload to projects where they have tasks
                $hasTaskInProject = Tasks::where('project_id', $validated['project_id'])
                    ->where('assigned_to', $user->id)
                    ->exists();

                if (!$hasTaskInProject) {
                    return redirect()->back()->with('error', 'You can only upload files to projects where you have assigned tasks.');
                }

                // Validate task assignment if task_id is provided
                if ($validated['task_id']) {
                    $task = Tasks::find($validated['task_id']);
                    if ($task->assigned_to !== $user->id) {
                        return redirect()->back()->with('error', 'You can only upload files to tasks assigned to you.');
                    }
                }
            }
        }

        $uploaded = $request->file('file_path');
        $path = $uploaded->store('files', 'public');
        $fileName = $validated['file_name'] ?? $uploaded->getClientOriginalName();
        $version  = $validated['version'] ?? 1;

        $file = Files::create([
            'project_id' => $validated['project_id'] ?? null,
            'task_id'    => $validated['task_id'] ?? null,
            'user_id'    => $validated['user_id'],
            'file_name'  => $fileName,
            'file_path'  => $path,
            'file_size'  => $uploaded->getSize(),
            'mime_type'  => $uploaded->getMimeType(),
            'version'    => $version,
            'description' => $validated['description'] ?? null,
            'is_public'  => $validated['is_public'] ?? false,
        ]);

        // NOTIFICATION: File uploaded
        $this->notifyFileUploaded($file, $user);

        return redirect()->route('files.index')->with('success', 'File uploaded successfully!');
    }

    // GET /files/{id}
    public function show($id)
    {
        $file = Files::with(['project','task','user','versions.user'])->findOrFail($id);

        $userId = auth()->id();
        \Log::info('Show page access check:', [
            'file_id' => $file->id,
            'file_name' => $file->file_name,
            'user_id' => $userId,
            'user_role' => auth()->user()->role,
            'file_project_id' => $file->project_id,
            'file_is_public' => $file->is_public,
            'file_accessible_users' => $file->accessible_users,
            'file_user_id' => $file->user_id,
            'canAccess_result' => $file->canUserAccess($userId)
        ]);

        // Check access using the new method
        if (!$file->canUserAccess(auth()->id())) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to access this file.');
        }

        return view('admin.files.show', compact('file'));
    }

    // GET /files/{id}/edit
    public function edit($id)
    {
        $file = Files::findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to edit this file.');
        }

        $user = auth()->user();

        if ($user->role === 'super_admin') {
            $projects = Projects::all();
            $tasks = Tasks::all();
        } elseif ($user->role === 'admin') {
            $projects = Projects::where('manager_id', $user->id)->get();
            $tasks = Tasks::whereIn('project_id', $projects->pluck('id'))->get();
        } else {
            // Team member: get projects where they have tasks and tasks assigned to them
            $projects = Projects::whereHas('tasks', function($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })->get();
            $tasks = Tasks::where('assigned_to', $user->id)->get();
        }

        $users = User::all();

        return view('admin.files.edit', compact('file', 'projects', 'tasks', 'users'));
    }

    // PUT/PATCH /files/{id}
    public function update(Request $request, $id)
    {
        $file = Files::findOrFail($id);
        $user = auth()->user();

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to update this file.');
        }

        $validated = $request->validate([
            'project_id' => ['nullable','exists:projects,id'],
            'task_id'    => ['nullable','exists:tasks,id'],
            'user_id'    => ['required','exists:users,id'],
            'file_name'  => ['required','string','max:255'],
            'version'    => ['required','integer','min:1'],
            'description' => ['nullable','string','max:500'],
        ]);

        $oldFileName = $file->file_name;
        $oldProjectId = $file->project_id;

        $file->update($validated);

        // NOTIFICATION: File updated
        $this->notifyFileUpdated($file, $user, $oldFileName);

        return redirect()->route('files.show', $file->id)->with('success', 'File updated successfully!');
    }

    // DELETE /files/{id}
    public function destroy($id)
    {
        $file = Files::findOrFail($id);
        $user = auth()->user();

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to delete this file.');
        }

        // Store file info for notification before deletion
        $fileName = $file->file_name;
        $project = $file->project;

        // NOTIFICATION: File about to be deleted
        $this->notifyFileDeleted($file, $user);

        // Delete all versions first
        foreach ($file->versions as $version) {
            if ($version->file_path && Storage::disk('public')->exists($version->file_path)) {
                Storage::disk('public')->delete($version->file_path);
            }
            $version->delete();
        }

        // Delete main file
        if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
            Storage::disk('public')->delete($file->file_path);
        }

        $file->delete();

        return redirect()->route('files.index')->with('success', 'File and all versions deleted successfully!');
    }

    // Download file
    public function download($id)
    {
        $file = Files::findOrFail($id);

        // Check access using the new method
        if (!$file->canUserAccess(auth()->id())) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to download this file.');
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            return redirect()->route('files.index')->with('error', 'File not found on server.');
        }

        $filePath = Storage::disk('public')->path($file->file_path);

        // NOTIFICATION: File downloaded
        $this->notifyFileDownloaded($file, auth()->user());

        return response()->download($filePath, $file->file_name, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'attachment; filename="' . $file->file_name . '"',
        ]);
    }

    // Preview file
    public function preview($id)
    {
        $file = Files::findOrFail($id);

        if (!Storage::disk('public')->exists($file->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // NOTIFICATION: File previewed
        $this->notifyFilePreviewed($file, auth()->user());

        // For images and PDFs, return the file for preview
        if ($file->is_image || $file->is_pdf) {
            $fileContent = Storage::disk('public')->get($file->file_path);
            $response = response($fileContent, 200);
            $response->header('Content-Type', $file->mime_type);
            $response->header('Content-Disposition', 'inline; filename="' . $file->file_name . '"');
            return $response;
        }

        // For other file types, offer download
        return $this->download($id);
    }

    // GET /files/{id}/new-version - Show new version form
    public function showNewVersionForm($id)
    {
        $file = Files::with(['project','task','user'])->findOrFail($id);

        // Check access
        if (!$this->canAccessFile($file)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to upload new versions for this file.');
        }

        return view('admin.files.new-version', compact('file'));
    }

    // POST /files/{id}/new-version - Store new version
    public function newVersion(Request $request, $id)
    {
        $parentFile = Files::findOrFail($id);
        $user = auth()->user();

        // Check access
        if (!$this->canAccessFile($parentFile)) {
            return redirect()->route('files.index')->with('error', 'You do not have permission to upload new versions for this file.');
        }

        $validated = $request->validate([
            'file_path' => ['required','file','max:10240'],
            'description' => ['nullable','string','max:500'],
        ]);

        $uploaded = $request->file('file_path');
        $path = $uploaded->store('files', 'public');

        // Get next version number
        $maxVersion = Files::where('id', $parentFile->id)
            ->orWhere('parent_id', $parentFile->id)
            ->max('version');

        $newVersion = $maxVersion + 1;

        $file = Files::create([
            'project_id' => $parentFile->project_id,
            'task_id'    => $parentFile->task_id,
            'user_id'    => auth()->id(),
            'file_name'  => $uploaded->getClientOriginalName(),
            'file_path'  => $path,
            'file_size'  => $uploaded->getSize(),
            'mime_type'  => $uploaded->getMimeType(),
            'version'    => $newVersion,
            'parent_id'  => $parentFile->id,
            'description' => $validated['description'] ?? "Version {$newVersion} - " . ($uploaded->getClientOriginalName() !== $parentFile->file_name ? "File renamed to: " . $uploaded->getClientOriginalName() : "Updated file"),
        ]);

        // NOTIFICATION: New version uploaded
        $this->notifyNewVersionUploaded($file, $parentFile, $user);

        return redirect()->route('files.show', $parentFile->id)->with('success', "New version {$newVersion} uploaded successfully!");
    }

    // GET /files/{id}/access
    public function manageAccess($id)
    {
        $file = Files::with(['user'])->findOrFail($id);

        // Only super admin can manage access for general files
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('files.show', $file->id)->with('error', 'You do not have permission to manage file access.');
        }

        // Only allow access management for general files
        if ($file->project_id) {
            return redirect()->route('files.show', $file->id)->with('error', 'Access management is only available for general files.');
        }

        $allUsers = User::where('id', '!=', auth()->id())->get();
        $accessibleUsers = $file->getAccessibleUsers();

        return view('admin.files.manage-access', compact('file', 'allUsers', 'accessibleUsers'));
    }

    // POST /files/{id}/access
    public function updateAccess(Request $request, $id)
    {
        $file = Files::findOrFail($id);
        $user = auth()->user();

        // Only super admin can manage access for general files
        if (auth()->user()->role !== 'super_admin') {
            return redirect()->route('files.show', $file->id)->with('error', 'You do not have permission to manage file access.');
        }

        // Only allow access management for general files
        if ($file->project_id) {
            return redirect()->route('files.show', $file->id)->with('error', 'Access management is only available for general files.');
        }

        $validated = $request->validate([
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
            'is_public' => ['boolean'],
        ]);

        $oldAccessibleUsers = $file->getAccessibleUsers();
        $oldIsPublic = $file->is_public;

        // Convert user_ids to integers and ensure they're unique
        $userIds = [];
        if ($request->has('user_ids')) {
            $userIds = array_map('intval', $request->user_ids);
            $userIds = array_unique($userIds);
        }

        // Update public access
        $file->update([
            'is_public' => $request->boolean('is_public'),
            'accessible_users' => $userIds // Store as integers
        ]);

        // NOTIFICATION: File access updated
        $this->notifyFileAccessUpdated($file, $user, $oldIsPublic, $oldAccessibleUsers);

        return redirect()->route('files.show', $file->id)->with('success', 'File access updated successfully!');
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

    // ===========================
    // NOTIFICATION METHODS
    // ===========================

    /**
     * Notify when file is uploaded
     */
    private function notifyFileUploaded($file, $uploadedBy)
    {
        $context = $file->project ? "project '{$file->project->name}'" : "general files";

        if ($file->project) {
            // Project file - notify project team members
            $this->notificationService->sendToTeamMembers($file->project, 'file_uploaded', [
                'title' => 'New File Uploaded',
                'message' => "File '{$file->file_name}' has been uploaded to project '{$file->project->name}'",
                'action_url' => route('files.show', $file),
                'icon' => 'fas fa-file-upload',
                'color' => 'blue',
                'file_name' => $file->file_name,
                'file_size' => $this->formatFileSize($file->file_size),
                'uploaded_by' => $uploadedBy->name,
                'project_name' => $file->project->name,
            ]);

            // Notify project manager specifically
            if ($file->project->manager_id !== $uploadedBy->id) {
                $this->notificationService->sendToUser($file->project->manager_id, 'file_uploaded', [
                    'title' => 'New File in Your Project',
                    'message' => "File '{$file->file_name}' has been uploaded to your project '{$file->project->name}'",
                    'action_url' => route('files.show', $file),
                    'icon' => 'fas fa-file-upload',
                    'color' => 'blue',
                    'file_name' => $file->file_name,
                    'uploaded_by' => $uploadedBy->name,
                ]);
            }
        } else {
            // General file - notify super admin and accessible users
            if ($uploadedBy->role !== 'super_admin') {
                $superAdmin = User::where('role', 'super_admin')->first();
                if ($superAdmin) {
                    $this->notificationService->sendToUser($superAdmin->id, 'file_uploaded', [
                        'title' => 'New General File Uploaded',
                        'message' => "File '{$file->file_name}' has been uploaded to general files by {$uploadedBy->name}",
                        'action_url' => route('files.show', $file),
                        'icon' => 'fas fa-file-upload',
                        'color' => 'blue',
                        'file_name' => $file->file_name,
                        'uploaded_by' => $uploadedBy->name,
                    ]);
                }
            }

            // Notify users with access to this file
            if ($file->is_public || !empty($file->accessible_users)) {
                $accessibleUserIds = $file->is_public
                    ? User::where('id', '!=', $uploadedBy->id)->pluck('id')->toArray()
                    : $file->accessible_users;

                foreach ($accessibleUserIds as $userId) {
                    if ($userId != $uploadedBy->id) {
                        $this->notificationService->sendToUser($userId, 'file_uploaded', [
                            'title' => 'New File Available',
                            'message' => "File '{$file->file_name}' has been shared with you",
                            'action_url' => route('files.show', $file),
                            'icon' => 'fas fa-file-import',
                            'color' => 'green',
                            'file_name' => $file->file_name,
                            'shared_by' => $uploadedBy->name,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Notify when file is updated
     */
    private function notifyFileUpdated($file, $updatedBy, $oldFileName)
    {
        $fileNameChanged = $oldFileName !== $file->file_name;

        if ($file->project) {
            $this->notificationService->sendToTeamMembers($file->project, 'file_updated', [
                'title' => 'File Updated',
                'message' => $fileNameChanged
                    ? "File '{$oldFileName}' has been renamed to '{$file->file_name}' in project '{$file->project->name}'"
                    : "File '{$file->file_name}' has been updated in project '{$file->project->name}'",
                'action_url' => route('files.show', $file),
                'icon' => 'fas fa-edit',
                'color' => 'yellow',
                'file_name' => $file->file_name,
                'old_file_name' => $fileNameChanged ? $oldFileName : null,
                'updated_by' => $updatedBy->name,
                'project_name' => $file->project->name,
            ]);
        } else {
            // General file update notification
            $accessibleUserIds = $file->getAccessibleUsers();
            foreach ($accessibleUserIds as $userId) {
                if ($userId != $updatedBy->id) {
                    $this->notificationService->sendToUser($userId, 'file_updated', [
                        'title' => 'File Updated',
                        'message' => $fileNameChanged
                            ? "Shared file '{$oldFileName}' has been renamed to '{$file->file_name}'"
                            : "Shared file '{$file->file_name}' has been updated",
                        'action_url' => route('files.show', $file),
                        'icon' => 'fas fa-edit',
                        'color' => 'yellow',
                        'file_name' => $file->file_name,
                        'updated_by' => $updatedBy->name,
                    ]);
                }
            }
        }
    }

    /**
     * Notify when file is deleted
     */
    private function notifyFileDeleted($file, $deletedBy)
    {
        if ($file->project) {
            $this->notificationService->sendToTeamMembers($file->project, 'file_deleted', [
                'title' => 'File Deleted',
                'message' => "File '{$file->file_name}' has been deleted from project '{$file->project->name}'",
                'action_url' => route('files.index'),
                'icon' => 'fas fa-trash',
                'color' => 'red',
                'file_name' => $file->file_name,
                'deleted_by' => $deletedBy->name,
                'project_name' => $file->project->name,
            ]);

            // Notify project manager
            if ($file->project->manager_id !== $deletedBy->id) {
                $this->notificationService->sendToUser($file->project->manager_id, 'file_deleted', [
                    'title' => 'File Deleted from Your Project',
                    'message' => "File '{$file->file_name}' has been deleted from your project '{$file->project->name}'",
                    'action_url' => route('files.index'),
                    'icon' => 'fas fa-trash',
                    'color' => 'red',
                    'file_name' => $file->file_name,
                    'deleted_by' => $deletedBy->name,
                ]);
            }
        } else {
            // General file deletion notification
            $accessibleUserIds = $file->getAccessibleUsers();
            foreach ($accessibleUserIds as $userId) {
                if ($userId != $deletedBy->id) {
                    $this->notificationService->sendToUser($userId, 'file_deleted', [
                        'title' => 'Shared File Deleted',
                        'message' => "File '{$file->file_name}' that was shared with you has been deleted",
                        'action_url' => route('files.index'),
                        'icon' => 'fas fa-trash',
                        'color' => 'red',
                        'file_name' => $file->file_name,
                        'deleted_by' => $deletedBy->name,
                    ]);
                }
            }

            // Notify super admin if deleted by someone else
            if ($deletedBy->role !== 'super_admin') {
                $superAdmin = User::where('role', 'super_admin')->first();
                if ($superAdmin) {
                    $this->notificationService->sendToUser($superAdmin->id, 'file_deleted', [
                        'title' => 'General File Deleted',
                        'message' => "File '{$file->file_name}' has been deleted from general files by {$deletedBy->name}",
                        'action_url' => route('files.index'),
                        'icon' => 'fas fa-trash',
                        'color' => 'red',
                        'file_name' => $file->file_name,
                        'deleted_by' => $deletedBy->name,
                    ]);
                }
            }
        }
    }

    /**
     * Notify when new version is uploaded
     */
    private function notifyNewVersionUploaded($newVersion, $parentFile, $uploadedBy)
    {
        if ($parentFile->project) {
            $this->notificationService->sendToTeamMembers($parentFile->project, 'file_version_uploaded', [
                'title' => 'New File Version',
                'message' => "New version {$newVersion->version} of '{$newVersion->file_name}' has been uploaded",
                'action_url' => route('files.show', $parentFile),
                'icon' => 'fas fa-code-branch',
                'color' => 'purple',
                'file_name' => $newVersion->file_name,
                'version' => $newVersion->version,
                'uploaded_by' => $uploadedBy->name,
                'project_name' => $parentFile->project->name,
            ]);
        } else {
            // General file version notification
            $accessibleUserIds = $parentFile->getAccessibleUsers();
            foreach ($accessibleUserIds as $userId) {
                if ($userId != $uploadedBy->id) {
                    $this->notificationService->sendToUser($userId, 'file_version_uploaded', [
                        'title' => 'New File Version Available',
                        'message' => "New version {$newVersion->version} of '{$newVersion->file_name}' has been uploaded",
                        'action_url' => route('files.show', $parentFile),
                        'icon' => 'fas fa-code-branch',
                        'color' => 'purple',
                        'file_name' => $newVersion->file_name,
                        'version' => $newVersion->version,
                        'uploaded_by' => $uploadedBy->name,
                    ]);
                }
            }
        }
    }

    /**
     * Notify when file is downloaded
     */
    private function notifyFileDownloaded($file, $downloadedBy)
    {
        // Only notify if the downloader is not the file owner
        if ($file->user_id !== $downloadedBy->id) {
            $this->notificationService->sendToUser($file->user_id, 'file_downloaded', [
                'title' => 'File Downloaded',
                'message' => "Your file '{$file->file_name}' was downloaded by {$downloadedBy->name}",
                'action_url' => route('files.show', $file),
                'icon' => 'fas fa-download',
                'color' => 'green',
                'file_name' => $file->file_name,
                'downloaded_by' => $downloadedBy->name,
            ]);
        }
    }

    /**
     * Notify when file is previewed
     */
    private function notifyFilePreviewed($file, $previewedBy)
    {
        // Only notify if the previewer is not the file owner
        if ($file->user_id !== $previewedBy->id) {
            $this->notificationService->sendToUser($file->user_id, 'file_previewed', [
                'title' => 'File Previewed',
                'message' => "Your file '{$file->file_name}' was previewed by {$previewedBy->name}",
                'action_url' => route('files.show', $file),
                'icon' => 'fas fa-eye',
                'color' => 'blue',
                'file_name' => $file->file_name,
                'previewed_by' => $previewedBy->name,
            ]);
        }
    }

    /**
     * Notify when file access is updated
     */
    private function notifyFileAccessUpdated($file, $updatedBy, $oldIsPublic, $oldAccessibleUsers)
    {
        $newAccessibleUsers = $file->getAccessibleUsers();

        // Notify users who gained access
        $gainedAccess = array_diff($newAccessibleUsers, $oldAccessibleUsers);
        foreach ($gainedAccess as $userId) {
            $this->notificationService->sendToUser($userId, 'file_access_granted', [
                'title' => 'File Access Granted',
                'message' => "You now have access to file '{$file->file_name}'",
                'action_url' => route('files.show', $file),
                'icon' => 'fas fa-share-square',
                'color' => 'green',
                'file_name' => $file->file_name,
                'shared_by' => $updatedBy->name,
            ]);
        }

        // Notify users who lost access
        $lostAccess = array_diff($oldAccessibleUsers, $newAccessibleUsers);
        foreach ($lostAccess as $userId) {
            $this->notificationService->sendToUser($userId, 'file_access_revoked', [
                'title' => 'File Access Revoked',
                'message' => "Your access to file '{$file->file_name}' has been revoked",
                'action_url' => route('files.index'),
                'icon' => 'fas fa-ban',
                'color' => 'red',
                'file_name' => $file->file_name,
                'revoked_by' => $updatedBy->name,
            ]);
        }

        // Notify about public status change
        if ($oldIsPublic != $file->is_public) {
            if ($file->is_public) {
                $this->notificationService->sendToUser($file->user_id, 'file_made_public', [
                    'title' => 'File Made Public',
                    'message' => "Your file '{$file->file_name}' is now publicly accessible",
                    'action_url' => route('files.show', $file),
                    'icon' => 'fas fa-globe',
                    'color' => 'blue',
                    'file_name' => $file->file_name,
                ]);
            } else {
                $this->notificationService->sendToUser($file->user_id, 'file_made_private', [
                    'title' => 'File Made Private',
                    'message' => "Your file '{$file->file_name}' is now private",
                    'action_url' => route('files.show', $file),
                    'icon' => 'fas fa-lock',
                    'color' => 'yellow',
                    'file_name' => $file->file_name,
                ]);
            }
        }
    }

    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}
