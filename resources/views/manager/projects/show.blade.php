
@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)

@section("content")
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('manager.projects.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
            </div>
            <p class="text-gray-600">{{ $project->description ?: 'No description provided' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('manager.milestones.create', ['project_id' => $project->id]) }}"
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Milestone
            </a>
            <a href="{{ route('manager.tasks.create', ['project_id' => $project->id]) }}"
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Task
            </a>
            <a href="{{ route('manager.projects.edit', $project->id) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Project
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Project Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Left Column - Project Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Project Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Project Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Basic Details</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Project Name</dt>
                                <dd class="text-sm text-gray-900">{{ $project->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Status</dt>
                                <dd>
                                    @if($project->status == 'completed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Completed
                                        </span>
                                    @elseif($project->status == 'in_progress')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            In Progress
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Pending
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Manager</dt>
                                <dd class="text-sm text-gray-900">{{ $project->manager->name ?? 'Not assigned' }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Timeline</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Start Date</dt>
                                <dd class="text-sm text-gray-900">
                                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d, Y') : 'Not set' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Due Date</dt>
                                <dd class="text-sm text-gray-900">
                                    @if($project->due_date)
                                        @if(\Carbon\Carbon::parse($project->due_date)->isPast())
                                            <span class="text-red-600 font-medium">
                                                {{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }} (Overdue)
                                            </span>
                                        @else
                                            {{ \Carbon\Carbon::parse($project->due_date)->format('M d, Y') }}
                                        @endif
                                    @else
                                        Not set
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-600">Created</dt>
                                <dd class="text-sm text-gray-900">{{ $project->created_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

      @if($project->description)
<div class="mt-6">
    <h3 class="text-sm font-medium text-gray-500 mb-2">Description</h3>
    <div class="text-sm text-gray-700 bg-gray-50 rounded-lg p-4 overflow-x-auto">
        <div class="whitespace-pre-line break-words min-w-0">
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
                e($project->description)) 
            !!}
        </div>
    </div>
</div>
@endif
            </div>

            <!-- Milestones Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Project Milestones
                    </h2>
                    <a href="{{ route('manager.milestones.create', ['project_id' => $project->id]) }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Milestone
                    </a>
                </div>

                @php
                    $milestones = $project->milestones;
                    $totalMilestones = $milestones->count();
                    $completedMilestones = $milestones->where('status', 'completed')->count();
                    $milestoneProgress = $totalMilestones > 0 ? round(($completedMilestones / $totalMilestones) * 100) : 0;
                @endphp

                <!-- Milestone Progress -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Milestone Progress</span>
                        <span class="text-sm text-gray-600">{{ $milestoneProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full bg-purple-600 transition-all duration-500"
                            style="width: {{ $milestoneProgress }}%">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $completedMilestones }} completed</span>
                        <span>{{ $totalMilestones }} total</span>
                    </div>
                </div>

                <!-- Milestones List -->
                <div class="space-y-4">
                    @forelse($milestones as $milestone)
                    @php
                        $milestoneTasks = $milestone->tasks ?? collect();
                        $totalTasks = $milestoneTasks->count();
                        $completedTasks = $milestoneTasks->where('status', 'done')->count();
                        $taskProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    @endphp

                    <div class="border border-gray-200 rounded-lg p-5 hover:shadow-md transition-all duration-200">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-semibold text-gray-900">{{ $milestone->title }}</h4>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        {{ $milestone->status == 'completed' ? 'bg-green-100 text-green-800' :
                                           ($milestone->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($milestone->status) }}
                                    </span>
                                </div>

                                @if($milestone->due_date)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Due: {{ \Carbon\Carbon::parse($milestone->due_date)->format('M d, Y') }}
                                    @if($milestone->due_date < now() && $milestone->status != 'completed')
                                    <span class="ml-2 text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        Overdue
                                    </span>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <div class="text-right ml-4">
                                <div class="text-2xl font-bold text-purple-600">{{ $taskProgress }}%</div>
                                <div class="text-sm text-gray-500">Task Progress</div>
                            </div>
                        </div>

                        <!-- Task Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div class="h-2 rounded-full transition-all duration-500
                                {{ $taskProgress == 100 ? 'bg-green-600' : 'bg-purple-600' }}"
                                style="width: {{ $taskProgress }}%">
                            </div>
                        </div>

                        <!-- Associated Tasks -->
                        <div class="mt-4">
                            <div class="flex items-center justify-between mb-3">
                                <h5 class="font-medium text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Tasks ({{ $totalTasks }})
                                </h5>
                                <span class="text-sm text-gray-500">
                                    {{ $completedTasks }} completed
                                </span>
                            </div>

                            <div class="space-y-2">
                                @forelse($milestoneTasks->take(3) as $task)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <input type="checkbox"
                                               {{ $task->status == 'done' ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-600">
                                        <span class="{{ $task->status == 'done' ? 'line-through text-gray-500' : 'text-gray-700' }} text-sm">
                                            {{ $task->title }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($task->priority)
                                        <span class="text-xs px-2 py-1 rounded
                                            {{ $task->priority == 'high' ? 'bg-red-100 text-red-800' :
                                               ($task->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-4 text-gray-500">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-sm">No tasks assigned to this milestone</p>
                                </div>
                                @endforelse

                                @if($totalTasks > 3)
                                <div class="text-center">
                                    <a href="{{ route('manager.tasks.index', ['project_id' => $project->id, 'milestone_id' => $milestone->id]) }}"
                                       class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                        View all {{ $totalTasks }} tasks
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center justify-between pt-4 mt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                Last updated: {{ $milestone->updated_at->diffForHumans() }}
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('manager.milestones.edit', $milestone->id) }}"
                                   class="text-purple-600 hover:text-purple-800 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('manager.milestones.destroy', $milestone->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('Delete this milestone?')"
                                            class="text-red-600 hover:text-red-800 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-500 mb-2">No milestones found for this project</p>
                        <p class="text-gray-400 text-sm mb-4">Create milestones to track major project phases</p>
                        <a href="{{ route('manager.milestones.create', ['project_id' => $project->id]) }}"
                           class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200 inline-flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create First Milestone
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Tasks Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Project Tasks</h2>
                    <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}"
                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Tasks
                    </a>
                </div>

                @php
                    $tasks = $project->tasks->take(5);
                    $totalTasks = $project->tasks->count();
                    $completedTasks = $project->tasks->where('status', 'done')->count();
                    $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                @endphp

                <!-- Progress Bar -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                        <span class="text-sm text-gray-600">{{ $progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full
                            @if($progress >= 70) bg-green-500
                            @elseif($progress >= 40) bg-yellow-500
                            @else bg-red-500 @endif"
                            style="width: {{ $progress }}%">
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $completedTasks }} completed</span>
                        <span>{{ $totalTasks }} total</span>
                    </div>
                </div>

                <!-- Tasks List -->
                <div class="space-y-3">
                    @forelse($tasks as $task)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $task->title }}</h4>
                                <p class="text-xs text-gray-500">
                                    Assigned to: {{ $task->assignee->name ?? 'Unassigned' }}
                                    @if($task->due_date)
                                        • Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($task->priority)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($task->priority == 'high') bg-red-100 text-red-800
                                @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($task->priority) }}
                            </span>
                            @endif
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                @if($task->status == 'done') bg-green-100 text-green-800
                                @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500">No tasks found for this project</p>
                        <a href="{{ route('manager.tasks.create', ['project_id' => $project->id]) }}"
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium mt-2 inline-block">
                            Create your first task
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column - Statistics & Team -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Statistics</h3>
                <div class="space-y-4">
                    @php
                        $teamMembers = \App\Models\User::whereHas('assignedTasks', function($q) use ($project) {
                            $q->where('project_id', $project->id);
                        })->distinct()->count();

                        $pendingTasks = $project->tasks->where('status', 'todo')->count();
                        $inProgressTasks = $project->tasks->where('status', 'in_progress')->count();
                        $completedTasks = $project->tasks->where('status', 'done')->count();
                    @endphp

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Team Members</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $teamMembers }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending Tasks</span>
                        <span class="text-lg font-semibold text-yellow-600">{{ $pendingTasks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">In Progress</span>
                        <span class="text-lg font-semibold text-blue-600">{{ $inProgressTasks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-lg font-semibold text-green-600">{{ $completedTasks }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Milestones</span>
                        <span class="text-lg font-semibold text-purple-600">{{ $totalMilestones }}</span>
                    </div>
                </div>
            </div>

            <!-- Team Members -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Team Members</h3>
                    <span class="text-sm text-gray-500">{{ $teamMembers }} members</span>
                </div>
                <div class="space-y-3">
                    @php
                        $teamMembersList = \App\Models\User::whereHas('assignedTasks', function($q) use ($project) {
                            $q->where('project_id', $project->id);
                        })->distinct()->take(5)->get();
                    @endphp

                    @forelse($teamMembersList as $member)
                    <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition duration-150">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm">
                            {{ strtoupper(substr($member->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                            <p class="text-xs text-gray-500">{{ $member->email }}</p>
                        </div>
                        <div class="text-xs text-gray-500">
                            @php
                                $memberTasks = $member->assignedTasks()->where('project_id', $project->id)->count();
                            @endphp
                            {{ $memberTasks }} tasks
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-gray-500 text-sm">No team members assigned</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('manager.milestones.create', ['project_id' => $project->id]) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-purple-50 rounded-lg transition duration-150 border border-purple-200">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Add Milestone</span>
                    </a>
                    <a href="{{ route('manager.tasks.create', ['project_id' => $project->id]) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Add New Task</span>
                    </a>
                    <a href="{{ route('manager.tasks.index', ['project_id' => $project->id]) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>Manage Tasks</span>
                    </a>
                    <a href="{{ route('manager.team.index') }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span>View Team</span>
                    </a>
                    <a href="{{ route('manager.chat.project', $project) }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Project Chat</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<x-comments
    :commentable="$project"
    commentableType="project"
    :showInternal="auth()->user()->role !== 'client'"
/>
@endsection
