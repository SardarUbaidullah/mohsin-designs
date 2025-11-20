<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-3 sm:p-4 hover:shadow-md transition-shadow duration-200
    @if($status === 'in_progress') border-yellow-200 @endif
    @if($status === 'completed') border-green-200 @endif">
    <div class="flex justify-between items-start mb-2 sm:mb-3">
        <h4 class="font-semibold text-gray-900 text-sm truncate flex-1 mr-2">{{ $project->name }}</h4>
        <div class="dropdown relative flex-shrink-0">
            <button class="w-5 h-5 sm:w-6 sm:h-6 hover:bg-gray-100 rounded flex items-center justify-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
            </button>
        </div>
    </div>

    <p class="text-gray-600 text-xs mb-2 sm:mb-3 line-clamp-2">
        {{ $project->description ?: 'No description provided' }}
    </p>

    <div class="flex items-center justify-between text-xs text-gray-500 mb-2 sm:mb-3">
        <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
            <span class="flex items-center truncate">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                @if($status === 'pending')
                    {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M d') : 'No date' }}
                @elseif($status === 'in_progress')
                    {{ $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('M d') : 'No due date' }}
                @else
                    Completed
                @endif
            </span>
        </div>
        <span class="text-gray-400 text-xs flex-shrink-0 ml-2">#{{ $project->id }}</span>
    </div>

    <!-- Progress Bar -->
    @php
        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->where('status', 'done')->count();
        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $progressWidth = $status === 'pending' ? 0 : ($status === 'completed' ? 100 : $progress);
        $progressColor = $status === 'pending' ? 'bg-gray-400' : ($status === 'completed' ? 'bg-green-500' : 'bg-yellow-500');
    @endphp
    <div class="mb-2 sm:mb-3">
        <div class="flex justify-between text-xs text-gray-500 mb-1">
            <span>Progress</span>
            <span>{{ $progressWidth }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-1.5">
            <div class="{{ $progressColor }} h-1.5 rounded-full" style="width: {{ $progressWidth }}%"></div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center justify-between pt-2 sm:pt-3 border-t border-gray-100">
        <div class="flex space-x-1 sm:space-x-2">
            <a href="{{ route('projects.show', $project->id) }}" class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View
            </a>
            <a href="{{ route('projects.edit', $project->id) }}" class="text-gray-600 hover:text-gray-800 text-xs font-medium flex items-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
        </div>
        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Are you sure you want to delete this project?')" class="text-red-600 hover:text-red-800 text-xs font-medium flex items-center">
                <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete
            </button>
        </form>
    </div>
</div>
