@extends('Client.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Enhanced Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900" aria-label="Dashboard Overview">Dashboard Overview</h1>
                <p class="text-gray-600 mt-2 flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    Welcome back, {{ auth()->user()->name }}
                </p>
            </div>
            <div class="text-sm text-gray-500 bg-gray-50 px-3 py-2 rounded-lg">
                <i class="fas fa-calendar-day mr-2"></i>
                {{ now()->format('F j, Y') }}
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border border-blue-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-blue-700 mb-2">Active Projects</h3>
                    <p class="text-3xl font-bold text-blue-900">{{ $projects->count() }}</p>
                    <p class="text-xs text-blue-600 mt-1">Currently working</p>
                </div>
                <div class="w-14 h-14 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-project-diagram text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl border border-emerald-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-emerald-700 mb-2">Total Tasks</h3>
                    <p class="text-3xl font-bold text-emerald-900">{{ $projects->sum('tasks_count') }}</p>
                    <p class="text-xs text-emerald-600 mt-1">Across all projects</p>
                </div>
                <div class="w-14 h-14 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-emerald-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border border-purple-200 p-6 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-medium text-purple-700 mb-2">Completed Tasks</h3>
                    @php
                        $totalTasks = $projects->sum('tasks_count');
                        $completedTasks = $projects->sum('completed_tasks_count');
                        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;
                    @endphp
                    <p class="text-3xl font-bold text-purple-900">{{ $completedTasks }}</p>
                    <p class="text-xs text-purple-600 mt-1">{{ $completionRate }}% completion rate</p>
                </div>
                <div class="w-14 h-14 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Enhanced Projects Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Your Projects</h3>
                        <p class="text-sm text-gray-600 mt-1">Active project overview</p>
                    </div>
                    <a href="{{ route('client.projects') }}"
                       class="text-primary-600 hover:text-primary-800 text-sm font-medium flex items-center space-x-1 bg-white px-3 py-2 rounded-lg border border-gray-300 hover:border-primary-300 transition-colors">
                        <span>View All</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                @forelse($projects as $project)
                <div class="border border-gray-200 rounded-xl p-5 mb-4 last:mb-0 hover:border-primary-300 transition-all duration-200 card-hover">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 text-lg">{{ $project->name }}</h4>
                                <p class="text-sm text-gray-500">Manager: {{ $project->manager->name ?? 'Not assigned' }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 text-xs font-medium rounded-full
                            @if($project->status == 'completed') bg-green-100 text-green-800 border border-green-200
                            @elseif($project->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                            @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->status ?? 'pending')) }}
                        </span>
                    </div>

                    <p class="text-gray-600 mb-4 text-sm leading-relaxed">{{ Str::limit($project->description ?? 'No description available', 120) }}</p>

                    <!-- Enhanced Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-2">
                            <span class="font-medium">Project Progress</span>
                            @php
                                $tasks_count = $project->tasks_count ?? 0;
                                $completed_tasks_count = $project->completed_tasks_count ?? 0;
                                $progress = $tasks_count > 0 ? ($completed_tasks_count / $tasks_count) * 100 : 0;
                            @endphp
                            <span class="font-semibold">{{ $completed_tasks_count }}/{{ $tasks_count }} tasks</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2.5 rounded-full progress-bar"
                                 style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="text-right text-xs text-gray-500 mt-1">{{ round($progress) }}% complete</div>
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="flex items-center space-x-1">
                                <i class="fas fa-clock text-xs"></i>
                                <span>Updated {{ $project->updated_at->diffForHumans() }}</span>
                            </span>
                        </div>
                        <a href="{{ route('client.projects.show', $project) }}"
                           class="text-primary-600 hover:text-primary-800 font-medium text-sm flex items-center space-x-1 transition-colors">
                            <span>View Details</span>
                            <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-project-diagram text-gray-400 text-2xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No Projects Yet</h4>
                    <p class="text-gray-500 max-w-sm mx-auto">You don't have any active projects at the moment. Projects will appear here once assigned.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Enhanced Right Column -->
        <div class="space-y-6">
            <!-- Enhanced Upcoming Deadlines -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Upcoming Deadlines</h3>
                            <p class="text-sm text-gray-600 mt-1">Tasks requiring attention</p>
                        </div>
                        <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hourglass-half text-orange-600 text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($upcomingDeadlines as $task)
                    <div class="border border-gray-200 rounded-xl p-4 mb-4 last:mb-0 hover:border-orange-300 transition-all duration-200">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="font-medium text-gray-900 text-sm leading-tight">{{ $task->title }}</h4>
                            @if($task->due_date)
                            <span class="text-xs px-2 py-1 rounded-full font-medium flex items-center space-x-1
                                @if($task->due_date->diffInDays(now()) <= 2) bg-red-100 text-red-800 border border-red-200
                                @elseif($task->due_date->diffInDays(now()) <= 7) bg-yellow-100 text-yellow-800 border border-yellow-200
                                @else bg-green-100 text-green-800 border border-green-200 @endif">
                                <i class="fas fa-clock text-xs"></i>
                                <span>{{ $task->due_date->format('M d') }}</span>
                            </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-3 leading-relaxed">{{ Str::limit($task->description ?? 'No description', 80) }}</p>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span class="bg-gray-100 px-2 py-1 rounded">Project: {{ $task->project->name }}</span>
                            @if($task->due_date)
                            <span>{{ $task->due_date->diffForHumans() }}</span>
                            @else
                            <span>No due date</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">All caught up! No upcoming deadlines.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Enhanced Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                            <p class="text-sm text-gray-600 mt-1">Latest project updates</p>
                        </div>
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bell text-blue-600 text-sm"></i>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @forelse($recentActivities as $activity)
                    <div class="border-b border-gray-100 pb-4 mb-4 last:border-b-0 last:mb-0 last:pb-0">
                        <div class="flex items-start space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <i class="fas fa-comment text-white text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                @if(is_array($activity))
                                    <p class="text-sm text-gray-900 mb-1 leading-tight">
                                        {!! $activity['message'] ?? 'Activity update' !!}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                            {{ isset($activity['time']) ? $activity['time']->diffForHumans() : 'Recently' }}
                                        </span>
                                        <span class="text-xs text-primary-600 font-medium">View ?</span>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-900 mb-1 leading-tight">
                                        <span class="font-semibold text-blue-600">{{ $activity->user->name ?? 'User' }}</span> commented on project
                                    </p>
                                    <p class="text-sm text-gray-600 mb-2 leading-relaxed">{{ Str::limit($activity->content ?? 'No content', 90) }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $activity->created_at->diffForHumans() }}</span>
                                        <span class="text-xs text-primary-600 font-medium">View ?</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-comments text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">No recent activity to display</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="loading-indicator" class="fixed inset-0 bg-white bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600"></div>
</div>

<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .progress-bar {
        transition: width 0.8s ease-in-out;
    }
</style>

<script>
    // Simple loading state management
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('a');
        const loadingIndicator = document.getElementById('loading-indicator');
        
        links.forEach(link => {
            link.addEventListener('click', function() {
                if (this.getAttribute('href') && !this.getAttribute('href').startsWith('#')) {
                    loadingIndicator.classList.remove('hidden');
                }
            });
        });
        
        // Hide loading indicator when page fully loads
        window.addEventListener('load', function() {
            loadingIndicator.classList.add('hidden');
        });
    });
</script>
@endsection