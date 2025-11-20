@extends('manager.layouts.app')
@section('content')
<!-- Dashboard Content -->
<div class="flex-1 p-6 bg-[#FCF8F3] overflow-y-auto">
    <!-- System Alert -->
    <div id="systemAlert" class="hidden mb-6 p-4 rounded-lg border bg-primary border-primary text-white slide-in">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span class="font-medium" id="alertMessage">Task updated successfully!</span>
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
                <i class="fas fa-briefcase text-primary text-2xl"></i>
                <h1 class="text-3xl font-bold text-black">Project Dashboard</h1>
            </div>
            <p class="text-gray-600">
                Monitor your projects, track team performance, and manage tasks efficiently
            </p>
        </div>

        <div class="flex items-center space-x-4 mt-4 lg:mt-0">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search projects, tasks..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary w-64 bg-white"
                />
            </div>

            <a href="{{ route('tasks.create') }}"
               class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-[#146c3e] transition-colors font-medium flex items-center space-x-2">
                <i class="fas fa-plus"></i>
                <span>Quick Task</span>
            </a>
        </div>
    </div>

    <!-- Project Overview Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Projects</p>
                    <p class="text-2xl font-bold text-black">{{ $activeProjects }}</p>
                </div>
                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-briefcase text-primary text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-1 mt-2">
                <i class="fas fa-chart-line text-primary text-xs"></i>
                <span class="text-xs text-primary">{{ $projects->count() }} total</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Tasks</p>
                    <p class="text-2xl font-bold text-black">{{ $pendingTasks }}</p>
                </div>
                <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-tasks text-accent text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-1 mt-2">
                <i class="fas fa-clock text-accent text-xs"></i>
                @php
                    $overdueTasks = \App\Models\Tasks::whereIn('project_id', $projects->pluck('id'))
                                                   ->where('due_date', '<', now())
                                                   ->where('status', '!=', 'done')
                                                   ->count();
                @endphp
                <span class="text-xs text-accent">{{ $overdueTasks }} overdue</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team Members</p>
                    <p class="text-2xl font-bold text-black">{{ $teamMembers->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-secondary text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-1 mt-2">
                <i class="fas fa-user-check text-secondary text-xs"></i>
                <span class="text-xs text-secondary">{{ $teamMembers->count() }} active</span>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completion Rate</p>
                    <p class="text-2xl font-bold text-black">{{ $completionRate }}%</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="flex items-center space-x-1 mt-2">
                <i class="fas fa-tasks text-green-600 text-xs"></i>
                <span class="text-xs text-green-600">{{ $completedTasks }}/{{ $totalTasks }} tasks</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column - Projects & Quick Actions -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Active Projects -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">Active Projects</h3>
                    <a href="{{ route('projects.index') }}" class="text-sm text-primary hover:text-[#146c3e] font-medium">
                        View All
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($projects->take(3) as $project)
                    @php
                        $projectTasks = \App\Models\Tasks::where('project_id', $project->id)->get();
                        $completedProjectTasks = $projectTasks->where('status', 'done')->count();
                        $totalProjectTasks = $projectTasks->count();
                        $progress = $totalProjectTasks > 0 ? round(($completedProjectTasks / $totalProjectTasks) * 100) : 0;

                        // Determine status
                        if ($progress >= 80) {
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = 'On Track';
                        } elseif ($progress >= 40) {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'In Progress';
                        } else {
                            $statusClass = 'bg-red-100 text-red-800';
                            $statusText = 'At Risk';
                        }
                    @endphp
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-project-diagram text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-black">{{ $project->name }}</h4>
                                <p class="text-sm text-gray-600">
                                    @if($project->due_date)
                                    Due: {{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }}
                                    @else
                                    No due date
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-primary" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">{{ $progress }}% complete</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-folder-open text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500">No projects found</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h2 class="text-xl font-semibold text-black mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('tasks.create') }}"
                       class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-black text-center">
                            Create Task
                        </span>
                    </a>

                    <a href="{{ route('projects.index') }}"
                       class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-secondary rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-briefcase text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-black text-center">
                            View Projects
                        </span>
                    </a>

                    <a href="{{ route('users.index') }}"
                       class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-accent rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-black text-center">
                            Team Members
                        </span>
                    </a>

                    <a href="{{ route('projects.index') }}"
                       class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-line text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-black text-center">
                            Project Reports
                        </span>
                    </a>

                    <a href="{{ route('tasks.index') }}"
                       class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-tasks text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-black text-center">
                            Manage Tasks
                        </span>
                    </a>

                    <a href="{{ route('time-logs.index') }}"
                       class="quick-action-btn flex flex-col items-center p-4 border border-gray-200 rounded-xl hover:border-primary hover:shadow-md transition-all group">
                        <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <span class="text-sm font-medium text-black text-center">
                            Time Logs
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column - Team & Recent Activity -->
        <div class="space-y-8">
            <!-- Team Availability -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">Team Members</h3>
                    <a href="{{ route('users.index') }}" class="text-sm text-primary hover:text-[#146c3e] font-medium">
                        View All
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($teamMembers->take(3) as $member)
                    @php
                        $memberTasks = \App\Models\Tasks::where('assigned_to', $member->id)
                                                      ->whereIn('project_id', $projects->pluck('id'))
                                                      ->where('status', '!=', 'done')
                                                      ->count();
                        $status = $memberTasks > 3 ? 'Busy' : 'Available';
                        $statusClass = $memberTasks > 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                    @endphp
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-black">{{ $member->name }}</p>
                                <p class="text-xs text-gray-600">{{ $member->email }}</p>
                            </div>
                        </div>
                        <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                            {{ $status }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-users text-gray-400 text-xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No team members</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Upcoming Deadlines -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">Upcoming Deadlines</h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-primary hover:text-[#146c3e] font-medium">
                        View All
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($upcomingDeadlines as $task)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-black">{{ $task->title }}</p>
                            <p class="text-xs text-gray-600">{{ $task->project->name ?? 'No Project' }}</p>
                        </div>
                        <div class="text-right">
                            @if($task->due_date < now())
                                <p class="text-sm font-semibold text-accent">Overdue</p>
                                @php
    $due = \Carbon\Carbon::parse($task->due_date);
@endphp

                            @elseif($task->due_date->isToday())
                                <p class="text-sm font-semibold text-orange-500">Today</p>
                            @elseif($task->due_date->isTomorrow())
                                <p class="text-sm font-semibold text-yellow-600">Tomorrow</p>
                            @else
                                <p class="text-sm font-semibold text-black">{{ $task->due_date->format('M d') }}</p>
                            @endif
                            <p class="text-xs text-gray-600">{{ $task->due_date->format('g:i A') }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-flag text-gray-400 text-xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No upcoming deadlines</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">Recent Activity</h3>
                    <a href="{{ route('tasks.index') }}" class="text-sm text-primary hover:text-[#146c3e] font-medium">
                        View All
                    </a>
                </div>
                <div class="space-y-3 max-h-64 overflow-y-auto scrollbar-custom">
                    @forelse($recentTasks as $task)

                      @php
        $assignedUserName = 'System';
        if ($task->assigned_to) {
            $user = \App\Models\User::find($task->assigned_to);
            $assignedUserName = $user ? $user->name : 'System';
        }
    @endphp
                    <div class="activity-item flex items-start space-x-3 p-3 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition-colors">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            @if($task->status == 'done') text-green-600 bg-green-100
                            @elseif($task->status == 'in_progress') text-blue-600 bg-blue-100
                            @else text-gray-600 bg-gray-100 @endif">
                            <i class="fas
                                @if($task->status == 'done') fa-check-circle
                                @elseif($task->status == 'in_progress') fa-tasks
                                @else fa-clock @endif"></i>
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-black">
                                <span class="font-semibold">{{ $assignedUserName ?? 'system'}}</span>
                                <span class="text-gray-600">
                                    @if($task->status == 'done')
                                    completed "{{ $task->title }}"
                                    @elseif($task->status == 'in_progress')
                                    started "{{ $task->title }}"
                                    @else
                                    created "{{ $task->title }}"
                                    @endif
                                </span>
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $task->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="fas fa-inbox text-gray-400 text-xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple alert system
function showAlert(message) {
    const alert = document.getElementById('systemAlert');
    const alertMessage = document.getElementById('alertMessage');
    alertMessage.textContent = message;
    alert.classList.remove('hidden');
    setTimeout(hideAlert, 5000);
}

function hideAlert() {
    document.getElementById('systemAlert').classList.add('hidden');
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    // You can implement live search here if needed
    console.log('Searching for:', searchTerm);
});
</script>

<style>
.scrollbar-custom {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.scrollbar-custom::-webkit-scrollbar {
    width: 6px;
}

.scrollbar-custom::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

.scrollbar-custom::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

.scrollbar-custom::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

.hover-scale {
    transition: transform 0.2s ease-in-out;
}

.hover-scale:hover {
    transform: scale(1.02);
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
