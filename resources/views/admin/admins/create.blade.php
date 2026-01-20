@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">{{ __('admins.create_title') }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.admin.index') }}" class="text-muted text-decoration-none">{{ __('admins.title') }}</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ __('admins.create_button') }}</li>
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
                        <i class="ti ti-user-plus text-primary fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">Account Information</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.admin.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.name') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user text-muted"></i></span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" placeholder="Enter full name" required>
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
                                           value="{{ old('email') }}" placeholder="admin@nexus-crm.com" required>
                                </div>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                                    <input type="password" name="password" id="password" 
                                           class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" 
                                           placeholder="••••••••" required>
                                </div>
                                <div class="form-text mt-1 small">Password must be at least 6 characters.</div>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.confirm_password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-lock text-muted"></i></span>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="form-control border-start-0 ps-0" 
                                           placeholder="••••••••" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="role_id" class="form-label fw-semibold text-muted small text-uppercase">
                                    {{ __('admins.role') ?? 'Role' }} <span class="text-danger">*</span>
                                </label>
                                <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                    <option value="">{{ __('admins.select_role') ?? 'Select Role' }}</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-3">
                                <hr class="text-light">
                            </div>

                            {{-- Permissions Section --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-info fw-bold mb-0">{{ __('admins.permissions') ?? 'Permissions' }}</h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-info" id="select-all-permissions">
                                            {{ __('admins.select_all') ?? 'Select All' }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all-permissions">
                                            {{ __('admins.deselect_all') ?? 'Deselect All' }}
                                        </button>
                                    </div>
                                </div>
                                <div class="permissions-container">
                                    @if(isset($permissions) && $permissions->count() > 0)
                                        @foreach($permissions as $module => $modulePermissions)
                                            <div class="mb-3">
                                                <h6 class="permission-module-title mb-2 fw-bold">{{ $module }}</h6>
                                                <div class="row g-2">
                                                    @foreach($modulePermissions as $permission)
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input 
                                                                    class="form-check-input permission-checkbox" 
                                                                    type="checkbox" 
                                                                    name="permissions[]" 
                                                                    value="{{ $permission->id }}" 
                                                                    id="permission_{{ $permission->id }}"
                                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}
                                                                >
                                                                <label class="form-check-label permission-label" for="permission_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                    @if($permission->description)
                                                                        <small class="permission-description d-block">{{ $permission->description }}</small>
                                                                    @endif
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            @if(!$loop->last)
                                                <hr class="my-2">
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="text-muted">{{ __('admins.no_permissions_available') ?? 'No permissions available. Please run the permissions seeder.' }}</p>
                                    @endif
                                </div>
                                <small class="text-muted d-block mt-2">{{ __('admins.permissions_help') ?? 'Select permissions for this admin\'s role. Permissions are assigned to the role, not the individual admin. If no permissions are selected, the role will have no permissions.' }}</small>
                            </div>

                            <div class="col-12 mt-5">
                                <hr class="text-light">
                                <div class="d-flex justify-content-end gap-2">
                                    <button type="reset" class="btn btn-light px-4 fw-bold">Clear Form</button>
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="ti ti-circle-check fs-5"></i> 
                                        <span class="fw-bold">{{ __('admins.save') }}</span>
                                    </button>
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

    /* Input Styling */
    .form-control {
        border-color: #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .input-group-text {
        border-color: #e2e8f0;
        color: #64748b;
    }

    .card-header .bg-primary.bg-opacity-10 {
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Permissions Container Styles */
    .permissions-container {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        background-color: #f8f9fa;
    }

    .permission-module-title {
        color: #6c757d;
    }

    .permission-label {
        color: #212529;
    }

    .permission-description {
        color: #6c757d;
    }

    /* Dark Mode Styles for Permissions */
    [data-bs-theme="dark"] .permissions-container {
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .permission-module-title {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .permission-label {
        color: #e2e8f0 !important;
    }

    [data-bs-theme="dark"] .permission-description {
        color: #cbd5e1 !important;
    }

    [data-bs-theme="dark"] .permissions-container .form-check-input {
        background-color: #334155 !important;
        border-color: #475569 !important;
    }

    [data-bs-theme="dark"] .permissions-container .form-check-input:checked {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
    }

    [data-bs-theme="dark"] .permissions-container hr {
        border-color: #334155 !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Select All / Deselect All Permissions
        const selectAllBtn = document.getElementById('select-all-permissions');
        const deselectAllBtn = document.getElementById('deselect-all-permissions');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', () => {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
            });
        }

        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', () => {
                permissionCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
        }
    });
</script>
@endsection