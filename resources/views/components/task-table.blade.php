{{-- Unified Task Table Component --}}
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 20%;">Title</th>
                <th style="width: 25%;">Description</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 15%;">Approval Status</th>
                <th style="width: 15%;">Deadline</th>
                <th style="width: 13%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td>
                    <strong class="text-dark">{{ $task->title }}</strong>
                </td>
                <td>
                    <small class="text-muted">{{ Str::limit($task->description, 50) }}</small>
                </td>
                <td>
                    <span class="badge 
                        @if($task->status === 'Completed') badge-success
                        @elseif($task->status === 'Active') badge-primary
                        @elseif($task->status === 'Pending') badge-warning
                        @elseif($task->status === 'Rejected') badge-danger
                        @else badge-secondary
                        @endif">
                        {{ $task->status ?? 'N/A' }}
                    </span>
                </td>
                <td>
                    <span class="badge 
                        @if($task->approval_status === 'Approved') badge-success
                        @elseif($task->approval_status === 'Pending for Approval') badge-warning
                        @elseif($task->approval_status === 'Rejected') badge-danger
                        @else badge-secondary
                        @endif">
                        {{ $task->approval_status ?? 'N/A' }}
                    </span>
                </td>
                <td>
                    <small>{{ $task->deadline ? $task->deadline->format('M d, Y') : 'N/A' }}</small>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-info" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        @if($task->status !== 'Completed' && Auth::check() && Auth::id() === $task->user_id)
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endif
                        @if(Auth::check() && (Auth::id() === $task->user_id || Auth::user()->role === 'admin'))
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No tasks found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
