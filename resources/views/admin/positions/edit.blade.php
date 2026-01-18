@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">Edit Position: {{ $position->name }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.positions.index') }}" class="text-muted text-decoration-none">Positions</a></li>
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
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                        <i class="ti ti-edit text-primary fs-5"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold">Update Position</h5>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.positions.update', $position) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-muted small text-uppercase">
                                    Position Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-tag text-muted"></i></span>
                                    <input type="text" name="name" id="name" 
                                           class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $position->name) }}" required>
                                </div>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-muted small text-uppercase">
                                    Description
                                </label>
                                <textarea name="description" id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3">{{ old('description', $position->description) }}</textarea>
                                @error('description')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="parent_id" class="form-label fw-semibold text-muted small text-uppercase">
                                    Parent Position
                                </label>
                                <select name="parent_id" id="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">No Parent (Root Level)</option>
                                    @foreach($positions as $pos)
                                        @if($pos->id != $position->id)
                                            <option value="{{ $pos->id }}" {{ old('parent_id', $position->parent_id) == $pos->id ? 'selected' : '' }}>
                                                {{ str_repeat('— ', $pos->level) }}{{ $pos->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <small class="text-muted">Changing parent will recalculate hierarchy level</small>
                                @error('parent_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="sort_order" class="form-label fw-semibold text-muted small text-uppercase">
                                    Sort Order
                                </label>
                                <input type="number" name="sort_order" id="sort_order" 
                                       class="form-control @error('sort_order') is-invalid @enderror" 
                                       value="{{ old('sort_order', $position->sort_order) }}" min="0">
                                <small class="text-muted">Current Level: <strong>{{ $position->level }}</strong></small>
                                @error('sort_order')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <hr class="text-light">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.positions.index') }}" class="btn btn-light px-4 fw-bold">Cancel</a>
                                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                                        <i class="ti ti-device-floppy fs-5"></i> 
                                        <span class="fw-bold">Update Position</span>
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
