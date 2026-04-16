<?php

namespace App\Http\Controllers\Web;

use App\Models\Task;
use App\Models\TaskRequest;
use App\Models\TaskCompletion;
use App\Models\ApprovalLog;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $pendingTaskRequests = TaskRequest::where('status', 'Pending')->count();
        $pendingCompletions = TaskCompletion::where('status', 'Pending')->count();
        $totalTasks = Task::count();
        $totalUsers = \App\Models\User::count();

        return view('admin.dashboard', compact(
            'pendingTaskRequests',
            'pendingCompletions',
            'totalTasks',
            'totalUsers'
        ));
    }

    /**
     * Show all task requests
     */
    public function taskRequests()
    {
        $requests = TaskRequest::with(['user', 'task'])
            ->paginate(15);

        return view('admin.task-requests', compact('requests'));
    }

    /**
     * Show pending requests
     */
    public function pendingRequests()
    {
        $requests = TaskRequest::where('status', 'Pending')
            ->with(['user', 'task'])
            ->paginate(15);

        return view('admin.pending-requests', compact('requests'));
    }

    /**
     * Approve task request
     */
    public function approveRequest(Request $request, TaskRequest $taskRequest)
    {
        try {
            DB::beginTransaction();

            if ($taskRequest->action_type === 'Create') {
                // Create the task
                $data = json_decode($taskRequest->new_data, true);
                $task = Task::create([
                    'user_id' => $taskRequest->user_id,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'deadline' => $data['deadline'],
                    'status' => 'Active',
                ]);

                $taskRequest->update(['status' => 'Approved', 'task_id' => $task->id]);
            } elseif ($taskRequest->action_type === 'Update') {
                // Update the task
                $data = json_decode($taskRequest->new_data, true);
                $taskRequest->task->update($data);
                $taskRequest->update(['status' => 'Approved']);
            } elseif ($taskRequest->action_type === 'Delete') {
                // Delete the task
                $taskRequest->task->delete();
                $taskRequest->update(['status' => 'Approved']);
            }

            // Log the approval
            ApprovalLog::create([
                'admin_id' => Auth::id(),
                'task_request_id' => $taskRequest->id,
                'action' => 'Approved',
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return back()->with('success', 'Task request approved successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error approving request: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject task request
     */
    public function rejectRequest(Request $request, TaskRequest $taskRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $taskRequest->update(['status' => 'Rejected']);

        // Log the rejection
        ApprovalLog::create([
            'admin_id' => Auth::id(),
            'task_request_id' => $taskRequest->id,
            'action' => 'Rejected',
            'remarks' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Task request rejected');
    }

    /**
     * Show completions for verification
     */
    public function completions()
    {
        $completions = TaskCompletion::with(['task', 'user'])
            ->paginate(15);

        return view('admin.completions', compact('completions'));
    }

    /**
     * Show pending completions
     */
    public function pendingCompletions()
    {
        $completions = TaskCompletion::where('status', 'Pending')
            ->with(['task', 'user'])
            ->paginate(15);

        return view('admin.pending-completions', compact('completions'));
    }

    /**
     * Verify task completion
     */
    public function verifyCompletion(Request $request, TaskCompletion $completion)
    {
        $request->validate([
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $completion->update([
                'status' => 'Verified',
                'verified_at' => now(),
            ]);

            // Update task status
            $completion->task->update(['status' => 'Completed']);

            // Log the verification
            ApprovalLog::create([
                'admin_id' => Auth::id(),
                'completion_id' => $completion->id,
                'action' => 'Verified',
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return back()->with('success', 'Task completion verified successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error verifying completion: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject task completion
     */
    public function rejectCompletion(Request $request, TaskCompletion $completion)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $completion->update([
            'status' => 'Rejected',
            'rejected_at' => now(),
        ]);

        // Log the rejection
        ApprovalLog::create([
            'admin_id' => Auth::id(),
            'completion_id' => $completion->id,
            'action' => 'Rejected',
            'remarks' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Task completion rejected');
    }

    /**
     * Filter tasks
     */
    public function filterTasks(Request $request)
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('deadline_from')) {
            $query->where('deadline', '>=', $request->deadline_from);
        }

        if ($request->filled('deadline_to')) {
            $query->where('deadline', '<=', $request->deadline_to);
        }

        $tasks = $query->with('user')->paginate(15);

        return view('admin.filtered-tasks', compact('tasks'));
    }
}
