<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use App\Models\TaskRequest;
use App\Models\TaskCompletion;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Show all tasks (dashboard)
     */
    public function dashboard()
    {
        $tasks = Task::where('user_id', Auth::id())->with(['completions', 'requests'])->get();
        return view('tasks.dashboard', compact('tasks'));
    }

    /**
     * Show task list
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show create task form
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a new task (creates task with Pending status)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:today',
        ]);

        // Create task directly with Pending status
        $task = Task::create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'deadline' => $data['deadline'],
            'status' => 'Pending',
            'approval_status' => 'Pending for Approval',
        ]);

        // Log the creation in task requests for audit trail
        TaskRequest::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'action_type' => 'Create',
            'new_data' => json_encode($data),
            'status' => 'Pending',
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully and is pending for approval');
    }

    /**
     * Show single task
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show edit form
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update task (creates approval request)
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $data = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'deadline' => 'date|after:today',
        ]);

        // Create a task request for admin approval
        TaskRequest::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'action_type' => 'Update',
            'old_data' => json_encode($task->only(['title', 'description', 'deadline'])),
            'new_data' => json_encode($data),
            'status' => 'Pending',
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Update request submitted for approval');
    }

    /**
     * Delete task (creates approval request)
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('delete', $task);

        TaskRequest::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'action_type' => 'Delete',
            'old_data' => json_encode($task->toArray()),
            'status' => 'Pending',
        ]);

        return redirect()->route('tasks.index')->with('success', 'Delete request submitted for approval');
    }

    /**
     * Show task completion form
     */
    public function showCompletionForm(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.submit-completion', compact('task'));
    }

    /**
     * Submit task completion (creates approval request)
     */
    public function submitCompletion(Request $request, Task $task)
    {
        $this->authorize('view', $task);

        $request->validate([
            'screenshots' => 'required|array|min:3',
            'screenshots.*' => 'required|image|max:5120',
            'remarks' => 'required|string|max:1000',
        ]);

        $screenshots = [];
        foreach ($request->file('screenshots') as $screenshot) {
            $path = $screenshot->store('completions', 'public');
            $screenshots[] = $path;
        }

        // Create a task completion request for admin approval
        $completion = TaskCompletion::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'screenshots' => json_encode($screenshots),
            'remarks' => $request->remarks,
            'status' => 'Pending',
        ]);

        return redirect()->route('tasks.show', $task)->with('success', 'Task completion submitted for admin approval');
    }
}
