@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">Create Permission</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.permissions.index') }}" class="text-muted text-decoration-none">Permissions</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Create</li>
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
                        <i class="ti ti-plus text-primary fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">New Permission</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.permissions.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-muted small text-uppercase">
                                    Permission Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-key text-muted"></i></span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" placeholder="e.g., users.view, clients.manage_all" required>
                                </div>
                                <small class="text-muted">Format: resource.action (e.g., users.view, clients.create)</small>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="group" class="form-label fw-semibold text-muted small text-uppercase">
                                    Group
                                </label>
                                <input type="text" name="group" id="group" 
                                       class="form-control @error('group') is-invalid @enderror" 
                                       value="{{ old('group') }}" 
                                       placeholder="e.g., users, clients, deals"
                                       list="group-suggestions">
                                <datalist id="group-suggestions">
                                    @foreach($groups as $group)
                                        <option value="{{ $group }}">
                                    @endforeach
                                </datalist>
                                <small class="text-muted">Group for organizing permissions</small>
                                @error('group')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="description" class="form-label fw-semibold text-muted small text-uppercase">
                                    Description
                                </label>
                                <input type="text" name="description" id="description" 
                                       class="form-control @error('description') is-invalid @enderror" 
                                       value="{{ old('description') }}" 
                                       placeholder="Brief description of this permission">
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <hr class="text-light">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light px-4 fw-bold">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="ti ti-circle-check fs-5"></i> 
                                        <span class="fw-bold">Create Permission</span>
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
