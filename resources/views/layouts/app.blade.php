<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Task Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2563EB;
            --secondary-color: #0891B2;
            --success-color: #16A34A;
            --danger-color: #DC2626;
            --warning-color: #EA580C;
            --info-color: #0284C7;
            --dark-bg: #0F172A;
            --light-bg: #F8FAFC;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1E293B;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--dark-bg) 0%, #1E293B 100%);
            color: white;
            min-height: 100vh;
            padding: 20px 0;
        }

        .sidebar a {
            color: #CBD5E1;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--primary-color);
            color: white;
            border-left-color: var(--secondary-color);
        }

        .main-content {
            padding: 30px;
        }

        .card {
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            margin-bottom: 20px;
            background: white;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 20px;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: #1D4ED8;
            border-color: #1D4ED8;
        }

        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .alert {
            border-radius: 12px;
            border: none;
        }

        .alert-success {
            background-color: #DCFCE7;
            color: #166534;
        }

        .alert-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .alert-warning {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .badge {
            font-weight: 600;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
        }

        .badge-success {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .badge-danger {
            background-color: #FEE2E2;
            color: #7F1D1D;
        }

        .badge-warning {
            background-color: #FEF08A;
            color: #78350F;
        }

        .badge-primary {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .badge-secondary {
            background-color: #E2E8F0;
            color: #334155;
        }

        table {
            font-size: 0.95rem;
        }

        table th {
            background-color: #F1F5F9;
            color: #334155;
            font-weight: 600;
            border-bottom: 2px solid #E2E8F0;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #E2E8F0;
            padding: 0.6rem 0.75rem;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--dark-bg);
            font-weight: 700;
        }

        .text-muted {
            color: #64748B !important;
        }
    </style>
    @yield('extra-styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="bi bi-list-check"></i> Task Manager
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link">Welcome, {{ Auth::user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            @auth
            <div class="col-md-3 sidebar">
                @if(Auth::user()->role === 'admin')
                    <h6 class="px-3 mb-3" style="color: #9CA3AF; text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">Admin Menu</h6>
                    <a href="{{ route('admin.dashboard') }}" class="@if(request()->routeIs('admin.dashboard')) active @endif">
                        <i class="bi bi-graph-up"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.task-requests.index') }}" class="@if(request()->routeIs('admin.task-requests.index')) active @endif">
                        <i class="bi bi-inbox"></i> Task Requests
                    </a>
                    <a href="{{ route('admin.task-requests.pending') }}" class="@if(request()->routeIs('admin.task-requests.pending')) active @endif">
                        <i class="bi bi-exclamation-circle"></i> Pending Requests
                    </a>
                    <a href="{{ route('admin.completions.index') }}" class="@if(request()->routeIs('admin.completions.index')) active @endif">
                        <i class="bi bi-check-circle"></i> Completions
                    </a>
                    <a href="{{ route('admin.completions.pending') }}" class="@if(request()->routeIs('admin.completions.pending')) active @endif">
                        <i class="bi bi-hourglass"></i> Pending Completions
                    </a>
                @else
                    <h6 class="px-3 mb-3" style="color: #9CA3AF; text-transform: uppercase; font-size: 0.75rem; font-weight: 600;">User Menu</h6>
                    <a href="{{ route('dashboard') }}" class="@if(request()->routeIs('dashboard')) active @endif">
                        <i class="bi bi-house"></i> Dashboard
                    </a>
                    <a href="{{ route('tasks.index') }}" class="@if(request()->routeIs('tasks.index')) active @endif">
                        <i class="bi bi-list-ul"></i> My Tasks
                    </a>
                    <a href="{{ route('tasks.create') }}" class="@if(request()->routeIs('tasks.create')) active @endif">
                        <i class="bi bi-plus-circle"></i> Create Task
                    </a>
                @endif
            </div>
            @endauth

            <div class="@auth col-md-9 @else col-md-12 @endauth">
                <div class="main-content">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <strong>Error!</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('extra-scripts')
</body>
</html>
