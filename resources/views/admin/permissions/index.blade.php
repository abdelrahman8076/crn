@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">Permissions Management</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Permissions</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="ti ti-plus fs-5"></i> 
                <span class="fw-bold">Create Permission</span>
            </a>
        </div>
    </div>

    <div class="nexus-alerts">
        <x-flash-success />
        <x-flash-error />
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                <i class="ti ti-key text-primary fs-5"></i>
            </div>
            <h5 class="card-title mb-0 fw-bold">System Permissions</h5>
        </div>
        
        <div class="card-body p-4">
            @foreach($groups as $group => $groupPermissions)
                <div class="mb-4">
                    <h6 class="fw-bold text-primary mb-3">{{ ucfirst($group) }}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Permission Name</th>
                                    <th>Description</th>
                                    <th>Roles</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupPermissions as $permission)
                                    <tr>
                                        <td class="ps-3">
                                            <code class="text-primary">{{ $permission->name }}</code>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $permission->description ?? 'No description' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $permission->roles()->count() }} roles</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <div class="d-flex gap-2 justify-content-end">
                                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="ti ti-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this permission?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="ti ti-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
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
