@extends('layouts.app')

@section('title', 'Filter Tasks')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Filter Tasks</h1>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Filter Options</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.tasks.filter') }}">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="Active" @if(request('status') === 'Active') selected @endif>Active</option>
                            <option value="Completed" @if(request('status') === 'Completed') selected @endif>Completed</option>
                            <option value="Pending" @if(request('status') === 'Pending') selected @endif>Pending</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="deadline_from" class="form-label">Deadline From</label>
                        <input type="date" class="form-control" id="deadline_from" name="deadline_from" value="{{ request('deadline_from') }}">
                    </div>

                    <div class="mb-3">
                        <label for="deadline_to" class="form-label">Deadline To</label>
                        <input type="date" class="form-control" id="deadline_to" name="deadline_to" value="{{ request('deadline_to') }}">
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.tasks.filter') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                @if($tasks->isEmpty())
                    <p class="text-muted text-center py-5">No tasks found matching your filters</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                <tr>
                                    <td><strong>{{ $task->title }}</strong></td>
                                    <td>{{ $task->user->name }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($task->status === 'Completed') badge-success
                                            @elseif($task->status === 'Active') badge-primary
                                            @elseif($task->status === 'Pending') badge-warning
                                            @else badge-secondary
                                            @endif">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    <td>{{ $task->deadline->format('M d, Y') }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#taskModal{{ $task->id }}">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>

                                <!-- Task Modal -->
                                <div class="modal fade" id="taskModal{{ $task->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ $task->title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>User:</strong> {{ $task->user->name }} ({{ $task->user->email }})</p>
                                                <p><strong>Status:</strong> {{ $task->status }}</p>
                                                <p><strong>Deadline:</strong> {{ $task->deadline->format('M d, Y') }}</p>
                                                <hr>
                                                <h6>Description</h6>
                                                <p>{{ $task->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <nav>
                        {{ $tasks->links('pagination::bootstrap-4') }}
                    </nav>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
