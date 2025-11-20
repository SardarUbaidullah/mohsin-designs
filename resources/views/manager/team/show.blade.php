@extends("manager.layouts.app")

@section("content")
<div class="max-w-7xl mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <a href="{{ route('manager.team.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900">{{ $teamMember->name }}</h1>
            </div>
            <p class="text-gray-600">Team member details and assigned tasks</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Member Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Assigned Tasks -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Assigned Tasks</h2>

                @if($teamMember->assignedTasks->count() > 0)
                    <div class="space-y-4">
                        @foreach($teamMember->assignedTasks as $task)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $task->title }}</h4>
                                        <p class="text-xs text-gray-500">
                                            Project: {{ $task->project->name }}
                                            @if($task->due_date)
                                                • Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
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
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-gray-500">No tasks assigned to this team member</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Member Information -->
        <div class="space-y-6">
            <!-- Member Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Member Information</h3>
                <div class="space-y-4">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-2xl">
                            {{ strtoupper(substr($teamMember->name, 0, 1)) }}
                        </div>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-600">Full Name</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $teamMember->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-600">Email</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $teamMember->email }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-600">Role</dt>
                        <dd class="text-sm text-gray-900 mt-1 capitalize">{{ $teamMember->role }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-600">Member Since</dt>
                        <dd class="text-sm text-gray-900 mt-1">{{ $teamMember->created_at->format('M d, Y') }}</dd>
                    </div>
                </div>
            </div>

            <!-- Task Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Task Statistics</h3>
                <div class="space-y-3">
                    @php
                        $totalTasks = $teamMember->assignedTasks->count();
                        $completedTasks = $teamMember->assignedTasks->where('status', 'done')->count();
                        $pendingTasks = $teamMember->assignedTasks->where('status', '!=', 'done')->count();
                        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                    @endphp

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Tasks</span>
                        <span class="text-lg font-semibold text-gray-900">{{ $totalTasks }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-lg font-semibold text-green-600">{{ $completedTasks }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Pending</span>
                        <span class="text-lg font-semibold text-orange-600">{{ $pendingTasks }}</span>
                    </div>

                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Completion Rate</span>
                            <span class="text-sm text-gray-600">{{ $completionRate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full bg-green-500" style="width: {{ $completionRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ route('manager.tasks.create') }}?assigned_to={{ $teamMember->id }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Assign New Task</span>
                    </a>
                    <a href="mailto:{{ $teamMember->email }}"
                       class="w-full flex items-center space-x-3 p-3 text-left text-sm text-gray-700 hover:bg-gray-50 rounded-lg transition duration-150">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Send Email</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
