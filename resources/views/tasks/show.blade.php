@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1>{{ $task->title }}</h1>
    </div>
    <div class="col-md-4 text-end">
        @if($task->status !== 'Completed')
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
        @endif
        @if($task->status === 'Active')
            <a href="{{ route('tasks.completion-form', $task) }}" class="btn btn-success">
                <i class="bi bi-check-circle"></i> Submit Completion
            </a>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Task Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Status</label>
                    <p>
                        <span class="badge 
                            @if($task->status === 'Completed') badge-success
                            @elseif($task->status === 'Active') badge-primary
                            @elseif($task->status === 'Pending') badge-warning
                            @else badge-secondary
                            @endif px-3 py-2" style="font-size: 0.9rem;">
                            {{ $task->status }}
                        </span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Description</label>
                    <p>{{ $task->description }}</p>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Created On</label>
                        <p>{{ $task->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">Deadline</label>
                        <p>{{ $task->deadline->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label text-muted">Days Remaining</label>
                    <p>
                        @php
                            $daysRemaining = $task->deadline->diffInDays(now(), false);
                        @endphp
                        @if($daysRemaining > 0)
                            <span class="badge bg-success">{{ $daysRemaining }} days</span>
                        @elseif($daysRemaining == 0)
                            <span class="badge bg-warning">Due today</span>
                        @else
                            <span class="badge bg-danger">{{ abs($daysRemaining) }} days overdue</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if($task->completions->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Completion Submissions</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    @foreach($task->completions as $completion)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">Submission</h6>
                                <p class="mb-1 text-muted small">{{ $completion->created_at->format('M d, Y \a\t g:i A') }}</p>
                                <p class="mb-0">{{ $completion->remarks }}</p>
                            </div>
                            <span class="badge 
                                @if($completion->status === 'Verified') badge-success
                                @elseif($completion->status === 'Rejected') badge-danger
                                @else badge-warning
                                @endif">
                                {{ $completion->status }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Quick Info</h6>
            </div>
            <div class="card-body">
                <dl class="row text-sm">
                    <dt class="col-6">Created:</dt>
                    <dd class="col-6">{{ $task->created_at->diffForHumans() }}</dd>

                    <dt class="col-6">Last Updated:</dt>
                    <dd class="col-6">{{ $task->updated_at->diffForHumans() }}</dd>
                </dl>
            </div>
        </div>

        @if($task->status !== 'Completed')
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Actions</h6>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('tasks.edit', $task) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-pencil"></i> Edit Task
                </a>
                @if($task->status === 'Active')
                <a href="{{ route('tasks.completion-form', $task) }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-check-circle"></i> Submit Completion
                </a>
                @endif
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="list-group-item list-group-item-action text-danger w-100 text-start"
                            onclick="return confirm('Are you sure?')">
                        <i class="bi bi-trash"></i> Delete Task
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
