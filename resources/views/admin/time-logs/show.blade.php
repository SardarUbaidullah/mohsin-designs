@extends("admin.layouts.app")

@section("content")
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Time Log Details</h1>
            <p class="text-gray-600 mt-2">View complete time tracking information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('time-logs.edit', $timeLog->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('time-logs.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition duration-200 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Time Logs
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
            <!-- Time Information -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="w-1.5 h-6 bg-blue-600 rounded-full mr-3"></div>
                    <h2 class="text-xl font-semibold text-gray-900">Time Information</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Time Log ID</span>
                        <span class="text-lg font-semibold text-gray-900">#{{ $timeLog->id }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Hours Logged</span>
                        @if($timeLog->hours >= 8)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $timeLog->hours }} hours
                            </span>
                        @elseif($timeLog->hours >= 4)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $timeLog->hours }} hours
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $timeLog->hours }} hours
                            </span>
                        @endif
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Log Date</span>
                        <span class="text-lg font-medium text-gray-900">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($timeLog->log_date)->format('F d, Y') }}
                            </span>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Created At</span>
                        <span class="text-lg font-medium text-gray-900">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $timeLog->created_at->format('F d, Y H:i') }}
                            </span>
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500">Updated At</span>
                        <span class="text-lg font-medium text-gray-900">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                {{ $timeLog->updated_at->format('F d, Y H:i') }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Assignment Details -->
            <div>
                <div class="flex items-center mb-4">
                    <div class="w-1.5 h-6 bg-green-600 rounded-full mr-3"></div>
                    <h2 class="text-xl font-semibold text-gray-900">Assignment Details</h2>
                </div>
                <div class="space-y-4">
                    <div class="py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500 block mb-2">Task</span>
                        @if($timeLog->task)
                            <div class="flex items-start space-x-3">
                                <div class="bg-blue-100 p-2 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-lg font-medium text-gray-900">{{ $timeLog->task->title }}</p>
                                    @if($timeLog->task->project)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                                </svg>
                                                {{ $timeLog->task->project->name }}
                                            </span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </div>
                    <div class="py-3 border-b border-gray-100">
                        <span class="text-sm font-medium text-gray-500 block mb-2">User</span>
                        @if($timeLog->user)
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white text-lg font-bold">
                                    {{ substr($timeLog->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-lg font-medium text-gray-900">{{ $timeLog->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $timeLog->user->email }}</p>
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Section -->
        <div class="border-t border-gray-200 bg-red-50 px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-red-800">Danger Zone</h3>
                    <p class="text-red-600 text-sm">Once deleted, this time log cannot be recovered</p>
                </div>
                <form action="{{ route('time-logs.destroy', $timeLog->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this time log? This action cannot be undone.')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Time Log
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
