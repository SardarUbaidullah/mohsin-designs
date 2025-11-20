@extends("manager.layouts.app")

@section("content")
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('manager.projects.index') }}"
                           class="group flex items-center text-gray-500 hover:text-green-600 transition-all duration-200">
                            <div class="w-8 h-8 bg-white rounded-xl shadow-sm border border-gray-200 flex items-center justify-center group-hover:bg-green-50 group-hover:border-green-200 group-hover:shadow-md transition-all duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </div>
                            <span class="ml-2 text-sm font-medium hidden sm:block">All Projects</span>
                        </a>
                        <div class="h-6 w-px bg-gray-300"></div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Completed Projects</h1>
                    </div>
                    <p class="text-gray-600">Successfully delivered projects and achievements</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('manager.projects.running') }}"
                       class="group flex items-center px-4 py-2 text-gray-600 hover:text-blue-600 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        View Running
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
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Completed</p>
                        <p class="text-2xl font-bold text-green-600 mt-1">{{ $completionStats['total'] }}</p>
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
                        <p class="text-sm font-medium text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-blue-600 mt-1">{{ $completionStats['completed_this_month'] }}</p>
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
                        <p class="text-sm font-medium text-gray-600">This Quarter</p>
                        <p class="text-2xl font-bold text-purple-600 mt-1">{{ $completionStats['completed_this_quarter'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200/60">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">On Time</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $completionStats['on_time'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed Projects Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
            @forelse($projects as $project)
            <div class="bg-white rounded-2xl shadow-sm border border-green-200/60 overflow-hidden hover:shadow-md transition-all duration-300 group">
                <!-- Project Header -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-200/60">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-900 text-lg truncate group-hover:text-green-600 transition-colors duration-200">
                            <a href="{{ route('manager.projects.show', $project->id) }}" class="hover:underline">
                                {{ $project->name }}
                            </a>
                        </h3>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Completed
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $project->description ?: 'No description provided' }}</p>
                </div>

                <!-- Project Completion Details -->
                <div class="p-6">
                    @php
                        $totalTasks = $project->tasks_count;
                        $completedTasks = $project->tasks->where('status', 'done')->count();
                        $completionDate = $project->updated_at;
                        $wasOnTime = $project->due_date && \Carbon\Carbon::parse($project->due_date)->gte($completionDate);
                    @endphp

                    <!-- Completion Summary -->
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700">Final Progress</span>
                            <span class="text-sm font-semibold text-green-600">100%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full bg-gradient-to-r from-green-500 to-emerald-500" style="width: 100%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ $completedTasks }} tasks completed</span>
                            <span class="flex items-center">
                                @if($wasOnTime)
                                <svg class="w-3 h-3 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                On Time
                                @else
                                <svg class="w-3 h-3 mr-1 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                                Delayed
                                @endif
                            </span>
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
                            <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }}</span>
                        </div>
                        @endif

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Completed On</span>
                            <span class="font-medium text-green-600">{{ $completionDate->format('M d, Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Duration</span>
                            <span class="font-medium text-gray-900">
                                @if($project->start_date)
                                    {{ \Carbon\Carbon::parse($project->start_date)->diffInDays($completionDate) }} days
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}"
                               class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                View Tasks
                            </a>

                            <form action="{{ route('manager.projects.progress', $project->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-xs font-medium transition-colors duration-200">
                                    Reopen
                                </button>
                            </form>
                        </div>

                        <div class="flex items-center space-x-2">
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
                <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No completed projects yet</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">Complete some projects to see your achievements and track your success here.</p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('manager.projects.running') }}"
                       class="inline-flex items-center px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-colors duration-200 font-semibold">
                        View Running Projects
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
