@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h1><i class="bi bi-list-task"></i> My Tasks</h1>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create New Task
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        @if($tasks->isEmpty())
            <p class="text-muted text-center py-5">No tasks found. <a href="{{ route('tasks.create') }}">Create your first task</a></p>
        @else
            @include('components.task-table', ['tasks' => $tasks])

            <nav>
                {{ $tasks->links('pagination::bootstrap-4') }}
            </nav>
        @endif
    </div>
</div>
@endsection
