@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">{{ __('admins.edit_title') }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admin.index') }}" class="text-muted text-decoration-none">{{ __('admins.title') }}</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ __('admins.edit_title') }}</li>
                </ol>
            </nav>
        </div>
  
    </div>

    <div class="nexus-alerts">
        <x-flash-success />
        <x-flash-error />
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                        <i class="ti ti-edit text-primary fs-5"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-0 fw-bold">Update Account</h5>
                        <p class="mb-0 text-muted small">Editing: <span class="text-dark fw-semibold">{{ $admin->email }}</span></p>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.admin.update', $admin->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.name') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $admin->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.email') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail text-muted"></i></span>
                                    <input type="email" name="email" id="email" 
                                           class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                                           value="{{ old('email', $admin->email) }}" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.password') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                                    <input type="password" name="password" id="password" 
                                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                           placeholder="{{ __('admins.leave_blank') }}">
                                </div>
                                <div class="form-text mt-1 small text-muted">{{ __('admins.password_hint') }}</div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-5">
                                <hr class="text-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="ti ti-info-circle"></i> Last updated {{ $admin->updated_at->diffForHumans() }}
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.admin.index') }}" class="btn btn-light px-4 fw-bold">Cancel</a>
                                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                            <i class="ti ti-device-floppy fs-5"></i> 
                                            <span class="fw-bold">{{ __('admins.update') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .tracking-tight { letter-spacing: -0.5px; }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "•";
        color: #cbd5e1;
    }

    .form-control {
        border-color: #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-group-text {
        border-color: #e2e8f0;
        color: #64748b;
    }

    /* Style for AR/RTL support without messy inline styles */
    [dir="rtl"] .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-top-left-radius: 0.5rem;
        border-bottom-left-radius: 0.5rem;
        border-right-width: 0;
        border-left-width: 1px;
        padding-right: 0.75rem;
    }
</style>
@endsection