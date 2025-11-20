@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section("content")
<div class="min-h-screen bg-gradient-to-br from-orange-50 to-amber-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('manager.tasks.index') }}"
                           class="group flex items-center text-gray-500 hover:text-orange-600 transition-all duration-200">
                            <div class="w-8 h-8 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center group-hover:bg-orange-50 group-hover:border-orange-200 group-hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">All Tasks</span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Pending Tasks</h1>
                    </div>
                    <p class="text-gray-600">Active tasks that require attention and action</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.tasks.create') }}"
                       class="group relative bg-gradient-to-r from-orange-500 to-amber-600 hover:from-orange-600 hover:to-amber-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                        <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center mr-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        Create Task
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl flex items-center shadow-sm animate-fade-in">
                <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @php
                $todoCount = $tasks->where('status', 'todo')->count();
                $inProgressCount = $tasks->where('status', 'in_progress')->count();
                $urgentCount = $tasks->where('priority', 'high')->whereIn('status', ['todo', 'in_progress'])->count();
            @endphp

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">To Do</p>
                        <p class="text-2xl font-bold text-gray-600 mt-1">{{ $todoCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $inProgressCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Urgent Tasks</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $urgentCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- To Do Column -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                        To Do ({{ $todoCount }})
                    </h2>
                    <span class="text-sm text-gray-500">Needs attention</span>
                </div>

                <div class="space-y-4">
                    @forelse($tasks->where('status', 'todo') as $task)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-6 hover:shadow-md transition-all duration-300 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-lg mb-2 group-hover:text-orange-600 transition-colors duration-200">
                                    <a href="{{ route('manager.tasks.show', $task->id) }}" class="hover:underline">
                                        {{ $task->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $task->description }}</p>

                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        {{ $task->project->name }}
                                    </span>
                                    @if($task->due_date)
                                    <span class="flex items-center
                                        @if(\Carbon\Carbon::parse($task->due_date)->isPast()) text-red-500 font-medium @endif">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-2">
                                @if($task->priority)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if($task->priority == 'high') bg-red-100 text-red-800 border border-red-200
                                    @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @endif

                                <form action="{{ route('manager.tasks.progress', $task->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                        Start
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-2">
                                @if($task->assignee)
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs">
                                        {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $task->assignee->name }}</span>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">Unassigned</span>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                <a href="{{ route('manager.tasks.edit', $task->id) }}"
                                   class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-8 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No tasks to do</h3>
                        <p class="text-gray-500 mb-4">All pending tasks are in progress or completed</p>
                        <a href="{{ route('manager.tasks.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors duration-200">
                            Create New Task
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- In Progress Column -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                        In Progress ({{ $inProgressCount }})
                    </h2>
                    <span class="text-sm text-gray-500">Currently working</span>
                </div>

                <div class="space-y-4">
                    @forelse($tasks->where('status', 'in_progress') as $task)
                    <div class="bg-white rounded-2xl shadow-sm border border-blue-200/60 p-6 hover:shadow-md transition-all duration-300 group">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 text-lg mb-2 group-hover:text-blue-600 transition-colors duration-200">
                                    <a href="{{ route('manager.tasks.show', $task->id) }}" class="hover:underline">
                                        {{ $task->title }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ $task->description }}</p>

                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        {{ $task->project->name }}
                                    </span>
                                    @if($task->due_date)
                                    <span class="flex items-center
                                        @if(\Carbon\Carbon::parse($task->due_date)->isPast()) text-red-500 font-medium @endif">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex flex-col items-end space-y-2">
                                @if($task->priority)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    @if($task->priority == 'high') bg-red-100 text-red-800 border border-red-200
                                    @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    {{ ucfirst($task->priority) }}
                                </span>
                                @endif

                                <form action="{{ route('manager.tasks.complete', $task->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Complete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center space-x-2">
                                @if($task->assignee)
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs">
                                        {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $task->assignee->name }}</span>
                                </div>
                                @else
                                <span class="text-sm text-gray-400">Unassigned</span>
                                @endif
                            </div>

                            <div class="flex items-center space-x-2">
                                <a href="{{ route('manager.tasks.edit', $task->id) }}"
                                   class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 p-8 text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No tasks in progress</h3>
                        <p class="text-gray-500">Start working on some tasks to see them here</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
