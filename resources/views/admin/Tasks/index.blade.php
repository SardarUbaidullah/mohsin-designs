@extends('admin.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tasks</h1>
                <p class="text-gray-600 mt-2">Manage all tasks across projects</p>
            </div>
            <a href="{{ url('/tasks/create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium transition duration-200 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
                Create New Task
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Professional Filter Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">

            <!-- Filter Title -->
            <div class="flex items-center gap-2 border-b mb-4">
                <svg class="size-7 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                </svg>
                <h3 class="text-xl font-semibold text-wrap text-gray-500">Filter Tasks</h3>
            </div>

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-4 flex-1 lg:flex-initial lg:justify-end">
                    <!-- Project Filter -->
                    <div class="flex-1 sm:flex-initial">
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select id="project_id" name="project_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                            <option value="">All Projects</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Assigned User Filter -->
                    <div class="flex-1 sm:flex-initial">
                        <label for="assigned_user" class="block text-sm font-medium text-gray-700 mb-2">Assigned To</label>
                        <select id="assigned_user" name="assigned_user"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                            <option value="">All Users</option>
                            @foreach ($assignedUsers as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('assigned_user') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                    @if ($user->department)
                                        ({{ $user->department }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div class="flex-1 sm:flex-initial">
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                        <select id="priority" name="priority"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200 bg-white">
                            <option value="">All Priorities</option>
                            <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="button" id="applyFilters"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z" />
                            </svg>
                            Apply
                        </button>
                        <a href="{{ route('tasks.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Active Filters Display -->
            @if (request()->has('project_id') || request()->has('assigned_user') || request()->has('priority'))
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center flex-wrap gap-2">
                        <span class="text-sm text-gray-600">Active filters:</span>
                        @if (request('project_id'))
                            @php
                                $selectedProject = $projects->firstWhere('id', request('project_id'));
                            @endphp
                            @if ($selectedProject)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Project: {{ $selectedProject->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['project_id' => null]) }}"
                                        class="ml-1 hover:text-purple-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        @endif
                        @if (request('assigned_user'))
                            @php
                                $selectedUser = $assignedUsers->firstWhere('id', request('assigned_user'));
                            @endphp
                            @if ($selectedUser)
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    User: {{ $selectedUser->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['assigned_user' => null]) }}"
                                        class="ml-1 hover:text-blue-600">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </a>
                                </span>
                            @endif
                        @endif
                        @if (request('priority'))
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Priority: {{ ucfirst(request('priority')) }}
                                <a href="{{ request()->fullUrlWithQuery(['priority' => null]) }}"
                                    class="ml-1 hover:text-green-600">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- View Toggle and Mobile Filter Tabs -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <!-- View Toggle -->
            <div class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 p-1">
                <button id="gridViewBtn"
                    class="view-toggle-btn active flex items-center px-4 py-2 rounded-lg bg-blue-50 text-blue-600 font-medium transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Grid View
                </button>
                <button id="listViewBtn"
                    class="view-toggle-btn flex items-center px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 font-medium transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    List View
                </button>
            </div>

            <!-- Professional Mobile Filter Tabs -->
            <div class="lg:hidden">
                <div class="flex space-x-1 bg-white p-1 rounded-xl shadow-sm border border-gray-200 overflow-x-auto">
                    <button data-status="todo"
                        class="task-filter-tab active flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-gray-100 text-gray-800 whitespace-nowrap transition-all duration-200">
                        To Do ({{ $tasks->where('status', 'todo')->count() }})
                    </button>
                    <button data-status="in_progress"
                        class="task-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                        In Progress ({{ $tasks->where('status', 'in_progress')->count() }})
                    </button>
                    <button data-status="done"
                        class="task-filter-tab flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                        Done ({{ $tasks->where('status', 'done')->count() }})
                    </button>
                </div>
            </div>
        </div>


        <!-- Kanban Board (Grid View) -->
        @if ($tasks->count() > 0)
            <div id="gridView" class="task-view active">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- To Do Column -->
                    <div class="task-column active bg-gray-50 rounded-2xl p-6" data-status="todo">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-3 h-3 bg-gray-400 rounded-full mr-3"></div>
                                To Do
                            </h3>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $tasks->where('status', 'todo')->count() }}
                            </span>
                        </div>
                        <div class="space-y-4">
                            @foreach ($tasks->where('status', 'todo') as $task)
                                @include('admin.Tasks.partials.task-card', [
                                    'task' => $task,
                                    'status' => 'todo',
                                ])
                            @endforeach
                        </div>
                    </div>

                    <!-- In Progress Column -->
                    <div class="task-column bg-gray-50 rounded-2xl p-6" data-status="in_progress">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                In Progress
                            </h3>
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $tasks->where('status', 'in_progress')->count() }}
                            </span>
                        </div>
                        <div class="space-y-4">
                            @foreach ($tasks->where('status', 'in_progress') as $task)
                                @include('admin.Tasks.partials.task-card', [
                                    'task' => $task,
                                    'status' => 'in_progress',
                                ])
                            @endforeach
                        </div>
                    </div>

                    <!-- Done Column -->
                    <div class="task-column bg-gray-50 rounded-2xl p-6" data-status="done">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                Done
                            </h3>
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $tasks->where('status', 'done')->count() }}
                            </span>
                        </div>
                        <div class="space-y-4">
                            @foreach ($tasks->where('status', 'done') as $task)
                                @include('admin.Tasks.partials.task-card', [
                                    'task' => $task,
                                    'status' => 'done',
                                ])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- List View -->
            <div id="listView" class="task-view hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Task</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Project</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Assigned To</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Priority</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Due Date</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($tasks as $task)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $task->title }}</div>
                                                <div class="text-sm text-gray-500 line-clamp-1">
                                                    {{ Str::limit($task->description, 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $task->project->name ?? 'No Project' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-8 w-8 bg-gray-200 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($task->assignedUser->name ?? 'Unassigned', 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $task->assignedUser->name ?? 'Unassigned' }}</div>
                                                @if ($task->assignedUser && $task->assignedUser->department)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $task->assignedUser->department }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $priorityColors = [
                                                'high' => 'bg-red-100 text-red-800',
                                                'medium' => 'bg-yellow-100 text-yellow-800',
                                                'low' => 'bg-green-100 text-green-800',
                                            ];
                                            $priorityColor =
                                                $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $priorityColor }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($task->due_date)
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M j, Y') }}
                                            @if ($task->due_date < now() && $task->status != 'done')
                                                <span class="ml-1 text-red-500" title="Overdue">⚠️</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">No due date</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'todo' => 'bg-gray-100 text-gray-800',
                                                'in_progress' => 'bg-blue-100 text-blue-800',
                                                'done' => 'bg-green-100 text-green-800',
                                            ];
                                            $statusColor = $statusColors[$task->status] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span
                                            class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('tasks.show', $task->id) }}"
                                                class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </a>
                                            <a href="{{ route('tasks.edit', $task->id) }}"
                                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                                title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                                    title="Delete"
                                                    onclick="return confirm('Are you sure you want to delete this task?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-sm">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">No tasks found</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    @if (request()->has('project_id') || request()->has('assigned_user') || request()->has('priority'))
                        No tasks match your current filters. Try adjusting your criteria.
                    @else
                        Get started by creating your first task
                    @endif
                </p>
                @if (request()->has('project_id') || request()->has('assigned_user') || request()->has('priority'))
                    <a href="{{ route('tasks.index') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Clear Filters
                    </a>
                @else
                    <a href="{{ route('tasks.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-medium transition duration-200 inline-flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Create Your First Task
                    </a>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.task-filter-tab');
            const taskColumns = document.querySelectorAll('.task-column');
            const applyFiltersBtn = document.getElementById('applyFilters');
            const projectSelect = document.getElementById('project_id');
            const assignedUserSelect = document.getElementById('assigned_user');
            const prioritySelect = document.getElementById('priority');

            // View toggle elements
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            const gridView = document.getElementById('gridView');
            const listView = document.getElementById('listView');
            const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');

            // View toggle functionality
            gridViewBtn.addEventListener('click', function() {
                switchView('grid');
            });

            listViewBtn.addEventListener('click', function() {
                switchView('list');
            });

            function switchView(viewType) {
                // Update button states
                viewToggleBtns.forEach(btn => {
                    btn.classList.remove('active', 'bg-blue-50', 'text-blue-600');
                    btn.classList.add('text-gray-600', 'hover:bg-gray-100');
                });

                if (viewType === 'grid') {
                    gridViewBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
                    gridViewBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
                    gridView.classList.remove('hidden');
                    gridView.classList.add('active');
                    listView.classList.add('hidden');
                    listView.classList.remove('active');
                } else {
                    listViewBtn.classList.add('active', 'bg-blue-50', 'text-blue-600');
                    listViewBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
                    listView.classList.remove('hidden');
                    listView.classList.add('active');
                    gridView.classList.add('hidden');
                    gridView.classList.remove('active');
                }
            }

            // Filter application
            applyFiltersBtn.addEventListener('click', function() {
                const projectId = projectSelect.value;
                const assignedUser = assignedUserSelect.value;
                const priority = prioritySelect.value;

                let url = new URL(window.location.href);
                let params = new URLSearchParams();

                if (projectId) {
                    params.set('project_id', projectId);
                }

                if (assignedUser) {
                    params.set('assigned_user', assignedUser);
                }

                if (priority) {
                    params.set('priority', priority);
                }

                const queryString = params.toString();
                window.location.href = queryString ? `${url.pathname}?${queryString}` : url.pathname;
            });

            // Enter key support for filters
            [projectSelect, assignedUserSelect, prioritySelect].forEach(select => {
                select.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        applyFiltersBtn.click();
                    }
                });
            });

            // Mobile view initialization
            function initMobileView() {
                if (window.innerWidth < 1024) {
                    taskColumns.forEach((col, index) => {
                        if (index === 0) {
                            col.style.display = 'block';
                        } else {
                            col.style.display = 'none';
                        }
                    });
                } else {
                    taskColumns.forEach(col => {
                        col.style.display = 'block';
                    });
                }
            }

            // Filter tab click handler
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const status = this.getAttribute('data-status');

                    // Update active tab
                    filterTabs.forEach(t => {
                        t.classList.remove('active', 'bg-gray-100', 'text-gray-800');
                        t.classList.add('text-gray-600', 'hover:bg-gray-100');
                    });
                    this.classList.remove('text-gray-600', 'hover:bg-gray-100');
                    this.classList.add('active', 'bg-gray-100', 'text-gray-800');

                    // Show selected column, hide others on mobile
                    if (window.innerWidth < 1024) {
                        taskColumns.forEach(col => {
                            if (col.getAttribute('data-status') === status) {
                                col.style.display = 'block';
                            } else {
                                col.style.display = 'none';
                            }
                        });
                    }
                });
            });

            // Handle window resize
            window.addEventListener('resize', initMobileView);

            // Initial setup
            initMobileView();
        });
    </script>

    <style>
        @media (max-width: 1023px) {
            .task-column {
                display: none;
            }

            .task-column:first-child {
                display: block;
            }
        }

        .task-filter-tab.active {
            background-color: rgb(243, 244, 246);
            color: rgb(31, 41, 55);
        }

        .task-filter-tab {
            transition: all 0.2s ease-in-out;
        }

        .view-toggle-btn.active {
            background-color: rgb(239, 246, 255);
            color: rgb(37, 99, 235);
        }

        .view-toggle-btn {
            transition: all 0.2s ease-in-out;
        }

        .task-view {
            transition: opacity 0.3s ease-in-out;
        }

        .hover\:shadow-md {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .shadow-sm {
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .rounded-2xl {
            border-radius: 1rem;
        }

        .rounded-xl {
            border-radius: 0.75rem;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
