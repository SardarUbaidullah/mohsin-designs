@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp

@extends($layout)

@section("content")
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('manager.milestones.index') }}"
                           class="group flex items-center text-gray-500 hover:text-purple-600 transition-all duration-200">
                            <div class="w-8 h-8 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center group-hover:bg-purple-50 group-hover:border-purple-200 group-hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">Back to Milestones</span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 truncate">{{ $milestone->title }}</h1>
                    </div>
                    <div class="flex flex-wrap items-center gap-3 mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if($milestone->status == 'completed') bg-green-100 text-green-800 border border-green-200
                            @elseif($milestone->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            @if($milestone->status == 'completed')
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2"></div>
                            @elseif($milestone->status == 'in_progress')
                                <div class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2 animate-pulse"></div>
                            @else
                                <div class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-2"></div>
                            @endif
                            {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                        </span>

                        @if($milestone->due_date)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
                                bg-red-100 text-red-800 border border-red-200
                            @else
                                bg-purple-100 text-purple-800 border border-purple-200
                            @endif">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
                                Overdue: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                            @else
                                Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                            @endif
                        </span>
                        @endif

                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ $milestone->tasks->count() }} Tasks
                        </span>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.milestones.edit', $milestone->id) }}"
                       class="group relative bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Milestone
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
            <!-- Left Column - Milestone Details -->
            <div class="xl:col-span-2 space-y-8">
                <!-- Milestone Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 px-6 py-4 border-b border-gray-200/60">
                        <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                            <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                            Milestone Information
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
                                            <span class="text-sm font-medium text-gray-600">Milestone ID</span>
                                            <span class="text-sm font-semibold text-gray-900">#{{ $milestone->id }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Title</span>
                                            <span class="text-sm font-semibold text-gray-900">{{ $milestone->title }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Project</span>
                                            <a href="{{ route('manager.projects.show', $milestone->project_id) }}"
                                               class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                                {{ $milestone->project->name }}
                                            </a>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Status</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                @if($milestone->status == 'completed') bg-green-100 text-green-800
                                                @elseif($milestone->status == 'in_progress') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst(str_replace('_', ' ', $milestone->status)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress & Timeline -->
                            <div class="space-y-6">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Progress & Timeline
                                    </h3>
                                    <div class="space-y-4">
                                        <!-- Progress Bar -->
                                        @php
                                            $completedTasks = $milestone->tasks->where('status', 'done')->count();
                                            $totalTasks = $milestone->tasks->count();
                                            $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
                                        @endphp
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Progress</span>
                                            <div class="flex items-center space-x-2">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 transition-all duration-500"
                                                         style="width: {{ $progress }}%"></div>
                                                </div>
                                                <span class="text-xs text-gray-600">{{ round($progress) }}%</span>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Tasks Completed</span>
                                            <span class="text-sm font-semibold text-gray-900">
                                                {{ $completedTasks }} of {{ $totalTasks }}
                                            </span>
                                        </div>
                                        @if($milestone->due_date)
                                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">Due Date</span>
                                            <span class="text-sm font-semibold
                                                @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
                                                    text-red-600
                                                @else
                                                    text-gray-900
                                                @endif">
                                                {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
    @if($milestone->description)
<div class="mt-8 pt-6 border-t border-gray-100">
    <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
        <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Description
    </h3>
    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 overflow-x-auto">
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
                e($milestone->description)) 
            !!}
        </div>
    </div>
</div>
@endif
                    </div>
                </div>

                <!-- Tasks Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200/60">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                Tasks ({{ $milestone->tasks->count() }})
                            </h2>
                            <a href="{{ route('manager.tasks.create', ['milestone_id' => $milestone->id]) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add Task
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($milestone->tasks->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($milestone->tasks as $task)
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 hover:border-blue-200 transition-all duration-200 group">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 group-hover:text-blue-600 transition-colors duration-200">
                                                <a href="{{ route('manager.tasks.show', $task->id) }}" class="hover:underline">
                                                    {{ $task->title }}
                                                </a>
                                            </h4>
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($task->status == 'done') bg-green-100 text-green-800
                                                    @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                                </span>
                                                @if($task->priority)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($task->priority == 'high') bg-red-100 text-red-800
                                                    @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($task->priority) }}
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                        <span>Assigned to: {{ $task->assignee->name ?? 'Unassigned' }}</span>
                                        @if($task->due_date)
                                        <span class="@if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done') text-red-600 font-medium @endif">
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                        </span>
                                        @endif
                                    </div>

                                    @if($task->description)
                                    <p class="text-xs text-gray-600 line-clamp-2 mb-3">{{ $task->description }}</p>
                                    @endif

                                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('manager.tasks.show', $task->id) }}"
                                               class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                View
                                            </a>
                                            <a href="{{ route('manager.tasks.edit', $task->id) }}"
                                               class="text-yellow-600 hover:text-yellow-800 text-xs font-medium">
                                                Edit
                                            </a>
                                        </div>
                                        <span class="text-xs text-gray-400">
                                            {{ $task->created_at->format('M d') }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-gray-500">No tasks assigned to this milestone</p>
                                <a href="{{ route('manager.tasks.create', ['milestone_id' => $milestone->id]) }}"
                                   class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                                    Create the first task
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Timeline & Actions -->
            <div class="space-y-8">
                <!-- Timeline Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden transition-all duration-300 hover:shadow-md">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-gray-200/60">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
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
                                    <p class="text-sm text-gray-600 mt-1">{{ $milestone->created_at->format('M d, Y \\a\\t H:i') }}</p>
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
                                    <p class="text-sm text-gray-600 mt-1">{{ $milestone->updated_at->format('M d, Y \\a\\t H:i') }}</p>
                                </div>
                            </div>

                            @if($milestone->due_date)
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10
                                    @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
                                        bg-red-100
                                    @else
                                        bg-purple-100
                                    @endif
                                    rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5
                                        @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
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
                                        @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
                                            text-red-800
                                        @else
                                            text-gray-900
                                        @endif">
                                        Due Date
                                    </p>
                                    <p class="text-sm
                                        @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
                                            text-red-600 font-medium
                                        @else
                                            text-gray-600
                                        @endif mt-1">
                                        {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                        @if(\Carbon\Carbon::parse($milestone->due_date)->isPast() && $milestone->status != 'completed')
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
                            <a href="{{ route('manager.milestones.edit', $milestone->id) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-purple-50 rounded-xl border border-gray-200 hover:border-purple-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Edit Milestone</p>
                                    <p class="text-sm text-gray-500">Modify milestone details</p>
                                </div>
                            </a>

                            <a href="{{ route('manager.projects.show', $milestone->project_id) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-blue-50 rounded-xl border border-gray-200 hover:border-blue-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">View Project</p>
                                    <p class="text-sm text-gray-500">Go to project details</p>
                                </div>
                            </a>

                            <a href="{{ route('manager.tasks.create', ['milestone_id' => $milestone->id]) }}"
                               class="group w-full flex items-center space-x-4 p-4 text-left text-gray-700 hover:bg-green-50 rounded-xl border border-gray-200 hover:border-green-200 transition-all duration-200">
                                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors duration-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Add Task</p>
                                    <p class="text-sm text-gray-500">Create new task</p>
                                </div>
                            </a>
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
                        <p class="text-sm text-gray-600 mb-4">Once you delete a milestone, there is no going back. Please be certain.</p>
                        <form action="{{ route('manager.milestones.destroy', $milestone->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure you want to delete this milestone? This action cannot be undone.')"
                                    class="w-full px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-semibold text-sm shadow-lg hover:shadow-xl hover:scale-105 active:scale-95 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Milestone
                            </button>
                        </form>
                    </div>
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
