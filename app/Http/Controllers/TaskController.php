<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks|max:255',
        ]);

        $task = new Task;
        $task->title = $request->title;
        $task->save();

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->completed = $request->completed;
        $task->save();

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => 'Task deleted']);
    }

    public function showAll()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
