@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">Edit Role: {{ $role->name }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.roles.index') }}" class="text-muted text-decoration-none">Roles</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="nexus-alerts">
        <x-flash-success />
        <x-flash-error />
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                        <i class="ti ti-edit text-primary fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">Update Role</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-muted small text-uppercase">
                                    Role Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-shield text-muted"></i></span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $role->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="fw-bold text-primary mb-3">Assign Permissions</h6>
                                <div class="border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                                    @php
                                        $permissionGroups = $permissions->groupBy('group');
                                    @endphp
                                    @foreach($permissionGroups as $group => $groupPermissions)
                                        <div class="mb-3">
                                            <strong class="text-primary d-block mb-2">{{ ucfirst($group) }}</strong>
                                            <div class="row g-2">
                                                @foreach($groupPermissions as $permission)
                                                    <div class="col-md-6 col-lg-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permission_ids[]" 
                                                                   value="{{ $permission->id }}" 
                                                                   id="role_edit_perm_{{ $permission->id }}"
                                                                   {{ in_array($permission->id, old('permission_ids', $rolePermissionIds)) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="role_edit_perm_{{ $permission->id }}">
                                                                {{ str_replace($group . '.', '', $permission->name) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">Select permissions to assign to this role</small>
                            </div>

                            <div class="col-12 mt-4">
                                <hr class="text-light">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light px-4 fw-bold">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="ti ti-device-floppy fs-5"></i> 
                                        <span class="fw-bold">Update Role</span>
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
</style>
@endsection
