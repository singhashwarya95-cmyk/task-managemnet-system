@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Edit Task: {{ $task->title }}</h1>
        <p class="text-muted">Changes will be sent to administrators for approval</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Update Task Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.update', $task) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title', $task->title) }}" 
                               placeholder="Enter task title" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="5" 
                                  placeholder="Describe your task" required>{{ old('description', $task->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                               id="deadline" name="deadline" value="{{ old('deadline', $task->deadline->format('Y-m-d')) }}" required>
                        @error('deadline')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Submit for Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Current Details</h6>
            </div>
            <div class="card-body text-sm">
                <p class="mb-2"><strong>Status:</strong></p>
                <p class="badge bg-primary mb-3">{{ $task->status }}</p>

                <p class="mb-2"><strong>Created:</strong></p>
                <p class="text-muted mb-3 small">{{ $task->created_at->format('M d, Y \a\t g:i A') }}</p>

                <p class="mb-2"><strong>Last Updated:</strong></p>
                <p class="text-muted small">{{ $task->updated_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
