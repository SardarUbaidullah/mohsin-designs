<div class="max-w-7xl mx-auto p-6 bg-white rounded-xl shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Kanban Board - {{ $project->name }}</h2>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        @foreach(['To Do', 'In Progress', 'Done'] as $status)
            <div class="bg-gray-50 rounded-lg p-4 border">
                <h3 class="font-semibold text-gray-800 mb-3">{{ $status }}</h3>
                <div class="space-y-3">
                    @foreach($project->tasks->where('status', strtolower(str_replace(' ', '_', $status))) as $task)
                        <div class="p-3 bg-white shadow rounded-md border-l-4 border-blue-500">
                            <p class="font-medium text-gray-700">{{ $task->title }}</p>
                            <p class="text-sm text-gray-500">{{ Str::limit($task->description, 60) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
