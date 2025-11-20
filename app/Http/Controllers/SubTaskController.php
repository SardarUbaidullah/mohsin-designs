<?php

namespace App\Http\Controllers;

use App\Models\task_subtasks;
use App\Models\Tasks;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubTaskController extends Controller
{
    public function index()
    {
        $subtasks = task_subtasks::with('task')->latest()->get();
        $tasks = Tasks::all();
        return view('admin.subtasks.index', compact('subtasks', 'tasks'));
    }

    public function create()
    {
        $tasks = Tasks::all();
        return view('admin.subtasks.create', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'title'   => 'required|string|max:255',
            'status'  => ['nullable', Rule::in(['todo','in_progress','done'])],
        ]);

        task_subtasks::create($request->all());

        return redirect()->route('subtasks.index')->with('success', 'Subtask created successfully!');
    }

    public function show($id)
    {
        $subtask = task_subtasks::with('task')->find($id);

        if (!$subtask) {
            return redirect()->route('subtasks.index')->with('error', 'Subtask not found');
        }

        return view('admin.subtasks.show', compact('subtask'));
    }

    public function edit($id)
    {
        $subtask = task_subtasks::find($id);
        $tasks = Tasks::all();

        if (!$subtask) {
            return redirect()->route('subtasks.index')->with('error', 'Subtask not found');
        }

        return view('admin.subtasks.edit', compact('subtask', 'tasks'));
    }

    public function update(Request $request, $id)
    {
        $subtask = task_subtasks::find($id);

        if (!$subtask) {
            return redirect()->route('subtasks.index')->with('error', 'Subtask not found');
        }

        $request->validate([
            'task_id' => 'sometimes|exists:tasks,id',
            'title'   => 'sometimes|string|max:255',
            'status'  => ['sometimes', Rule::in(['todo','in_progress','done'])],
        ]);

        $subtask->update($request->all());

        return redirect()->route('subtasks.index')->with('success', 'Subtask updated successfully!');
    }

    public function destroy($id)
    {
        $subtask = task_subtasks::find($id);

        if (!$subtask) {
            return redirect()->route('subtasks.index')->with('error', 'Subtask not found');
        }

        $subtask->delete();

        return redirect()->route('subtasks.index')->with('success', 'Subtask deleted successfully!');
    }
}
