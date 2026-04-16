@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="mb-4">Dashboard</h1>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #2563EB 0%, #0891B2 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.85; font-size: 0.9rem;">Total Tasks</p>
                        <h3 class="mb-0">{{ $tasks->count() }}</h3>
                    </div>
                    <i class="bi bi-list-check" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #16A34A 0%, #059669 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.85; font-size: 0.9rem;">Completed</p>
                        <h3 class="mb-0">{{ $tasks->where('status', 'Completed')->count() }}</h3>
                    </div>
                    <i class="bi bi-check-circle-fill" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #EA580C 0%, #D97706 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.85; font-size: 0.9rem;">Active</p>
                        <h3 class="mb-0">{{ $tasks->where('status', 'Active')->count() }}</h3>
                    </div>
                    <i class="bi bi-lightning-fill" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.85; font-size: 0.9rem;">Pending</p>
                        <h3 class="mb-0">{{ $tasks->where('status', 'Pending')->count() }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-task"></i> Recent Tasks</h5>
        <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> New Task
        </a>
    </div>
    <div class="card-body">
        @if($tasks->isEmpty())
            <p class="text-muted text-center py-5">No tasks yet. <a href="{{ route('tasks.create') }}">Create one</a></p>
        @else
            @include('components.task-table', ['tasks' => $tasks->take(10)])
        @endif
    </div>
</div>
@endsection
