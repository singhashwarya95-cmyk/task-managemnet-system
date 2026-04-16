@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h1>Admin Dashboard</h1>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #4F46E5 0%, #06B6D4 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.8;">Total Tasks</p>
                        <h3 class="mb-0">{{ $totalTasks }}</h3>
                    </div>
                    <i class="bi bi-list-check" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #10B981 0%, #06B6D4 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.8;">Total Users</p>
                        <h3 class="mb-0">{{ $totalUsers }}</h3>
                    </div>
                    <i class="bi bi-people" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #F59E0B 0%, #06B6D4 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.8;">Pending Requests</p>
                        <h3 class="mb-0">{{ $pendingTaskRequests }}</h3>
                    </div>
                    <i class="bi bi-inbox" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
            <a href="{{ route('admin.task-requests.pending') }}" class="card-footer bg-transparent border-top-0 text-white text-decoration-none">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-white" style="background: linear-gradient(135deg, #EF4444 0%, #06B6D4 100%);">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2" style="opacity: 0.8;">Pending Completions</p>
                        <h3 class="mb-0">{{ $pendingCompletions }}</h3>
                    </div>
                    <i class="bi bi-hourglass-split" style="font-size: 2rem; opacity: 0.7;"></i>
                </div>
            </div>
            <a href="{{ route('admin.completions.pending') }}" class="card-footer bg-transparent border-top-0 text-white text-decoration-none">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('admin.task-requests.pending') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-inbox"></i> Review Pending Task Requests
                    <span class="badge bg-warning float-end">{{ $pendingTaskRequests }}</span>
                </a>
                <a href="{{ route('admin.completions.pending') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-check-circle"></i> Verify Task Completions
                    <span class="badge bg-danger float-end">{{ $pendingCompletions }}</span>
                </a>
                <a href="{{ route('admin.task-requests.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-list"></i> All Task Requests
                </a>
                <a href="{{ route('admin.completions.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-list"></i> All Completions
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">System Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h6 class="text-muted mb-2">Tasks Created</h6>
                        <p class="h4 mb-0">{{ $totalTasks }}</p>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted mb-2">Active Users</h6>
                        <p class="h4 mb-0">{{ $totalUsers }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <h6 class="text-muted mb-2">Pending Actions</h6>
                        <p class="h4 mb-0">{{ $pendingTaskRequests + $pendingCompletions }}</p>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted mb-2">Items to Review</h6>
                        <p class="h4 mb-0">{{ $pendingTaskRequests }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
