@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
        <x-flash-error />
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('admins.create_title') }}</h4>
        <a href="{{ route('admin.admin.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('admins.back') }}
        </a>
    </div>

    <form action="{{ route('admin.admin.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admins.name') }}</label>
            <input type="text" name="name" id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admins.email') }}</label>
            <input type="email" name="email" id="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admins.password') }}</label>
            <input type="password" name="password" id="password" 
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i> {{ __('admins.save') }}
        </button>
    </form>
</div>
@endsection
