@extends('layouts.app')

@section('title', 'Submit Task Completion')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Submit Task Completion</h1>
        <p class="text-muted">Task: <strong>{{ $task->title }}</strong></p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Completion Details</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('tasks.submit-completion', $task) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="screenshots" class="form-label">Screenshots <span class="text-danger">*</span></label>
                        <p class="text-muted small mb-2">Upload at least 3 screenshots as proof of completion</p>
                        <input type="file" class="form-control @error('screenshots') is-invalid @enderror" 
                               id="screenshots" name="screenshots[]" multiple accept="image/*" required>
                        <small class="form-text text-muted">Accepted formats: JPG, PNG, GIF, WebP (Max 5MB each)</small>
                        @error('screenshots')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                        <div id="preview" class="mt-3"></div>
                    </div>

                    <div class="mb-3">
                        <label for="remarks" class="form-label">Remarks <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('remarks') is-invalid @enderror" 
                                  id="remarks" name="remarks" rows="5" 
                                  placeholder="Describe what you've completed and any relevant details" 
                                  required>{{ old('remarks') }}</textarea>
                        @error('remarks')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Submit Completion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Requirements</h6>
            </div>
            <div class="card-body">
                <ul class="text-muted small mb-0">
                    <li>Minimum 3 screenshots required</li>
                    <li>Screenshots should clearly show the completed work</li>
                    <li>Provide clear remarks explaining what was done</li>
                    <li>Screenshots will be reviewed by administrators</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">Task Info</h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-2"><strong>Created:</strong> {{ $task->created_at->format('M d, Y') }}</p>
                <p class="text-muted small mb-0"><strong>Deadline:</strong> {{ $task->deadline->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>

@section('extra-scripts')
<script>
document.getElementById('screenshots').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    
    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const div = document.createElement('div');
            div.className = 'mb-2';
            div.innerHTML = `
                <img src="${event.target.result}" alt="Preview ${index + 1}" style="max-width: 100px; max-height: 100px; margin-right: 10px; border-radius: 4px;">
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
@endsection
