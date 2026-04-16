<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use App\Models\TaskRequest;
use App\Models\TaskCompletion;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function getAllTasks()
    {
        $tasks = Task::with('user', 'completions')->get();
        return response()->json($tasks);
    }

    public function getPendingRequests()
    {
        $requests = TaskRequest::with('user', 'task')
            ->where('status', 'Pending')
            ->get();

        $completions = TaskCompletion::with('user', 'task')
            ->where('verification_status', 'Pending')
            ->get();

        return response()->json([
            'action_requests' => $requests,
            'completion_requests' => $completions,
        ]);
    }

    public function approveRequest(Request $request, TaskRequest $taskRequest)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $taskRequest->update(['status' => 'Approved']);

        if ($taskRequest->action_type === 'Create') {
            $task = Task::create(array_merge(
                $taskRequest->new_data,
                ['user_id' => $taskRequest->user_id, 'approval_status' => 'Approved']
            ));

            ApprovalLog::create([
                'task_id' => $task->id,
                'admin_id' => $request->user()->id,
                'action' => 'Approved',
                'new_data' => $task->toArray(),
            ]);

            return response()->json([
                'message' => 'Task created and approved',
                'task' => $task,
            ]);
        } elseif ($taskRequest->action_type === 'Update') {
            $task = Task::find($taskRequest->task_id);
            $oldData = $task->toArray();
            $task->update(array_merge($taskRequest->new_data, ['approval_status' => 'Approved']));

            ApprovalLog::create([
                'task_id' => $task->id,
                'admin_id' => $request->user()->id,
                'action' => 'Approved',
                'old_data' => $oldData,
                'new_data' => $task->toArray(),
            ]);

            return response()->json([
                'message' => 'Task updated and approved',
                'task' => $task,
            ]);
        } elseif ($taskRequest->action_type === 'Delete') {
            $task = Task::find($taskRequest->task_id);

            ApprovalLog::create([
                'task_id' => $task->id,
                'admin_id' => $request->user()->id,
                'action' => 'Approved',
                'old_data' => $task->toArray(),
            ]);

            $task->delete();
            return response()->json(['message' => 'Task deleted and approved']);
        }

        return response()->json(['message' => 'Action approved']);
    }

    public function rejectRequest(Request $request, TaskRequest $taskRequest)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'remarks' => 'required|string',
        ]);

        $taskRequest->update(['status' => 'Rejected']);

        if ($taskRequest->task_id) {
            $task = Task::find($taskRequest->task_id);
            $task->update([
                'approval_status' => 'Rejected',
                'admin_remarks' => $data['remarks'],
            ]);

            ApprovalLog::create([
                'task_id' => $task->id,
                'admin_id' => $request->user()->id,
                'action' => 'Rejected',
                'remarks' => $data['remarks'],
            ]);
        }

        return response()->json([
            'message' => 'Request rejected',
            'remarks' => $data['remarks'],
        ]);
    }

    public function verifyCompletion(Request $request, TaskCompletion $completion)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task = $completion->task;
        $task->update([
            'status' => 'Completed',
            'approval_status' => 'Verified',
        ]);

        $completion->update(['verification_status' => 'Verified']);

        ApprovalLog::create([
            'task_id' => $task->id,
            'admin_id' => $request->user()->id,
            'action' => 'Verified',
        ]);

        return response()->json([
            'message' => 'Task completion verified',
            'task' => $task,
        ]);
    }

    public function rejectCompletion(Request $request, TaskCompletion $completion)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = $request->validate([
            'remarks' => 'required|string',
        ]);

        $task = $completion->task;
        $task->update([
            'status' => 'Pending',
            'admin_remarks' => $data['remarks'],
        ]);

        $completion->update([
            'verification_status' => 'Rejected',
            'admin_remarks' => $data['remarks'],
        ]);

        ApprovalLog::create([
            'task_id' => $task->id,
            'admin_id' => $request->user()->id,
            'action' => 'Rejected Completion',
            'remarks' => $data['remarks'],
        ]);

        return response()->json([
            'message' => 'Task completion rejected',
            'remarks' => $data['remarks'],
        ]);
    }

    public function filterTasks(Request $request)
    {
        $query = Task::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $tasks = $query->with('user', 'completions')->get();
        return response()->json($tasks);
    }
}
