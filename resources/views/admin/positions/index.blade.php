@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">Positions Management</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">Positions</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.positions.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="ti ti-plus fs-5"></i> 
                <span class="fw-bold">Create Position</span>
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
                <i class="ti ti-hierarchy text-primary fs-5"></i>
            </div>
            <h5 class="card-title mb-0 fw-bold">Hierarchy Positions</h5>
        </div>
        
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Position</th>
                            <th>Description</th>
                            <th>Level</th>
                            <th>Parent</th>
                            <th>Users</th>
                            <th>Admins</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $position)
                            <tr>
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold">{{ str_repeat('— ', $position->level) }}{{ $position->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $position->description ?? 'No description' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $position->level }}</span>
                                </td>
                                <td>
                                    @if($position->parent)
                                        <span class="text-muted">{{ $position->parent->name }}</span>
                                    @else
                                        <span class="text-muted">Root</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $position->users()->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $position->admins()->count() }}</span>
                                </td>
                                <td class="text-end pe-3">
                                    <div class="d-flex gap-2 justify-content-end">
                                        <a href="{{ route('admin.positions.edit', $position) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="ti ti-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.positions.destroy', $position) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this position?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="ti ti-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No positions found. <a href="{{ route('admin.positions.create') }}">Create one</a></td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
