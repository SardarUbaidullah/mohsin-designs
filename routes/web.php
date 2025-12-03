<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\TimeReportController;
use App\Http\Controllers\Admin\TimeTrackingController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\MilestoneController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TeamOwnController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TeamChatController;
use App\Http\Controllers\Manager\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SubTaskController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Projects as Project;


// Manager Controllers
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ProjectController as ManagerProjectController;
use App\Http\Controllers\Manager\TaskController as ManagerTaskController;
use App\Http\Controllers\Manager\TeamController as manager_TeamController;
use App\Http\Controllers\Manager\SubTaskController as ManagerSubTaskController;

Route::get('/test-chat-notifications', [ChatController::class, 'testChatNotification'])->middleware('auth');Route::get('/chat/updates', [ChatController::class, 'getChatListUpdates'])->name('manager.chat.updates');
Route::post('/chat/rooms/{chatRoom}/mark-read', [ChatController::class, 'markRoomAsRead'])->name('manager.chat.room.mark-read');
// Notification routes
// Notifications Routes
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::get('/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::delete('/clear', [NotificationController::class, 'clearAll'])->name('notifications.clear');
});


// Add this temporary route for testing

Route::middleware('auth')->group(function () {
    // Custom Profile Routes - Use Blade template instead of Inertia
    Route::get('/profile', function () {
        return view('profile.custom-profile');
    })->name('profile.edit');

    // Profile update routes
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile/photo', [ProfileController::class, 'updateProfilePhoto'])->name('profile.photo.update');
    Route::delete('/profile/photo', [ProfileController::class, 'deleteProfilePhoto'])->name('profile.photo.delete');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
// Calendar Routes

// In web.php
Route::middleware(['auth'])->group(function () {
    // Comment routes
    Route::post('/projects/{project}/comments', [CommentController::class, 'storeProjectComment'])->name('comments.project.store');
    Route::post('/tasks/{task}/comments', [CommentController::class, 'storeTaskComment'])->name('comments.task.store');
    Route::post('/files/{file}/comments', [CommentController::class, 'storeFileComment'])->name('comments.file.store');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // API routes for dynamic loading
    Route::get('/comments', [CommentController::class, 'getComments'])->name('comments.get');

});
// Comment routes





 Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Manager routes
Route::prefix('chat')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('manager.chat.index');
    Route::get('/project/{project}', [ChatController::class, 'projectChat'])->name('manager.chat.project');
    Route::get('/user/{user}', [ChatController::class, 'directChat'])->name('manager.chat.direct');
    Route::post('/{chatRoom}/send', [ChatController::class, 'sendMessage'])->name('manager.chat.send');
    Route::post('/{chatRoom}/read', [ChatController::class, 'markAsRead'])->name('manager.chat.read');
    Route::get('/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('manager.chat.messages');
});

// Chat sync routes
Route::get('/chat/rooms/{chatRoom}/messages', [ChatController::class, 'getMessagesForSync'])->name('manager.chat.messages.sync');
Route::post('/chat/rooms/{chatRoom}/messages/{message}/read', [ChatController::class, 'markMessageAsRead'])->name('manager.chat.message.read');

Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

// Team routes
Route::prefix('team')->name('team.')->group(function () {
    Route::get('/', [TeamOwnController::class, 'index'])->name('index');
    Route::get('/tasks', [TeamOwnController::class, 'tasks'])->name('tasks.index');
    Route::get('/tasks/{id}', [TeamOwnController::class, 'showTask'])->name('tasks.show');
     Route::post('/tasks/{task}/complete-task', [TeamOwnController::class, 'completeTask'])->name('tasks.complete-task');
    Route::post('/tasks/{task}/update-status', [TeamOwnController::class, 'updateStatus'])->name('tasks.update-status');
    Route::get('/projects', [TeamOwnController::class, 'projects'])->name('projects');
    Route::post('/tasks/{task}/complete', [TeamOwnController::class, 'complete'])->name('tasks.complete');

    // Team chat routes - using same controller but different route names
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/project/{project}', [ChatController::class, 'projectChat'])->name('project');
        Route::get('/user/{user}', [ChatController::class, 'directChat'])->name('direct');
        Route::post('/{chatRoom}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{chatRoom}/read', [ChatController::class, 'markAsRead'])->name('read');
        Route::get('/{chatRoom}/messages', [ChatController::class, 'getMessages'])->name('messages');
    });
});

Route::resource('milestones', MilestoneController::class);

// Pusher Authentication Route
Route::post('/pusher/auth', function (Request $request) {
    $user = auth()->user();

    if (!$user) {
        return response('Unauthorized', 401);
    }

    $channelName = $request->channel_name;
    $socketId = $request->socket_id;

    // Check if user can access this channel
    if (str_starts_with($channelName, 'private-chat.room.')) {
        $roomId = str_replace('private-chat.room.', '', $channelName);

        // Check if user has access to this chat room
        $hasAccess = \App\Models\ChatRoom::where('id', $roomId)
            ->whereHas('participants', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->exists();

        if (!$hasAccess) {
            return response('Forbidden', 403);
        }
    }

    // Generate auth response
    $pusher = new Pusher\Pusher(
        config('broadcasting.connections.pusher.key'),
        config('broadcasting.connections.pusher.secret'),
        config('broadcasting.connections.pusher.app_id'),
        config('broadcasting.connections.pusher.options')
    );

    return $pusher->authorizeChannel($channelName, $socketId);
})->middleware('auth')->name('pusher.auth');
/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function(){


    Route::get('/calendar', [App\Http\Controllers\Manager\CalendarController::class, 'index'])->name('manager.calendar.index');
Route::get('/calendar/events', [App\Http\Controllers\Manager\CalendarController::class, 'getEvents'])->name('manager.calendar.events');


 Route::resource('files', FileController::class);
    Route::get('/files/{id}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{id}/preview', [FileController::class, 'preview'])->name('files.preview');

     Route::get('/files/{id}/new-version', [FileController::class, 'showNewVersionForm'])->name('files.new-version-form');
    Route::post('/files/{id}/new-version', [FileController::class, 'newVersion'])->name('files.new-version');
    // Admin - Users
Route::resource('users', UserController::class);

    // File access management routes
Route::get('/files/{id}/access', [FileController::class, 'manageAccess'])->name('files.manage-access');
Route::post('/files/{id}/access', [FileController::class, 'updateAccess'])->name('files.update-access');

});



Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::resource('projects', ProjectController::class);

//main dashboard controls
 Route::get('/users/{user}/tasks', [UserController::class, 'userTasks'])->name('users.tasks');
    Route::get('/users/{user}/projects', [UserController::class, 'userProjects'])->name('users.projects');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    
    // Project specific routes
    Route::get('/projects/{project}/active-tasks', [ProjectController::class, 'activeTasks'])->name('projects.tasks.active');
    


    // Profile
// Super Admin Task Routes
Route::get('/tasks/pending', [TaskController::class, 'pendingTasks'])->name('tasks.pending');
Route::get('/tasks/completed', [TaskController::class, 'completedTasks'])->name('tasks.completed');
Route::post('/tasks/{id}/complete', [TaskController::class, 'markAsComplete'])->name('tasks.complete');
Route::post('/tasks/{id}/in-progress', [TaskController::class, 'markAsInProgress'])->name('tasks.in-progress');
Route::get('/projects/{projectId}/milestones', [TaskController::class, 'getMilestones'])->name('tasks.milestones');
Route::post('/tasks/{id}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
  // User status management routes
// User status management routes
Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
Route::post('/users/bulk-status-update', [UserController::class, 'bulkStatusUpdate'])->name('users.bulk-status-update');
Route::get('/users/status-stats', [UserController::class, 'getStatusStats'])->name('users.status-stats');

    // Teams
    Route::resource('teams', TeamController::class);

    // Tasks
    Route::resource('tasks', TaskController::class);

    // Subtasks
    Route::resource('subtasks', SubTaskController::class);

    // Milestones
    // Route::resource('milestones', MilestoneController::class);

    // Time Logs
    Route::resource('time-logs', TimeLogController::class);

    // Files





});


// Reports Routes for Super Admin
   // routes/web.php


   Route::middleware(['auth', 'super_admin'])->group(function () {
    // Categories Routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
});
Route::prefix('admin')->middleware(['auth', 'super_admin'])->group(function () {
    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::get('/quick-stats', [AdminReportController::class, 'quickStats'])->name('admin.reports.quick-stats');
         Route::patch('/users/{user}/toggle-project-permission', [UserController::class, 'toggleProjectPermission'])
         ->name('admin.users.toggle-project-permission');
        Route::get('/data/{type}', [AdminReportController::class, 'getReportData'])->name('admin.reports.data');
    });
});
// routes/web.php
Route::get('/admin/reports/test', function() {
    return response()->json([
        'message' => 'Reports API is working',
        'total_projects' => \App\Models\Projects::count(),
        'total_tasks' => \App\Models\Tasks::count(),
        'total_users' => \App\Models\User::count()
    ]);
});
/*
|--------------------------------------------------------------------------
| Manager Routes
|--------------------------------------------------------------------------
*/

// routes/web.php
// In your manager routes group
Route::get('/tasks/projects/{project}/milestones', [TaskController::class, 'getMilestones'])
    ->name('manager.tasks.milestones');

Route::prefix('manager')
    ->name('manager.')
    ->middleware(['auth'])
    ->group(function () {

        // Dashboard
//milestone

   Route::get('/projects/create', [ManagerProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ManagerProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects', [ManagerProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/edit/{id}', [ManagerProjectController::class, 'edit'])->name('projects.edit');    
Route::put('/projects/update/{id}', [ManagerProjectController::class, 'update'])->name('projects.update');    
Route::get('/projects/delete/{id}', [ManagerProjectController::class, 'destroy'])->name('projects.destroy');



    Route::get('/projects/{id}', [ManagerProjectController::class, 'show'])->name('projects.show');
Route::resource('milestones', MilestoneController::class);


Route::get('/tasks/projects/{project}/milestones', [ManagerTaskController::class, 'getMilestones'])
    ->name('manager.tasks.milestones');
    // In your manager routes group
Route::put('/tasks/{task}/status', [TaskController::class, 'updateStatus'])
    ->name('manager.tasks.update-status');
        // Projects
        // Manager Project Routes
        Route::patch('/projects/{project}/status', [ManagerProjectController::class, 'updateStatus'])
    ->name('projects.updateStatus');
// Route::post('/projects/{project}/update-status', [ManagerProjectController::class, 'updateStatus'])->name('projects.update-status');


        // Tasks
        Route::get('/tasks/pending', [ManagerTaskController::class, 'pendingTasks'])->name('tasks.pending');
Route::get('/tasks/completed', [ManagerTaskController::class, 'completedTasks'])->name('tasks.completed');
Route::post('/tasks/{id}/complete', [ManagerTaskController::class, 'markAsComplete'])->name('tasks.complete');
Route::post('/tasks/{id}/progress', [ManagerTaskController::class, 'markAsInProgress'])->name('tasks.progress');
        // Teams
        Route::resource('tasks', ManagerTaskController::class);

        // Teams
Route::get('/team', [manager_TeamController::class, 'index'])->name('team.index');
 Route::get('/team/{id}', [manager_TeamController::class, 'show'])->name('team.show');
  Route::get('/project/{project}/team', [manager_TeamController::class, 'projectTeam'])
        ->name('project.team');
        // Subtasks (linked to tasks)
        Route::prefix('tasks/{taskId}')->group(function () {
            Route::resource('subtasks', ManagerSubTaskController::class);
        });
    });




Route::prefix('attendance')->group(function () {
    Route::get('/clear-page', function() {
        return view('admin.attendance.clear');
    })->name('attendance.clear.page');
    Route::post('/clear', [AttendanceController::class, 'clearAllRecords'])
        ->name('attendance.mark.clear');
    Route::get('/test', function() {
        return response()->json([
            'message' => 'Test route working',
            'timestamp' => now()
        ]);
    })->name('attendance.test');
});


// routes/web.php

Route::prefix('client')->name('client.')->middleware(['auth', 'client.access'])->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [ClientController::class, 'projects'])->name('projects');
    Route::get('/projects/{project}', [ClientController::class, 'projectShow'])->name('projects.show');

    // Comments
    Route::post('/projects/{project}/comments', [ClientController::class, 'addProjectComment'])->name('projects.comments.store');
    Route::post('/tasks/{task}/comments', [ClientController::class, 'addTaskComment'])->name('tasks.comments.store');

    // Files
    Route::get('/files/{file}/download', [ClientController::class, 'downloadFile'])->name('files.download');
});


Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Professional Time Analytics
    // ... other routes ...

  // In web.php or your route file
    Route::prefix('time-reports')->group(function () {
        Route::get('/', [TimeReportController::class, 'index'])->name('admin.time-reports');
        Route::get('/summary', [TimeReportController::class, 'getTimeSummary'])->name('admin.time-reports.summary');
        Route::get('/project-duration', [TimeReportController::class, 'getProjectDurationReport'])->name('admin.time-reports.project-duration');
        Route::get('/detailed', [TimeReportController::class, 'getDetailedReport'])->name('admin.time-reports.detailed');
    });



   Route::post('/time-tracking/start-timer', [TimeTrackingController::class, 'startTimer']);
        Route::post('/time-tracking/stop-timer', [TimeTrackingController::class, 'stopTimer']);
        Route::get('/time-tracking/running-timer', [TimeTrackingController::class, 'getRunningTimer']);
        Route::get('/time-tracking//task-logs/{taskId}', [TimeTrackingController::class, 'getTaskTimeLogs']);
        Route::post('/time-tracking//manual-entry', [TimeTrackingController::class, 'manualEntry']);
        Route::delete('/time-tracking//delete-log/{id}', [TimeTrackingController::class, 'deleteTimeLog']);
    // Existing Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('admin.reports');
        Route::get('/quick-stats', [AdminReportController::class, 'quickStats']);
        Route::get('/data/{type}', [AdminReportController::class, 'getReportData']);
    });
});



require __DIR__ . '/auth.php';
