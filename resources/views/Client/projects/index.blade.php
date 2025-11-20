@extends('Client.app')

@section('title', 'My Projects')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Enhanced Header -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Project Portfolio</h1>
                <p class="text-gray-600 mt-2 flex items-center">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Your Projects with us: {{ $projects->count() }}
                </p>
            </div>
            <div class="mt-4 lg:mt-0 flex items-center space-x-3">
                <div class="bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-filter mr-2"></i>All Projects
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    @if($projects->count() > 0)
    <div class="mb-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border border-blue-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700 mb-1">Total Projects</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $projects->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-project-diagram text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-2xl border border-emerald-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-emerald-700 mb-1">Completed</p>
                    <p class="text-3xl font-bold text-emerald-900">{{ $projects->where('status', 'completed')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl border border-orange-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-700 mb-1">Total Tasks</p>
                    <p class="text-3xl font-bold text-orange-900">{{ $projects->sum('tasks_count') }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tasks text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border border-purple-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-700 mb-1">Team Members</p>
                    <p class="text-3xl font-bold text-purple-900">{{ $totalTeamMembers }}</p>
                </div>
                <div class="w-12 h-12 bg-white bg-opacity-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($projects as $project)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <!-- Project Header with Status -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold text-gray-900 truncate">{{ $project->name ?? 'Unnamed Project' }}</h3>
                        <p class="text-gray-500 text-sm mt-1">Created {{ $project->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full border
                        @if($project->status == 'completed') bg-green-50 text-green-700 border-green-200
                        @elseif($project->status == 'in_progress') bg-blue-50 text-blue-700 border-blue-200
                        @elseif($project->status == 'on_hold') bg-yellow-50 text-yellow-700 border-yellow-200
                        @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                        {{ ucfirst(str_replace('_', ' ', $project->status ?? 'pending')) }}
                    </span>
                </div>

                <p class="text-gray-600 text-sm leading-relaxed mb-4">{{ Str::limit($project->description ?? 'No description available', 120) }}</p>

                <!-- Enhanced Progress Bar -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span class="font-medium">Project Progress</span>
                        @php
                            $tasks_count = $project->tasks_count ?? 0;
                            $completed_tasks_count = $project->completed_tasks_count ?? 0;
                            $progress = $tasks_count > 0 ? round(($completed_tasks_count / $tasks_count) * 100) : 0;
                        @endphp
                        <span class="font-semibold">{{ $progress }}% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2.5 rounded-full transition-all duration-1000"
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Project Stats & Details -->
            <div class="p-6">
                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $tasks_count }}</div>
                        <div class="text-xs text-gray-500 font-medium">Tasks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $completed_tasks_count }}</div>
                        <div class="text-xs text-gray-500 font-medium">Done</div>
                    </div>
                    <div class="text-center">
                        @php
                            $team_count = $project->teamMembers ? $project->teamMembers->count() : 0;
                        @endphp
                        <div class="text-2xl font-bold text-blue-600">{{ $team_count }}</div>
                        <div class="text-xs text-gray-500 font-medium">Team</div>
                    </div>
                </div>

                <!-- Project Details -->
                <div class="space-y-3 text-sm">
                    <div class="flex items-center text-gray-600">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user text-blue-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-medium">Manager:</span>
                            <span class="ml-1">{{ $project->manager->name ?? 'Not assigned' }}</span>
                        </div>
                    </div>

                    <div class="flex items-center text-gray-600">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users text-purple-600 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-medium">Team Size:</span>
                            <span class="ml-1">{{ $team_count }} members</span>
                        </div>
                    </div>

                    @if($project->due_date)
                    <div class="flex items-center {{ $project->due_date->isPast() ? 'text-red-600' : 'text-gray-600' }}">
                        <div class="w-8 h-8 {{ $project->due_date->isPast() ? 'bg-red-100' : 'bg-green-100' }} rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-calendar {{ $project->due_date->isPast() ? 'text-red-600' : 'text-green-600' }} text-xs"></i>
                        </div>
                        <div>
                            <span class="font-medium">Due Date:</span>
                            <span class="ml-1">{{ $project->due_date->format('M d, Y') }}</span>
                            @if($project->due_date->isPast())
                            <span class="ml-2 text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full">Overdue</span>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="flex items-center text-gray-600">
                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-calendar text-gray-400 text-xs"></i>
                        </div>
                        <div>
                            <span class="font-medium">Due Date:</span>
                            <span class="ml-1 text-gray-400">Not set</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Action Button -->
                <div class="mt-6">
                    <a href="{{ route('client.projects.show', $project) }}"
                       class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-3 rounded-xl font-medium transition-all duration-200 transform hover:scale-[1.02] shadow-md hover:shadow-lg block">
                        <div class="flex items-center justify-center space-x-2">
                            <span>View Project</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-16">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-project-diagram text-gray-400 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No Projects Assigned</h3>
                <p class="text-gray-500 max-w-md mx-auto text-lg">
                    You don't have any projects assigned at the moment. Projects will appear here once they are assigned to you by your manager.
                </p>
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 max-w-md mx-auto">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <p class="text-sm text-blue-700">
                            Contact your project manager to get assigned to new projects.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Project Status Summary -->
    @if($projects->count() > 0)
    <div class="mt-12 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Status Overview</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @php
                $statusCounts = [
                    'in_progress' => $projects->where('status', 'in_progress')->count(),
                    'completed' => $projects->where('status', 'completed')->count(),
                    'on_hold' => $projects->where('status', 'on_hold')->count(),
                    'planning' => $projects->where('status', 'planning')->count(),
                ];
                
                // Add pending count for any other statuses
                $otherCount = $projects->count() - array_sum($statusCounts);
                $statusCounts['pending'] = $otherCount > 0 ? $otherCount : 0;
            @endphp

            @foreach($statusCounts as $status => $count)
                @if($count > 0)
                <div class="text-center p-4 rounded-xl border
                    @if($status == 'in_progress') border-blue-200 bg-blue-50
                    @elseif($status == 'completed') border-green-200 bg-green-50
                    @elseif($status == 'on_hold') border-yellow-200 bg-yellow-50
                    @elseif($status == 'planning') border-purple-200 bg-purple-50
                    @else border-gray-200 bg-gray-50 @endif">
                    <div class="text-2xl font-bold text-gray-900 mb-1">{{ $count }}</div>
                    <div class="text-sm font-medium text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif
</div>

<style>
    .transform:hover {
        transition: all 0.3s ease;
    }
</style>

<script>
    // Add smooth animations
    document.addEventListener('DOMContentLoaded', function() {
        const projectCards = document.querySelectorAll('.transform');
        
        projectCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    });
</script>
@endsection