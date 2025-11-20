@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section("content")
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50/30 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('manager.tasks.index') }}"
                           class="group flex items-center text-gray-500 hover:text-blue-600 transition-all duration-200">
                            <div class="w-8 h-8 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center group-hover:bg-blue-50 group-hover:border-blue-200 group-hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">Back to Tasks</span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $task->title }}</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($task->status == 'done') bg-green-100 text-green-800 border border-green-200
                            @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            @if($task->status == 'done')
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></div>
                            @elseif($task->status == 'in_progress')
                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                            @else
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-2"></div>
                            @endif
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </span>

                        @if($task->priority)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($task->priority == 'high') bg-red-100 text-red-800 border border-red-200
                            @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800 border border-yellow-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            @if($task->priority == 'high')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                </svg>
                            @elseif($task->priority == 'medium')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            {{ ucfirst($task->priority) }} Priority
                        </span>
                        @endif

                        @if($task->due_date)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                bg-red-100 text-red-800 border border-red-200
                            @else
                                bg-purple-100 text-purple-800 border border-purple-200
                            @endif">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                Overdue: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            @else
                                Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                            @endif
                        </span>
                        @endif

                        @if($task->milestone)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ $task->milestone->title }}
                        </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.tasks.edit', $task->id) }}"
                       class="group relative bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Task
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

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <!-- Left Column - Task Details -->
            <div class="xl:col-span-2 space-y-8">
                <!-- Task Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200/60">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                            Task Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Basic Details -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Basic Details
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Task Title</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ $task->title }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Project</span>
                                            <a href="{{ route('manager.projects.show', $task->project_id) }}"
                                               class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                                {{ $task->project->name }}
                                            </a>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Created By</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ $task->user->name ?? 'System' }}</span>
                                        </div>
                                        @if($task->milestone)
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Milestone</span>
                                            <div class="flex items-center">
                                                <span class="text-sm font-semibold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">
                                                    {{ $task->milestone->title }}
                                                </span>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status & Assignment -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Status & Assignment
                                    </h3>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Assigned To</span>
                                            <div class="flex items-center">
                                                @if($task->assignee)
                                                <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-xs mr-2">
                                                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                                                </div>
                                                <span class="text-sm font-semibold text-gray-900">{{ $task->assignee->name }}</span>
                                                @else
                                                <span class="text-sm text-gray-500">Unassigned</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Progress</span>
                                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full
                                                    @if($task->status == 'done') bg-green-500
                                                    @elseif($task->status == 'in_progress') bg-blue-500 w-1/2
                                                    @else bg-gray-400 w-1/4 @endif">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                       @if($task->description)
<div class="mt-8 pt-6 border-t border-gray-100">
    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Description
    </h3>
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 overflow-x-auto">
        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words min-w-0">
            {!! preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 break-all">$1</a>', e($task->description)) !!}
        </div>
    </div>
</div>
@endif
                    </div>
                </div>

                <!-- Milestone Information Card -->
                @if($task->milestone)
                <div class="bg-white rounded-2xl shadow-sm border border-indigo-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-indigo-200/60">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full mr-3"></div>
                            Milestone Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Milestone Title</span>
                                    <span class="text-sm font-semibold text-indigo-600">{{ $task->milestone->title }}</span>
                                </div>
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Status</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($task->milestone->status == 'completed') bg-green-100 text-green-800
                                        @elseif($task->milestone->status == 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($task->milestone->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-4">
                                @if($task->milestone->due_date)
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Due Date</span>
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($task->milestone->due_date)->format('M d, Y') }}
                                    </span>
                                </div>
                                @endif
                                <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Progress</span>
                                    <div class="flex items-center space-x-2">
                                        @php
                                            $milestoneTasks = $task->milestone->tasks ?? collect();
                                            $completedTasks = $milestoneTasks->where('status', 'done')->count();
                                            $totalTasks = $milestoneTasks->count();
                                            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                                        @endphp
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full bg-indigo-500" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600">{{ round($progress) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                   @if($task->milestone->description)
<div class="mt-6 pt-6 border-t border-gray-100">
    <h3 class="text-sm font-semibold text-gray-700 mb-3">Milestone Description</h3>
    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-200 overflow-x-auto">
        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line break-words min-w-0">
            {!! 
                preg_replace_callback('/(https?:\/\/[^\s]+|www\.[^\s]+)/', 
                function($matches) {
                    $url = $matches[0];
                    // Agar www se start ho raha hai toh http:// add karo
                    if (strpos($url, 'www.') === 0) {
                        $url = 'http://' . $url;
                    }
                    return '<a href="' . $url . '" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline transition-colors duration-200 break-all">' . $matches[0] . '</a>';
                }, 
                e($task->milestone->description)) 
            !!}
        </div>
    </div>
</div>
@endif
                    </div>
                </div>
                @endif

                <!-- Subtasks Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Subtasks</h2>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('manager.subtasks.index', $task->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All
                            </a>
                            <a href="{{ route('manager.subtasks.create', $task->id) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Subtask
                            </a>
                        </div>
                    </div>

                    @if($task->subtasks && $task->subtasks->count() > 0)
                        <div class="space-y-3">
                            @foreach($task->subtasks->take(5) as $subtask)
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center space-x-3">
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded flex items-center justify-center">
                                        @if($subtask->status == 'done')
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $subtask->title }}</p>
                                        <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $subtask->status) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('manager.subtasks.edit', ['taskId' => $task->id, 'subtaskId' => $subtask->id]) }}"
                                       class="text-yellow-600 hover:text-yellow-800 text-xs">
                                        Edit
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($task->subtasks->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="{{ route('manager.subtasks.index', $task->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View all {{ $task->subtasks->count() }} subtasks
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <p class="text-gray-500">No subtasks created yet</p>
                            <a href="{{ route('manager.subtasks.create', $task->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                Create your first subtask
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Timeline & Actions -->
            <div class="space-y-8">
                <!-- Timeline Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b border-gray-200/60">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            Timeline
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Created</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $task->created_at->format('M d, Y \\a\\t H:i') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">Last Updated</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $task->updated_at->format('M d, Y \\a\\t H:i') }}</p>
                                </div>
                            </div>

                            @if($task->due_date)
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10
                                    @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                        bg-red-100
                                    @else
                                        bg-purple-100
                                    @endif
                                    rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5
                                        @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                            text-red-600
                                        @else
                                            text-purple-600
                                        @endif"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold
                                        @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                            text-red-800
                                        @else
                                            text-gray-900
                                        @endif">
                                        Due Date
                                    </p>
                                    <p class="text-sm
                                        @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                            text-red-600 font-medium
                                        @else
                                            text-gray-600
                                        @endif mt-1">
                                        {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                        @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done')
                                            (Overdue)
                                        @endif
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-orange-50 to-amber-50 px-6 py-4 border-b border-gray-200/60">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="w-2 h-2 bg-orange-500 rounded-full mr-3"></div>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <a href="{{ route('manager.tasks.edit', $task->id) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-blue-50 rounded-xl border border-gray-200 hover:border-blue-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Edit Task</p>
                                    <p class="text-sm text-gray-500">Modify task details</p>
                                </div>
                            </a>

                            <a href="{{ route('manager.projects.show', $task->project_id) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-green-50 rounded-xl border border-gray-200 hover:border-green-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">View Project</p>
                                    <p class="text-sm text-gray-500">Go to project details</p>
                                </div>
                            </a>

                            @if($task->milestone)
                            <a href="{{ route('manager.milestones.show', $task->milestone->id) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-indigo-50 rounded-xl border border-gray-200 hover:border-indigo-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:bg-indigo-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">View Milestone</p>
                                    <p class="text-sm text-gray-500">See milestone details</p>
                                </div>
                            </a>
                            @endif

                            @if($task->assignee)
                            <a href="{{ route('manager.team.show', $task->assigned_to) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-purple-50 rounded-xl border border-gray-200 hover:border-purple-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">View Assignee</p>
                                    <p class="text-sm text-gray-500">See team member details</p>
                                </div>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-white rounded-2xl shadow-sm border border-red-200 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-800 flex items-center">
                            <div class="w-2 h-2 bg-red-500 rounded-full mr-3"></div>
                            Danger Zone
                        </h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-4">Once you delete a task, there is no going back. Please be certain.</p>
                        <form action="{{ route('manager.tasks.destroy', $task->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this task? This action cannot be undone.')"
                                    class="w-full px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-semibold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-comments
    :commentable="$task"
    commentableType="task"
    :showInternal="auth()->user()->role !== 'client'"
/>
<style>
    .animate-fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
