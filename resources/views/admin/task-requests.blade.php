@extends('layouts.app')

@section('title', 'Task Requests')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>Task Requests</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.task-requests.pending') }}" class="btn btn-warning">
            <i class="bi bi-exclamation-circle"></i> Pending Only
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($requests->isEmpty())
            <p class="text-muted text-center py-5">No task requests found</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>Task</th>
                            <th>Requested</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                        <tr>
                            <td><strong>{{ $request->user->name }}</strong></td>
                            <td>
                                <span class="badge 
                                    @if($request->action_type === 'Create') bg-success
                                    @elseif($request->action_type === 'Update') bg-warning text-dark
                                    @else bg-danger
                                    @endif">
                                    {{ $request->action_type }}
                                </span>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($request->status === 'Approved') badge-success
                                    @elseif($request->status === 'Rejected') badge-danger
                                    @else badge-warning
                                    @endif">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td>{{ $request->task?->title ?? 'N/A' }}</td>
                            <td>{{ $request->created_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $request->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                @if($request->status === 'Pending')
                                    <form action="{{ route('admin.task-requests.approve', $request) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                            <i class="bi bi-check"></i> Approve
                                        </button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                        <i class="bi bi-x"></i> Reject
                                    </button>
                                @endif
                            </td>
                        </tr>

                        <!-- Details Modal -->
                        <div class="modal fade" id="detailsModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Task Request Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>User:</strong> {{ $request->user->name }} ({{ $request->user->email }})</p>
                                        <p><strong>Action:</strong> {{ $request->action_type }}</p>
                                        <p><strong>Status:</strong> {{ $request->status }}</p>
                                        <p><strong>Requested at:</strong> {{ $request->created_at->format('M d, Y g:i A') }}</p>
                                        
                                        @if($request->action_type === 'Create')
                                            <hr>
                                            <h6>New Task Data:</h6>
                                            @php $data = json_decode($request->new_data, true); @endphp
                                            <p><strong>Title:</strong> {{ $data['title'] ?? 'N/A' }}</p>
                                            <p><strong>Description:</strong> {{ $data['description'] ?? 'N/A' }}</p>
                                            <p><strong>Deadline:</strong> {{ $data['deadline'] ?? 'N/A' }}</p>
                                        @elseif($request->action_type === 'Update')
                                            <hr>
                                            <h6>Old Data:</h6>
                                            @php $oldData = json_decode($request->old_data, true); @endphp
                                            <p><strong>Title:</strong> {{ $oldData['title'] ?? 'N/A' }}</p>
                                            <p><strong>Description:</strong> {{ $oldData['description'] ?? 'N/A' }}</p>
                                            <p><strong>Deadline:</strong> {{ $oldData['deadline'] ?? 'N/A' }}</p>
                                            
                                            <hr>
                                            <h6>New Data:</h6>
                                            @php $newData = json_decode($request->new_data, true); @endphp
                                            <p><strong>Title:</strong> {{ $newData['title'] ?? 'N/A' }}</p>
                                            <p><strong>Description:</strong> {{ $newData['description'] ?? 'N/A' }}</p>
                                            <p><strong>Deadline:</strong> {{ $newData['deadline'] ?? 'N/A' }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        @if($request->status === 'Pending')
                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.task-requests.reject', $request) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Request</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        @endforeach
                    </tbody>
                </table>
            </div>

            <nav>
                {{ $requests->links('pagination::bootstrap-4') }}
            </nav>
        @endif
    </div>
</div>
@endsection
