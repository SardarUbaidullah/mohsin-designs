<!-- resources/views/client/calendar/index.blade.php -->
@extends('Client.app')

@section('title', 'Calendar')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Project Calendar</h1>
        <p class="text-gray-600 mt-2">Milestones and deadlines from your projects</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Milestones -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Upcoming Milestones</h3>
            </div>
            <div class="p-6">
                @if($milestones->count() > 0)
                <div class="space-y-4">
                    @foreach($milestones as $milestone)
                    <div class="border-l-4 border-purple-500 pl-4 py-2">
                        <h4 class="font-semibold text-gray-900">{{ $milestone->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $milestone->description }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Project: {{ $milestone->project->name }} •
                            Due: {{ $milestone->due_date->format('M d, Y') }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No milestones scheduled.</p>
                @endif
            </div>
        </div>

        <!-- Tasks -->
        <div class="bg-white rounded-lg shadow border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Task Deadlines</h3>
            </div>
            <div class="p-6">
                @if($tasks->count() > 0)
                <div class="space-y-3">
                    @foreach($tasks as $task)
                    <div class="border-l-4 border-blue-500 pl-3 py-2">
                        <h4 class="font-medium text-gray-900 text-sm">{{ $task->title }}</h4>
                        <p class="text-xs text-gray-500">
                            Due: {{ $task->due_date->format('M d, Y') }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-8">No upcoming task deadlines.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
