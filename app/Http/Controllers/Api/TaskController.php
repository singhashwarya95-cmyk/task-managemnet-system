<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\TaskRequest;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)->get();
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'deadline' => 'required|date',
        ]);

        // Create a task request for admin approval
        $taskRequest = TaskRequest::create([
            'user_id' => $request->user()->id,
            'action_type' => 'Create',
            'new_data' => $data,
        ]);

        return response()->json([
            'message' => 'Task request created and awaiting admin approval',
            'request' => $taskRequest,
        ], 201);
    }

    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'title' => 'string',
            'description' => 'string',
            'deadline' => 'date',
        ]);

        // Create a task request for admin approval
        $taskRequest = TaskRequest::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'action_type' => 'Update',
            'old_data' => $task->only(['title', 'description', 'deadline']),
            'new_data' => $data,
        ]);

        return response()->json([
            'message' => 'Update request created and awaiting admin approval',
            'request' => $taskRequest,
        ]);
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Create a task request for admin approval
        TaskRequest::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'action_type' => 'Delete',
            'old_data' => $task->toArray(),
        ]);

        return response()->json(['message' => 'Delete request created and awaiting admin approval']);
    }

    public function submitCompletion(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'screenshots' => 'required|array|min:3',
            'screenshots.*' => 'required|image|max:5120',
            'remarks' => 'required|string',
        ]);

        $screenshots = [];
        foreach ($request->file('screenshots') as $screenshot) {
            $path = $screenshot->store('completions', 'public');
            $screenshots[] = $path;
        }

        // Create a task completion request for admin approval
        $completion = TaskCompletion::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'screenshots' => $screenshots,
            'remarks' => $data['remarks'],
        ]);

        return response()->json([
            'message' => 'Task completion submitted for admin approval',
            'completion' => $completion,
        ], 201);
    }
}
