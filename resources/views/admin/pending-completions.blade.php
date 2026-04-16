@extends('layouts.app')

@section('title', 'Pending Completions')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>Pending Task Completions</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('admin.completions.index') }}" class="btn btn-secondary">
            <i class="bi bi-list"></i> All Completions
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($completions->isEmpty())
            <p class="text-muted text-center py-5">No pending completions</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Task</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completions as $completion)
                        <tr>
                            <td><strong>{{ $completion->user->name }}</strong></td>
                            <td>{{ $completion->task->title }}</td>
                            <td>{{ $completion->created_at->diffForHumans() }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $completion->id }}">
                                    <i class="bi bi-eye"></i> View
                                </button>
                                <form action="{{ route('admin.completions.verify', $completion) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Verify this completion?')">
                                        <i class="bi bi-check"></i> Verify
                                    </button>
                                </form>
                                <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $completion->id }}">
                                    <i class="bi bi-x"></i> Reject
                                </button>
                            </td>
                        </tr>

                        <!-- Details Modal -->
                        <div class="modal fade" id="detailsModal{{ $completion->id }}" tabindex="-1">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Completion Details</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <p><strong>User:</strong> {{ $completion->user->name }} ({{ $completion->user->email }})</p>
                                                <p><strong>Task:</strong> {{ $completion->task->title }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p><strong>Submitted:</strong> {{ $completion->created_at->format('M d, Y g:i A') }}</p>
                                                <p><strong>Days ago:</strong> {{ $completion->created_at->diffInDays(now()) }} days</p>
                                            </div>
                                        </div>

                                        <hr>

                                        <h6>Remarks</h6>
                                        <p>{{ $completion->remarks }}</p>

                                        <hr>

                                        <h6>Screenshots</h6>
                                        <div class="row">
                                            @php $screenshots = json_decode($completion->screenshots, true); @endphp
                                            @if(is_array($screenshots))
                                                @foreach($screenshots as $screenshot)
                                                <div class="col-md-4 mb-3">
                                                    <a href="{{ asset('storage/' . $screenshot) }}" target="_blank">
                                                        <img src="{{ asset('storage/' . $screenshot) }}" alt="Screenshot" class="img-fluid rounded" style="max-height: 200px;">
                                                    </a>
                                                </div>
                                                @endforeach
                                            @else
                                                <p class="text-muted">No screenshots available</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $completion->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.completions.reject', $completion) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Completion</h5>
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
                {{ $completions->links('pagination::bootstrap-4') }}
            </nav>
        @endif
    </div>
</div>
@endsection
