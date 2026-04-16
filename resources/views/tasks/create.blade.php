@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Create New Task</h1>
        <p class="text-muted">Task creation requests must be approved by an administrator</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Task Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="title" class="form-label">Task Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                               id="title" name="title" value="{{ old('title') }}" 
                               placeholder="Enter task title" required>
                        @error('title')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="5" 
                                  placeholder="Describe your task" required>{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                               id="deadline" name="deadline" value="{{ old('deadline') }}" required>
                        @error('deadline')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
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
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Information</h6>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>What happens next?</strong></p>
                <p class="text-muted small">Your task creation request will be sent to the administrators for review and approval. You'll be notified once it's approved or rejected.</p>
                
                <hr>

                <p class="mb-2"><strong>Requirements:</strong></p>
                <ul class="text-muted small mb-0">
                    <li>Title must be descriptive</li>
                    <li>Description should explain the task clearly</li>
                    <li>Deadline must be in the future</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
