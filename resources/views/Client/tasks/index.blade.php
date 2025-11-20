<!-- resources/views/client/tasks/index.blade.php -->
@extends('Client.app')

@section('title', 'Project Tasks')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Project Tasks</h1>
        <p class="text-gray-600 mt-2">All tasks from your projects</p>
    </div>

    <div class="bg-white rounded-lg shadow border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Tasks ({{ $tasks->count() }})</h3>
        </div>
        <div class="p-6">
            @if($tasks->count() > 0)
            <div class="space-y-4">
                @foreach($tasks as $task)
                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition duration-200">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold text-gray-900 text-lg">{{ $task->title }}</h4>
                        <span class="px-3 py-1 text-sm rounded-full
                            @if($task->status == 'completed') bg-green-100 text-green-800
                            @elseif($task->status == 'in_progress') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                    <p class="text-gray-600 mb-3">{{ $task->description }}</p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <div class="flex items-center space-x-4">
                            <span>Project: {{ $task->project->name }}</span>
                            <span>Assigned to: {{ $task->assignedTo->name ?? 'Unassigned' }}</span>
                            @if($task->due_date)
                            <span>Due: {{ $task->due_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-12">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tasks text-gray-400 text-3xl"></i>
                </div>
                <p class="text-gray-500">No tasks found in your projects.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
