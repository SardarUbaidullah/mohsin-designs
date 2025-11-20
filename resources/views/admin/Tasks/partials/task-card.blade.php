<div
    class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-all duration-200
    @if ($status === 'in_progress') border-blue-200 @endif
    @if ($status === 'done') border-green-200 @endif">
    <!-- Task Header -->
    <div class="flex items-start justify-between mb-3">
        <h4
            class="font-semibold text-gray-900 text-sm leading-tight
            @if ($status === 'done') line-through @endif">
            {{ $task->title }}
        </h4>
        <div class="flex space-x-1">
            <a href="{{ route('tasks.edit', $task->id) }}"
                class="text-gray-400 hover:text-blue-600 p-1 rounded hover:bg-blue-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Task Description -->
    @if ($task->description)
        <p
            class="text-gray-600 text-xs mb-4 line-clamp-2
        @if ($status === 'done') line-through @endif">
            {{ $task->description }}
        </p>
    @endif

    <!-- Project & Assignee -->
    <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
        <div class="flex items-center space-x-3">
            @if ($task->project)
                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded-lg font-medium">
                    {{ $task->project->name }}
                </span>
            @endif
            @if ($task->assignee)
                <span class="flex items-center">
                    <div
                        class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium mr-1">
                        {{ strtoupper(substr($task->assignee->name, 0, 1)) }}
                    </div>
                    {{ $task->assignee->name }}
                </span>
            @else
                <span class="text-gray-400">Unassigned</span>
            @endif
        </div>
        <span class="text-gray-400">#{{ $task->id }}</span>
    </div>

    <!-- Priority & Due Date -->
    <div class="flex items-center justify-between mb-4">
        <div>
            @if ($task->priority == 'high')
                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-red-100 text-red-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z"
                            clip-rule="evenodd" />
                    </svg>
                    High
                </span>
            @elseif($task->priority == 'medium')
                <span
                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-yellow-100 text-yellow-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    Medium
                </span>
            @elseif($task->priority == 'low')
                <span
                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z"
                            clip-rule="evenodd" />
                    </svg>
                    Low
                </span>
            @endif
        </div>
        @if ($task->due_date)
            <span class="text-xs text-gray-500 flex items-center">
                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
            </span>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
        <a href="{{ route('tasks.show', $task->id) }}"
            class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            View
        </a>
        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to delete this task?')"
                class="text-red-600 hover:text-red-800 text-xs font-medium flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete
            </button>
        </form>
    </div>
</div>
