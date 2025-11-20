<div class="task-card bg-white rounded-xl shadow-sm border border-gray-200/60 p-4 cursor-move hover:shadow-md transition-all duration-300 group"
     data-task-id="{{ $task->id }}">
    <!-- Task Header -->
    <div class="flex items-start justify-between mb-3">
        <div class="flex-1 min-w-0">
            <h4 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 group-hover:text-green-600 transition-colors duration-200">
                <a href="{{ route('manager.tasks.show', $task->id) }}" class="hover:underline">
                    {{ $task->title }}
                </a>
            </h4>
            <p class="text-xs text-gray-500 line-clamp-2">{{ Str::limit($task->description, 80) ?: 'No description' }}</p>
        </div>
    </div>

    <!-- Project & Assignee -->
    <div class="flex items-center justify-between mb-3 text-xs">
        <span class="font-medium text-gray-700 bg-gray-100 px-2 py-1 rounded-lg">
            {{ $task->project->name }}
        </span>
        <span class="text-gray-500 flex items-center">
            @if($task->assignee)
                <div class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-semibold text-xs mr-1">
                    {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                </div>
                {{ $task->assignee->name }}
            @else
                Unassigned
            @endif
        </span>
    </div>

    <!-- Priority & Due Date -->
    <div class="flex items-center justify-between mb-3">
        @if($task->priority)
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                @if($task->priority == 'high') bg-red-100 text-red-800
                @elseif($task->priority == 'medium') bg-yellow-100 text-yellow-800
                @else bg-gray-100 text-gray-800 @endif">
                @if($task->priority == 'high') 🔥
                @elseif($task->priority == 'medium') ⚡
                @else 💤 @endif
                {{ ucfirst($task->priority) }}
            </span>
        @endif

        @if($task->due_date)
            <span class="text-xs font-medium @if(\Carbon\Carbon::parse($task->due_date)->isPast() && $task->status != 'done') text-red-600 @else text-gray-600 @endif">
                {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
            </span>
        @endif
    </div>

    <!-- Milestone (if exists) -->
    @if($task->milestone)
        <div class="mb-3">
            <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                {{ $task->milestone->title }}
            </span>
        </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
        <div class="flex items-center space-x-2">
            <a href="{{ route('manager.tasks.show', $task->id) }}"
               class="text-blue-600 hover:text-blue-800 p-1 rounded transition duration-200"
               title="View Task">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </a>
            <a href="{{ route('manager.tasks.edit', $task->id) }}"
               class="text-yellow-600 hover:text-yellow-800 p-1 rounded transition duration-200"
               title="Edit Task">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </a>
        </div>

        <!-- Created Date -->
        <span class="text-xs text-gray-400">
            {{ $task->created_at->format('M d') }}
        </span>
    </div>
</div>
