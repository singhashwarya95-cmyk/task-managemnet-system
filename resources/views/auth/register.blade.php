@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="row justify-content-center" style="min-height: 80vh; align-items: center;">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Register</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" required>
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Register</button>

                    <p class="text-center mb-0">
                        Already have an account? <a href="{{ route('login') }}">Login here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
