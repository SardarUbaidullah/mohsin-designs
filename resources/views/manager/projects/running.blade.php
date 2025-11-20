@extends("Manager.layouts.app")

@section("content")
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('manager.projects.index') }}"
                           class="group flex items-center text-gray-500 hover:text-blue-600 transition-all duration-200">
                            <div class="w-8 h-8 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center group-hover:bg-blue-50 group-hover:border-blue-200 group-hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">All Projects</span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Running Projects</h1>
                    </div>
                    <p class="text-gray-600">Active projects currently in development and planning stages</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.projects.completed') }}"
                       class="group flex items-center px-4 py-2 text-gray-600 hover:text-green-600 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        View Completed
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

        <!-- Running Projects Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Running</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $runningStats['total'] }}</p>
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
                        <p class="text-sm font-medium text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $runningStats['in_progress'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-orange-600 mt-1">{{ $runningStats['pending'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Overdue</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ $runningStats['overdue'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse($projects as $project)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200/60 overflow-hidden hover:shadow-md transition-all duration-300 group">
                <!-- Project Header -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200/60">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900 text-lg truncate group-hover:text-blue-600 transition-colors duration-200">
                            <a href="{{ route('manager.projects.show', $project->id) }}" class="hover:underline">
                                {{ $project->name }}
                            </a>
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                            @if($project->status == 'in_progress') bg-green-100 text-green-800 border border-green-200
                            @else bg-orange-100 text-orange-800 border border-orange-200 @endif">
                            @if($project->status == 'in_progress')
                                <div class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1 animate-pulse"></div>
                                In Progress
                            @else
                                <div class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-1"></div>
                                Planning
                            @endif
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $project->description ?: 'No description provided' }}</p>
                </div>

                <!-- Project Progress -->
                <div class="p-6">
                    @php
                        $totalTasks = $project->tasks_count;
                        $completedTasks = $project->tasks->where('status', 'done')->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    @endphp

                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress</span>
                            <span class="text-sm font-semibold text-blue-600">{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ $completedTasks }} completed</span>
                            <span>{{ $totalTasks }} total tasks</span>
                        </div>
                    </div>

                    <!-- Timeline Info -->
                    <div class="space-y-3 mb-4">
                        @if($project->start_date)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Start Date</span>
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}</span>
                        </div>
                        @endif

                        @if($project->due_date)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Due Date</span>
                            <span class="font-medium
                                @if(\Carbon\Carbon::parse($project->due_date)->isPast()) text-red-600
                                @else text-gray-900 @endif">
                                {{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }}
                                @if(\Carbon\Carbon::parse($project->due_date)->isPast())
                                    (Overdue)
                                @endif
                            </span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Last Updated</span>
                            <span class="font-medium text-gray-900">{{ $project->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Tasks
                            </a>

                            @if($project->status == 'pending')
                            <form action="{{ route('manager.projects.progress', $project->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                    Start Project
                                </button>
                            </form>
                            @endif
                        </div>

                        <div class="flex items-center space-x-2">
                            @if($project->status == 'in_progress')
                            <form action="{{ route('manager.projects.complete', $project->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                    Complete
                                </button>
                            </form>
                            @endif

                            <a href="{{ route('manager.projects.edit', $project->id) }}"
                               class="text-gray-400 hover:text-blue-600 transition-colors duration-200 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-200/60 p-12 text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No running projects</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">All your projects are completed or you haven't started any projects yet.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('manager.projects.completed') }}"
                       class="inline-flex items-center px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors duration-200 font-semibold">
                        View Completed Projects
                    </a>
                </div>
            </div>
            @endforelse
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
