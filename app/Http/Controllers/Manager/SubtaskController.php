<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\task_subtasks as TaskSubtasks;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubtaskController extends Controller
{


    public function index($taskId)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($taskId);

        $subtasks = TaskSubtasks::where('task_id', $taskId)->latest()->get();

        return view('manager.subtasks.index', compact('task', 'subtasks'));
    }

    public function create($taskId)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($taskId);

        return view('manager.subtasks.create', compact('task'));
    }

    public function store(Request $request, $taskId)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($taskId);

        $request->validate([
            'title' => 'required|string|max:255',
            'status' => ['nullable', Rule::in(['todo', 'in_progress', 'done'])],
        ]);

        TaskSubtasks::create([
            'task_id' => $taskId,
            'title' => $request->title,
            'status' => $request->status ?? 'todo',
        ]);

        return redirect()->route('manager.tasks.show', $taskId)
                        ->with('success', 'Subtask created successfully!');
    }

    public function edit($taskId, $subtaskId)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($taskId);

        $subtask = TaskSubtasks::where('task_id', $taskId)->findOrFail($subtaskId);

        return view('manager.subtasks.edit', compact('task', 'subtask'));
    }

    public function update(Request $request, $taskId, $subtaskId)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($taskId);

        $subtask = TaskSubtasks::where('task_id', $taskId)->findOrFail($subtaskId);

        $request->validate([
            'title' => 'required|string|max:255',
            'status' => ['required', Rule::in(['todo', 'in_progress', 'done'])],
        ]);

        $subtask->update($request->all());

        return redirect()->route('manager.tasks.show', $taskId)
                        ->with('success', 'Subtask updated successfully!');
    }

    public function destroy($taskId, $subtaskId)
    {
        $task = Tasks::whereHas('project', function($query) {
            $query->where('manager_id', auth()->id());
        })->findOrFail($taskId);

        $subtask = TaskSubtasks::where('task_id', $taskId)->findOrFail($subtaskId);
        $subtask->delete();

        return redirect()->route('manager.tasks.show', $taskId)
                        ->with('success', 'Subtask deleted successfully!');
    }
}
