@extends('admin.layouts.app')
@section('content')
    <!-- Dashboard Content -->
    <!-- System Alert -->
    <div id="systemAlert" class="hidden mb-6 p-4 rounded-lg border bg-primary border-primary text-white slide-in">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span class="font-medium" id="alertMessage">User created successfully!</span>
            </div>
            <button onclick="hideAlert()" class="hover:opacity-70 transition-opacity">
                <i class="fas fa-times-circle"></i>
            </button>
        </div>
    </div>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <i class="fas fa-shield-alt text-primary text-2xl"></i>
                <h1 class="text-3xl font-bold text-black">System Administration</h1>
            </div>
            <p class="text-gray-600">
                Monitor system health, manage users, and configure platform settings
            </p>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex space-x-1 bg-white rounded-lg p-1 border border-gray-200 mb-8">
        <button data-tab="overview"
            class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors bg-primary text-white">
            Overview
        </button>
        <button data-tab="users"
            class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100">
            Users
        </button>
        <button data-tab="projects"
            class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100">
            Projects
        </button>
        <button data-tab="tasks"
            class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100">
            Tasks
        </button>
        <button data-tab="files"
            class="tab-btn flex-1 px-4 py-2 rounded-md text-sm font-medium transition-colors text-gray-600 hover:text-black hover:bg-gray-100">
            Files
        </button>
    </div>

    <!-- Overview Tab -->
    <div id="overviewTab" class="tab-content space-y-8">
        <!-- System Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ url('/users') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-black">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-primary text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center space-x-1 mt-2">
                    <i class="fas fa-arrow-up text-primary text-xs"></i>
                    <span
                        class="text-xs text-primary">+{{ \App\Models\User::where('created_at', '>=', now()->subMonth())->count() }}
                        this month</span>
                </div>
            </a>

            <a href="{{ url('/projects') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Projects</p>
                        <p class="text-2xl font-bold text-black">{{ \App\Models\Projects::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-primary text-xl"></i>
                    </div>
                </div>
                <div class="flex items-center space-x-1 mt-2">
                    <i class="fas fa-arrow-up-right text-primary text-xs"></i>
                    <span class="text-xs text-primary">{{ \App\Models\Projects::where('status', 'in_progress')->count() }}
                        in progress</span>
                </div>
            </a>

            <a href="{{ url('/tasks') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Tasks</p>
                        <p class="text-2xl font-bold text-black">{{ \App\Models\Tasks::count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tasks text-secondary text-xl"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span
                        class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                        {{ \App\Models\Tasks::where('status', 'done')->count() }} completed
                    </span>
                </div>
            </a>

            <a href="{{ url('/files') }}" class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Files Uploaded</p>
                        <p class="text-2xl font-bold text-black">{{ \App\Models\Files::count() }}</p>
                        <p class="text-xs text-gray-500">
                            {{ round(
                                \App\Models\Files::all()->sum(function ($file) {
                                    return \Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path)
                                        ? \Illuminate\Support\Facades\Storage::disk('public')->size($file->file_path) / (1024 * 1024)
                                        : 0;
                                }),
                                1,
                            ) }}
                            MB total</p>
                    </div>
                    <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hdd text-accent text-xl"></i>
                    </div>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    @php
                        $totalFiles = \App\Models\Files::count();
                        $storagePercentage = $totalFiles > 0 ? min(($totalFiles / 100) * 100, 100) : 0;
                    @endphp
                    <div id="storageBar" class="h-2 rounded-full bg-accent" style="width: {{ $storagePercentage }}%"></div>
                </div>
            </a>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h2 class="text-xl font-semibold text-black mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <a href="{{ route('users.index') }}"
                    class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black text-center">
                        Manage Users
                    </span>
                    <span class="text-xs text-gray-500 text-center mt-1">
                        User accounts & permissions
                    </span>
                </a>

                <a href="{{ route('projects.index') }}"
                    class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-project-diagram text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black text-center">
                        Projects
                    </span>
                    <span class="text-xs text-gray-500 text-center mt-1">
                        Manage projects
                    </span>
                </a>

                <a href="{{ route('tasks.index') }}"
                    class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black text-center">
                        Tasks
                    </span>
                    <span class="text-xs text-gray-500 text-center mt-1">
                        Task management
                    </span>
                </a>

                <a href="{{ route('files.index') }}"
                    class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-upload text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black text-center">
                        Files
                    </span>
                    <span class="text-xs text-gray-500 text-center mt-1">
                        File management
                    </span>
                </a>

                <a href="{{ route('time-logs.index') }}"
                    class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black text-center">
                        Time Logs
                    </span>
                    <span class="text-xs text-gray-500 text-center mt-1">
                        Time tracking
                    </span>
                </a>

                <a href="{{ route('subtasks.index') }}"
                    class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                    <div
                        class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="fas fa-list-ul text-white text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-black text-center">
                        Subtasks
                    </span>
                    <span class="text-xs text-gray-500 text-center mt-1">
                        Subtask management
                    </span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- System Health -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">System Health</h3>
                    <span
                        class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        <i class="fas fa-check-circle mr-1 text-xs"></i>
                        Healthy
                    </span>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Projects Health -->
                    <div
                        class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-project-diagram text-primary"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-black">{{ \App\Models\Projects::count() }}</div>
                                <div class="flex items-center space-x-1 text-xs text-primary">
                                    <i class="fas fa-chart-line text-xs"></i>
                                    <span>active</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-black mb-1">Projects</h3>
                        <span
                            class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                            {{ \App\Models\Projects::where('status', 'completed')->count() }} completed
                        </span>
                    </div>

                    <!-- Tasks Health -->
                    <div
                        class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-primary"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-black">{{ \App\Models\Tasks::count() }}</div>
                                <div class="flex items-center space-x-1 text-xs text-gray-600">
                                    <span>total</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-black mb-1">Tasks</h3>
                        <span
                            class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                            {{ \App\Models\Tasks::where('status', 'done')->count() }} done
                        </span>
                    </div>

                    <!-- Files Health -->
                    <div
                        class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file text-primary"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-black">{{ \App\Models\Files::count() }}</div>
                                <div class="flex items-center space-x-1 text-xs text-primary">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                    <span>uploaded</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-black mb-1">Files</h3>
                        <span
                            class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                            Active
                        </span>
                    </div>

                    <!-- Time Tracking -->
                    <div
                        class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-clock text-primary"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-black">{{ \App\Models\TimeLog::count() }}</div>
                                <div class="flex items-center space-x-1 text-xs text-primary">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                    <span>logs</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-black mb-1">Time Logs</h3>

                    </div>

                    <!-- Subtasks -->
                    <div
                        class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-accent/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-list-ul text-accent"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-black">{{ \App\Models\task_subtasks::count() }}</div>
                                <div class="flex items-center space-x-1 text-xs text-primary">
                                    <i class="fas fa-arrow-up text-xs"></i>
                                    <span>total</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-black mb-1">Subtasks</h3>
                        <span
                            class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                            {{ \App\Models\task_subtasks::where('status', 'done')->count() }} done
                        </span>
                    </div>

                    <!-- Users -->
                    <div
                        class="health-metric bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover-scale transition-all-custom">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-primary/10 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-black">{{ \App\Models\User::count() }}</div>
                                <div class="flex items-center space-x-1 text-xs text-gray-600">
                                    <span>active</span>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-sm font-medium text-black mb-1">Users</h3>
                        <span
                            class="status-badge status-healthy inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1 text-xs"></i>
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">Recent Activity</h3>
                    <button class="text-sm text-primary hover:text-[#146c3e] font-medium">
                        View All
                    </button>
                </div>
                <div class="space-y-2 max-h-96 overflow-y-auto scrollbar-custom">
                    @php
                        $recentActivities = collect();

                        // Recent users
                        $recentUsers = \App\Models\User::latest()
                            ->take(3)
                            ->get()
                            ->map(function ($user) {
                                return [
                                    'type' => 'user',
                                    'message' => "New user registered: {$user->name}",
                                    'time' => $user->created_at,
                                    'icon' => 'fas fa-user-plus',
                                    'color' => 'text-primary',
                                ];
                            });

                        // Recent projects
                        $recentProjects = \App\Models\Projects::latest()
                            ->take(2)
                            ->get()
                            ->map(function ($project) {
                                return [
                                    'type' => 'project',
                                    'message' => "New project created: {$project->name}",
                                    'time' => $project->created_at,
                                    'icon' => 'fas fa-project-diagram',
                                    'color' => 'text-secondary',
                                ];
                            });

                        // Recent tasks
                        $recentTasks = \App\Models\tasks::latest()
                            ->take(2)
                            ->get()
                            ->map(function ($task) {
                                return [
                                    'type' => 'task',
                                    'message' => "New task created: {$task->title}",
                                    'time' => $task->created_at,
                                    'icon' => 'fas fa-tasks',
                                    'color' => 'text-accent',
                                ];
                            });

                        // Recent files
                        $recentFiles = \App\Models\Files::latest()
                            ->take(2)
                            ->get()
                            ->map(function ($file) {
                                return [
                                    'type' => 'file',
                                    'message' => "File uploaded: {$file->file_name}",
                                    'time' => $file->created_at,
                                    'icon' => 'fas fa-file-upload',
                                    'color' => 'text-primary',
                                ];
                            });

                        $recentActivities = $recentUsers
                            ->merge($recentProjects)
                            ->merge($recentTasks)
                            ->merge($recentFiles)
                            ->sortByDesc('time')
                            ->take(5);
                    @endphp

                    @forelse($recentActivities as $activity)
                        <div
                            class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center {{ $activity['color'] }} bg-gray-100">
                                <i class="{{ $activity['icon'] }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-black">
                                    <span class="font-semibold">{{ Auth::user()->name }}</span>
                                    <span class="text-gray-600"> {{ $activity['message'] }}</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity['time']->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-inbox text-gray-400 text-2xl mb-2"></i>
                            <p class="text-gray-500 text-sm">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Users Tab -->
    <div id="usersTab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-black">User Management</h2>
                    <a href="{{ route('users.create') }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors font-medium flex items-center space-x-2">
                        <i class="fas fa-user-plus"></i>
                        <span>Add User</span>
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (\App\Models\User::latest()->get() as $user)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-black">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if ($user->status == 'active') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($user->status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('users.tasks', $user->id) }}"
                                                class="text-primary hover:text-[#146c3e] px-2 py-1 rounded hover:bg-primary/10 transition-colors"
                                                title="View Tasks">
                                                <i class="fas fa-tasks text-xs"></i>
                                            </a>
                                            <a href="{{ route('users.projects', $user->id) }}"
                                                class="text-secondary hover:text-[#146c3e] px-2 py-1 rounded hover:bg-secondary/10 transition-colors"
                                                title="View Projects">
                                                <i class="fas fa-project-diagram text-xs"></i>
                                            </a>
                                            <form action="{{ route('users.toggle-status', $user) }}" method="POST"
                                                class="inline toggle-status-form flex-shrink-0">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $user->status === 'active' ? 'bg-green-600' : 'bg-red-600' }}"
                                                    role="switch"
                                                    aria-checked="{{ $user->status === 'active' ? 'true' : 'false' }}"
                                                    {{ $user->id === auth()->id() ? 'disabled' : '' }}
                                                    title="{{ $user->id === auth()->id() ? 'Cannot change your own status' : ($user->status === 'active' ? 'Deactivate User' : 'Activate User') }}">
                                                    <span class="sr-only">Toggle user status</span>
                                                    <span aria-hidden="true"
                                                        class="pointer-events-none inline-block h-4 w-4 sm:h-5 sm:w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->status === 'active' ? 'translate-x-4 sm:translate-x-5' : 'translate-x-0' }}"></span>
                                                </button>
                                            </form>
                                            <a href="{{ route('users.edit', $user->id) }}"
                                                class="text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-100 transition-colors"
                                                title="Edit">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Tab -->
    <div id="projectsTab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-black">Project Management</h2>
                    <a href="{{ route('projects.create') }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors font-medium flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Add Project</span>
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Project</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Manager</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Active Tasks</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Start Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Due Date</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (\App\Models\Projects::with(['manager', 'tasks'])->withCount([
                'tasks as active_tasks_count' => function ($query) {
                    $query->whereIn('status', ['todo', 'in_progress']);
                },
            ])->latest()->get() as $project)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-black">{{ $project->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $project->tasks_count }} total tasks</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $project->manager->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <a href="{{ route('projects.tasks.active', $project->id) }}"
                                            class="text-sm font-medium text-primary hover:text-[#146c3e] hover:underline">
                                            {{ $project->active_tasks_count }} active
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($project->status == 'completed') bg-green-100 text-green-800
                                    @elseif($project->status == 'in_progress') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('projects.show', $project->id) }}"
                                            class="text-primary hover:text-[#146c3e] mr-3">View</a>
                                        <a href="{{ route('projects.edit', $project->id) }}"
                                            class="text-primary hover:text-[#146c3e] mr-3">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Tab -->
    <div id="tasksTab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-black">Task Management</h2>
                    <a href="{{ route('tasks.create') }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors font-medium flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Add Task</span>
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Task</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Project</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigned To</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (\App\Models\Tasks::with(['project', 'assignee'])->latest()->get() as $task)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-black">{{ $task->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($task->description, 50) }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $task->project->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($task->assignee)
                                            <div class="flex items-center">
                                                <div
                                                    class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white font-semibold text-xs mr-2">
                                                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                                </div>
                                                <span class="text-sm text-gray-900">{{ $task->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($task->status == 'done') bg-green-100 text-green-800
                                    @elseif($task->status == 'in_progress') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($task->priority)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($task->priority == 'high') bg-red-100 text-red-800
                                    @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($task->priority) }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-500">Not set</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('tasks.show', $task->id) }}"
                                            class="text-primary hover:text-[#146c3e] mr-3">View</a>
                                        <a href="{{ route('tasks.edit', $task->id) }}"
                                            class="text-primary hover:text-[#146c3e] mr-3">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Files Tab -->
    <div id="filesTab" class="tab-content hidden">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-black">File Management</h2>
                    <a href="{{ route('files.create') }}"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors font-medium flex items-center space-x-2">
                        <i class="fas fa-upload"></i>
                        <span>Upload File</span>
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Project</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Uploaded By</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Size</th>
                                <th
                                    class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach (\App\Models\Files::with(['project', 'user'])->latest()->get() as $file)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <i class="fas fa-file text-gray-400 mr-2"></i>
                                            <div class="text-sm font-medium text-black">{{ $file->file_name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $file->project->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $file->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($file->file_path))
                                            {{ number_format(\Illuminate\Support\Facades\Storage::disk('public')->size($file->file_path) / 1024, 1) }}
                                            KB
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('files.download', $file->id) }}"
                                            class="text-primary hover:text-[#146c3e] mr-3">Download</a>
                                        <a href="{{ route('files.show', $file->id) }}"
                                            class="text-primary hover:text-[#146c3e] mr-3">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');

                    // Update active tab button
                    tabButtons.forEach(btn => {
                        btn.classList.remove('bg-primary', 'text-white');
                        btn.classList.add('text-gray-600', 'hover:text-black',
                            'hover:bg-gray-100');
                    });
                    this.classList.add('bg-primary', 'text-white');
                    this.classList.remove('text-gray-600', 'hover:text-black', 'hover:bg-gray-100');

                    // Show active tab content
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.getElementById(tabId + 'Tab').classList.remove('hidden');
                });
            });

            // Clear cache button
            document.getElementById('clearCacheBtn').addEventListener('click', function() {
                fetch('', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        showAlert('Cache cleared successfully!');
                    })
                    .catch(error => {
                        showAlert('Error clearing cache', 'error');
                    });
            });

            // Search functionality
            document.getElementById('searchInput').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                // Implement search logic based on active tab
            });
        });

        function showAlert(message, type = 'success') {
            const alert = document.getElementById('systemAlert');
            const alertMessage = document.getElementById('alertMessage');

            alertMessage.textContent = message;
            alert.classList.remove('hidden');

            setTimeout(() => {
                hideAlert();
            }, 5000);
        }

        function hideAlert() {
            document.getElementById('systemAlert').classList.add('hidden');
        }
    </script>

    <style>
        .hidden {
            display: none;
        }

        .hover-scale:hover {
            transform: scale(1.02);
        }

        .transition-all-custom {
            transition: all 0.3s ease;
        }

        .scrollbar-custom::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-custom::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        .scrollbar-custom::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .status-badge.status-healthy {
            background-color: #dcfce7;
            color: #166534;
        }

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-10px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
@endsection
