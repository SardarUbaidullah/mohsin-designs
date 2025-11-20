@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)

@section("content")
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('manager.tasks.index') }}"
                           class="group flex items-center text-gray-500 hover:text-green-600 transition-all duration-200">
                            <div class="w-8 h-8 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center group-hover:bg-green-50 group-hover:border-green-200 group-hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">All Tasks</span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Completed Tasks</h1>
                    </div>
                    <p class="text-gray-600">Successfully completed tasks and achievements</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.tasks.create') }}"
                       class="group relative bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center shadow-lg hover:shadow-xl hover:scale-105 active:scale-95">
                        <div class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center mr-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        Create New Task
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

        <!-- Completion Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @php
                $totalCompleted = $tasks->count();
                $completedThisWeek = $tasks->where('updated_at', '>=', now()->subWeek())->count();
                $completedThisMonth = $tasks->where('updated_at', '>=', now()->subMonth())->count();
                $onTimeCompletion = $tasks->filter(function($task) {
                    return $task->due_date && \Carbon\Carbon::parse($task->due_date)->gte($task->updated_at);
                })->count();
            @endphp

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Completed</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $totalCompleted }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">This Week</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $completedThisWeek }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $completedThisMonth }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">On Time</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $onTimeCompletion }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Tasks Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($tasks as $task)
            <div class="bg-white rounded-2xl shadow-sm border border-green-200/60 p-6 hover:shadow-md transition-all duration-300 group">
                <!-- Completion Badge -->
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Completed
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ $task->updated_at->diffForHumans() }}
                    </span>
                </div>

                <!-- Task Content -->
                <h3 class="font-semibold text-gray-900 text-lg mb-3 line-clamp-2 group-hover:text-green-600 transition-colors duration-200">
                    <a href="{{ route('manager.tasks.show', $task->id) }}" class="hover:underline">
                        {{ $task->title }}
                    </a>
                </h3>

                <p class="text-sm text-gray-600 line-clamp-3 mb-4">{{ $task->description }}</p>

                <!-- Project & Timeline -->
                <div class="space-y-3 mb-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Project</span>
                        <a href="{{ route('manager.projects.show', $task->project_id) }}"
                           class="font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                            {{ $task->project->name }}
                        </a>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Completed</span>
                        <span class="font-medium text-gray-900">{{ $task->updated_at->format('M d, Y') }}</span>
                    </div>

                    @if($task->due_date)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Due Date</span>
                        <span class="font-medium
                            @if(\Carbon\Carbon::parse($task->due_date)->gte($task->updated_at)) text-green-600
                            @else text-orange-600 @endif">
                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                        </span>
                    </div>
                    @endif
                </div>

                <!-- Priority & Assignee -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-3">
                        @if($task->priority)
                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                            @if($task->priority == 'high') bg-red-100 text-red-800
                            @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($task->priority) }}
                        </span>
                        @endif

                        @if($task->assignee)
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-semibold text-xs">
                                {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-600">{{ $task->assignee->name }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        <form action="{{ route('manager.tasks.progress', $task->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1"
                                    title="Reopen Task">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        </form>

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
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-200/60 p-12 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No completed tasks yet</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Complete some tasks to see your achievements and track your progress here.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('manager.tasks.pending') }}"
                       class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors duration-200 font-semibold">
                        View Pending Tasks
                    </a>
                    <a href="{{ route('manager.tasks.create') }}"
                       class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-semibold">
                        Create New Task
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Load More Button (if pagination is needed) -->
@if($tasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $tasks->hasMorePages())
        <div class="mt-8 text-center">
            <button class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200 font-semibold shadow-sm">
                Load More Completed Tasks
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>
        @endif
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

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection
