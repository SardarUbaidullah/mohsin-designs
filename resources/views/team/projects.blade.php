@php
    $layout = match(Auth::user()->role) {
        'super_admin' => 'admin.layouts.app',
        'admin' => 'Manager.layouts.app',
        'user' => 'team.app',
    };
@endphp
@extends($layout)
@section('content')
<div class="min-h-screen bg-gray-50/30 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-briefcase text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Related Projects</h1>
                            <p class="text-gray-600 mt-1 flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Projects where you have tasks assigned
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="flex flex-wrap gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $projects->count() }}</div>
                        <div class="text-sm text-gray-500">Total Projects</div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Projects Grid -->
        @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($projects as $project)
            @php
                $userTasks = $project->tasks->where('assigned_to', auth()->id());
                $completedTasks = $userTasks->where('status', 'completed')->count();
                $totalTasks = $userTasks->count();
                $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                $dueDate = $project->due_date ? \Carbon\Carbon::parse($project->due_date) : null;
                $isOverdue = $dueDate && $dueDate->isPast() && $project->status !== 'completed';
                $isDueSoon = $dueDate && $dueDate->diffInDays(now()) <= 7 && !$isOverdue;
            @endphp

            <div class="group bg-white rounded-2xl border border-gray-200/60 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
                <!-- Project Header -->
                <div class="p-6 border-b border-gray-200/60 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-xl font-bold text-gray-900 truncate group-hover:text-indigo-700 transition-colors mb-2">
                                {{ $project->name }}
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    @if($project->status == 'completed') bg-green-100 text-green-800 border border-green-200
                                    @elseif($project->status == 'in_progress') bg-blue-100 text-blue-800 border border-blue-200
                                    @elseif($project->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-200
                                    @else bg-gray-100 text-gray-800 border border-gray-200 @endif">
                                    <i class="fas
                                        @if($project->status == 'completed') fa-check-circle
                                        @elseif($project->status == 'in_progress') fa-spinner fa-spin
                                        @else fa-clock @endif mr-1.5 text-xs">
                                    </i>
                                    {{ str_replace('_', ' ', ucfirst($project->status)) }}
                                </span>
                                @if($project->manager)
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                    by {{ $project->manager->name }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-project-diagram text-indigo-600 text-lg"></i>
                        </div>
                    </div>

                    <!-- Project Description -->
                    @if($project->description)
                    <p class="text-gray-600 text-sm leading-relaxed line-clamp-2">
                        {{ $project->description }}
                    </p>
                    @endif
                </div>

                <!-- Progress Section -->
                <div class="p-6">
                    <!-- Task Progress -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between text-sm mb-3">
                            <span class="font-medium text-gray-700 flex items-center">
                                <i class="fas fa-tasks text-gray-400 mr-2 text-xs"></i>
                                My Tasks Progress
                            </span>
                            <span class="font-semibold text-gray-900">
                                {{ $completedTasks }}/{{ $totalTasks }} completed
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                            <div class="h-3 rounded-full bg-gradient-to-r from-green-500 to-green-600 transition-all duration-500"
                                 style="width: {{ $progress }}%">
                            </div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span>Your progress</span>
                            <span class="font-semibold">{{ $progress }}% complete</span>
                        </div>
                    </div>

                    <!-- Project Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-3 bg-blue-50/50 rounded-xl border border-blue-200/60">
                            <div class="text-lg font-bold text-blue-700">{{ $totalTasks }}</div>
                            <div class="text-xs text-blue-600 font-medium">My Tasks</div>
                        </div>
                        <div class="text-center p-3 bg-green-50/50 rounded-xl border border-green-200/60">
                            <div class="text-lg font-bold text-green-700">{{ $completedTasks }}</div>
                            <div class="text-xs text-green-600 font-medium">Completed</div>
                        </div>
                    </div>

                    <!-- Timeline & Actions -->
                    <div class="space-y-3">
                        @if($dueDate)
                        <div class="flex items-center justify-between p-3 rounded-lg border
                            @if($isOverdue) bg-red-50/50 border-red-200/60
                            @elseif($isDueSoon) bg-orange-50/50 border-orange-200/60
                            @else bg-gray-50/50 border-gray-200/60 @endif">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-day
                                    @if($isOverdue) text-red-500
                                    @elseif($isDueSoon) text-orange-500
                                    @else text-gray-400 @endif mr-2 text-sm">
                                </i>
                                <span class="text-sm font-medium
                                    @if($isOverdue) text-red-700
                                    @elseif($isDueSoon) text-orange-700
                                    @else text-gray-700 @endif">
                                    Due Date
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-semibold
                                    @if($isOverdue) text-red-700
                                    @elseif($isDueSoon) text-orange-700
                                    @else text-gray-900 @endif">
                                    {{ $dueDate->format('M d, Y') }}
                                </span>
                                @if($isOverdue)
                                <div class="text-xs text-red-600 flex items-center mt-1">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Overdue
                                </div>
                                @elseif($isDueSoon)
                                <div class="text-xs text-orange-600 mt-1">
                                    Due {{ $dueDate->diffForHumans() }}
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 pt-2">
                            <a href="{{ route('team.tasks.index') }}?project={{ $project->id }}"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-lg group/btn">
                                <i class="fas fa-tasks mr-1.5 text-xs group-hover/btn:scale-110 transition-transform"></i>
                                View Tasks
                            </a>
                            <a href="{{ route('team.chat.project', $project) }}"
                               class="inline-flex items-center justify-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl transition-all duration-200 hover:shadow-md group/btn">
                                <i class="fas fa-comments mr-1.5 text-xs group-hover/btn:scale-110 transition-transform"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Project Footer -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-200/60">
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span class="flex items-center">
                            <i class="fas fa-clock mr-1.5 text-xs"></i>
                            Updated {{ $project->updated_at->diffForHumans() }}
                        </span>
                        @if($project->teamMembers->count() > 0)
                        <span class="flex items-center">
                            <i class="fas fa-users mr-1.5 text-xs"></i>
                            {{ $project->teamMembers->count() }} members
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl border border-gray-200/60 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-briefcase text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No Projects Assigned</h3>
            <p class="text-gray-500 max-w-md mx-auto mb-8">
                You haven't been assigned to any projects yet. Projects you're added to will appear here automatically.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('team.tasks.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors duration-200">
                    <i class="fas fa-tasks mr-2"></i>
                    View Available Tasks
                </a>
                <button class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors duration-200">
                    <i class="fas fa-envelope mr-2"></i>
                    Contact Manager
                </button>
            </div>
        </div>
        @endif

        <!-- Quick Stats Footer -->
    
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
