@extends('team.app')

@section('content')
<div class="min-h-screen bg-gray-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Dashboard Overview</h1>
                    <p class="text-gray-600 mt-2 flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Welcome back, {{ Auth::user()->name }}! Here's your work summary.
                    </p>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Notification Bell -->
                    @include('components.notifications')

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-user-shield mr-1.5 text-xs"></i>
                        Team Member
                    </span>
                    <span class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Tasks Card -->
            <div class="bg-white rounded-xl border border-gray-200/60 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Total Tasks</p>
                        <p class="text-3xl font-bold text-gray-900">{{ $userStats['total_tasks'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">All assigned tasks</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-trending-up text-green-500 mr-1.5"></i>
                        <span>Active assignments</span>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks Card -->
            <div class="bg-white rounded-xl border border-gray-200/60 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Pending Tasks</p>
                        <p class="text-3xl font-bold text-orange-600">{{ $userStats['pending_tasks'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">Require attention</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-clock text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-exclamation-circle text-orange-500 mr-1.5"></i>
                        <span>Needs completion</span>
                    </div>
                </div>
            </div>

            <!-- Completed Tasks Card -->
            <div class="bg-white rounded-xl border border-gray-200/60 p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                        <p class="text-3xl font-bold text-green-600">{{ $userStats['completed_tasks'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">Finished tasks</p>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center text-sm text-green-600">
                        <i class="fas fa-trophy mr-1.5"></i>
                        <span>Great progress!</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Recent Tasks Section -->
       <div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-list-check text-blue-600 mr-3"></i>
                Recent Tasks
            </h2>
            <span class="text-sm text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                {{ count($tasks ?? []) }} items
            </span>
        </div>
    </div>
    <div class="p-6">
        @if(isset($tasks) && count($tasks) > 0)
        <div class="space-y-4">
            @foreach($tasks as $task)
            <div class="group p-4 border border-gray-200/60 rounded-lg hover:border-blue-300 hover:bg-blue-50/30 transition-all duration-200 cursor-pointer">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-2">
                            <h3 class="font-semibold text-gray-900 truncate group-hover:text-blue-700 transition-colors">
                                {{ $task->title }}
                            </h3>
                            @if($task->priority === 'high')
                            <span class="flex-shrink-0">
                                <i class="fas fa-arrow-up text-red-500 text-xs"></i>
                            </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            {{ Str::limit($task->description ?? 'No description', 80) }}
                        </p>
                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-project-diagram mr-1.5 text-gray-400"></i>
                                {{ $task->project->name ?? 'No Project' }}
                            </span>
                            <span class="flex items-center">
                                <i class="far fa-clock mr-1.5 text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($task->created_at)->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                            @if($task->status === 'done') bg-green-100 text-green-800
                            @elseif($task->status === 'in_progress') bg-blue-100 text-blue-800
                            @elseif($task->status === 'todo') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($task->status === 'done') Completed
                            @elseif($task->status === 'in_progress') In Progress
                            @elseif($task->status === 'todo') To Do
                            @else {{ $task->status }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-inbox text-gray-400 text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm">No recent tasks found</p>
            <p class="text-gray-400 text-xs mt-1">Tasks assigned to you will appear here</p>
        </div>
        @endif
    </div>
</div>

<!-- Upcoming Deadlines Section -->
<div class="bg-white rounded-xl border border-gray-200/60 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-calendar-day text-orange-600 mr-3"></i>
                Upcoming Deadlines
            </h2>
            <span class="text-sm text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full font-medium">
                {{ count($upcomingDeadlines ?? []) }} due soon
            </span>
        </div>
    </div>
    <div class="p-6">
        @if(isset($upcomingDeadlines) && count($upcomingDeadlines) > 0)
        <div class="space-y-4">
            @foreach($upcomingDeadlines as $task)
            @php
                $dueDate = \Carbon\Carbon::parse($task->due_date);
                $isUrgent = $dueDate->diffInDays(now()) <= 1;
                $isToday = $dueDate->isToday();
            @endphp
            <div class="group p-4 border border-gray-200/60 rounded-lg hover:shadow-md transition-all duration-200
                {{ $isUrgent ? 'border-l-4 border-l-red-500 bg-red-50/30' : '' }}
                {{ $isToday ? 'border-l-4 border-l-orange-500 bg-orange-50/30' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2 mb-2">
                            <h3 class="font-semibold text-gray-900 truncate">
                                {{ $task->title }}
                            </h3>
                            @if($isToday)
                            <span class="flex-shrink-0">
                                <i class="fas fa-bolt text-orange-500 text-xs"></i>
                            </span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-4 text-sm text-gray-600 mb-3">
                            <span class="flex items-center font-medium
                                @if($isUrgent) text-red-600
                                @elseif($isToday) text-orange-600
                                @else text-gray-600 @endif">
                                <i class="fas fa-calendar-alt mr-1.5 text-xs"></i>
                                Due: {{ $dueDate->format('M d, Y') }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $dueDate->diffForHumans() }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium
                                @if($task->priority == 'high') bg-red-100 text-red-800
                                @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                <i class="fas fa-flag mr-1 text-xs"></i>
                                {{ ucfirst($task->priority) }}
                            </span>
                            <span class="text-xs text-gray-500">
                                {{ $task->project->name ?? 'No Project' }}
                            </span>
                        </div>
                    </div>
                    @if($isUrgent)
                    <div class="ml-4 flex-shrink-0">
                        <span class="animate-pulse inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Urgent
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
            </div>
            <p class="text-gray-500 text-sm">No upcoming deadlines</p>
            <p class="text-gray-400 text-xs mt-1">You're all caught up!</p>
        </div>
        @endif
    </div>
</div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('team.tasks.index') }}" class="group p-4 bg-white border border-gray-200/60 rounded-xl text-center hover:border-blue-300 hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:bg-blue-500 transition-colors">
                    <i class="fas fa-tasks text-blue-600 group-hover:text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">View All Tasks</p>
                <p class="text-xs text-gray-500 mt-1">Manage assignments</p>
            </a>

            <a href="{{ route('team.projects') }}" class="group p-4 bg-white border border-gray-200/60 rounded-xl text-center hover:border-green-300 hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:bg-green-500 transition-colors">
                    <i class="fas fa-project-diagram text-green-600 group-hover:text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">My Projects</p>
                <p class="text-xs text-gray-500 mt-1">Project overview</p>
            </a>

            <a href="{{ route('team.chat.index') }}" class="group p-4 bg-white border border-gray-200/60 rounded-xl text-center hover:border-purple-300 hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:bg-purple-500 transition-colors">
                    <i class="fas fa-comments text-purple-600 group-hover:text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Team Chat</p>
                <p class="text-xs text-gray-500 mt-1">Communicate</p>
            </a>

            <a href="{{ route('profile.edit') }}" class="group p-4 bg-white border border-gray-200/60 rounded-xl text-center hover:border-orange-300 hover:shadow-md transition-all duration-200">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:bg-orange-500 transition-colors">
                    <i class="fas fa-user text-orange-600 group-hover:text-white"></i>
                </div>
                <p class="text-sm font-medium text-gray-900">Profile</p>
                <p class="text-xs text-gray-500 mt-1">Account settings</p>
            </a>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
