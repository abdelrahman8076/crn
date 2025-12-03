@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
        <x-flash-error />
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('admins.edit_title') }}</h4>
        <a href="{{ route('admin.admin.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('admins.back') }}
        </a>
    </div>

    <form action="{{ route('admin.admin.update', $admin->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('admins.name') }}</label>
            <input type="text" name="name" id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $admin->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('admins.email') }}</label>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $admin->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password (optional) --}}
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('admins.password') }}</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('admins.leave_blank') }}">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">{{ __('admins.password_hint') }}</small>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-save me-1"></i> {{ __('admins.update') }}
        </button>
    </form>
</div>
@endsection
