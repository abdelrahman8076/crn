@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('users.edit_title') }}</h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('users.back') }}
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('users.name') }} *</label>
            <input type="text" name="name" id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('users.email') }} *</label>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Password (optional) --}}
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('users.password') }}</label>
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="{{ __('users.leave_blank') }}">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">{{ __('users.password_hint') }}</small>
        </div>

        {{-- Role --}}
        <div class="mb-3">
            <label for="role_id" class="form-label">{{ __('users.role') }} *</label>
            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">{{ __('users.select_role') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Manager --}}
        <div class="mb-3">
            <label for="manager_id" class="form-label">{{ __('users.manager') }}</label>
            <select name="manager_id" id="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                <option value="">{{ __('users.select_manager') }}</option>
                @foreach($users as $manager)
                    <option value="{{ $manager->id }}" {{ old('manager_id', $user->manager_id) == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
            @error('manager_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-success">
            <i class="bi bi-save me-1"></i> {{ __('users.update') }}
        </button>
    </form>
</div>
@endsection
