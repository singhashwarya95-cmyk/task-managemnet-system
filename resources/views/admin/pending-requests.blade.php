@extends('layouts.app')

@section('title', 'Pending Task Requests')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>Pending Task Requests</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.task-requests.index') }}" class="btn btn-secondary">
            <i class="bi bi-list"></i> All Requests
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($requests->isEmpty())
            <p class="text-muted text-center py-5">No pending task requests</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Action</th>
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
                            <td>{{ $request->task?->title ?? 'New Task' }}</td>
                            <td>{{ $request->created_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $request->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <form action="{{ route('admin.task-requests.approve', $request) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve this request?')">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                    <i class="bi bi-x"></i> Reject
                                </button>
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
                                            <h6>Changes:</h6>
                                            @php 
                                                $oldData = json_decode($request->old_data, true);
                                                $newData = json_decode($request->new_data, true);
                                            @endphp
                                            <table class="table table-sm">
                                                <tr>
                                                    <th>Field</th>
                                                    <th>Old Value</th>
                                                    <th>New Value</th>
                                                </tr>
                                                @foreach($newData as $key => $value)
                                                <tr>
                                                    <td><strong>{{ ucfirst($key) }}</strong></td>
                                                    <td>{{ $oldData[$key] ?? 'N/A' }}</td>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                                @endforeach
                                            </table>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
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
