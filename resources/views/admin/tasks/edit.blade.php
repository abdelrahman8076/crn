@extends('layouts.admin')

@section('content')
@php
$user = auth()->guard('admin')->user() ?? auth()->user();    $isAssignee = ($user->id == $task->assigned_to);
    
    // Safety check for role (matches your earlier hasRole fix)
    $isAdmin = $user->role && auth()->guard('admin')->check(); 
    $isCreator = ($user->id == $task->created_by);
    
    // Determine if the general form is readonly
    $formLocked = ($isAssignee && !$isAdmin && !$isCreator);
    $readonlyAttr = $formLocked ? 'disabled' : '';
@endphp

<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}" class="text-decoration-none text-muted">{{ __('tasks.title') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ __('tasks.edit_button') }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Permission Alert --}}
            @if($formLocked)
                <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex align-items-center bg-soft-warning">
                    <i class="ti ti-alert-circle fs-4 text-warning me-3"></i>
                    <div>
                        <strong class="text-warning">{{ __('tasks.limited_access') ?? 'Limited Access' }}</strong>: 
                        {{ __('tasks.assignee_status_only_note') ?? 'As the assignee, you can only update the status of this task.' }}
                    </div>
                </div>
            @endif

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-4 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary p-3 rounded-circle me-3">
                                <i class="ti ti-edit fs-2 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">{{ __('tasks.edit_button') }}</h4>
                                <p class="text-muted small mb-0">{{ __('tasks.subtitle') }}</p>
                            </div>
                        </div>
                        <span class="badge bg-soft-secondary text-secondary px-3 py-2 rounded-pill small">
                            ID: #{{ $task->id }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 pt-0">
                    <x-flash-success />
                    <x-flash-error />

                    <form action="{{ route('admin.tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Section 1: Task Content --}}
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-file-description me-1"></i> {{ __('tasks.task_details') }}
                                </h6>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">{{ __('tasks.title_field') }} *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-pencil"></i></span>
                                    <input type="text" name="title" class="form-control border-start-0" 
                                           value="{{ old('title', $task->title) }}" required {{ $readonlyAttr }}>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-muted">{{ __('tasks.description') }} *</label>
                                <textarea name="description" rows="4" class="form-control" required {{ $readonlyAttr }}>{{ old('description', $task->description) }}</textarea>
                            </div>

                            {{-- Section 2: Assignment & Logistics --}}
                            <div class="col-12 mt-5">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-settings me-1"></i> {{ __('tasks.assignment_timeline') }}
                                </h6>
                            </div>

                            {{-- HIDE ASSIGNED TO IF IT IS THE USER --}}
                            @if(!$isAssignee || $isAdmin)
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-muted">{{ __('tasks.assigned_to') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user-circle"></i></span>
                                    <select name="assigned_to" class="form-select border-start-0" {{ $readonlyAttr }}>
                                        <option value="">{{ __('tasks.select_user') }}</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ $task->assigned_to == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif

                            {{-- STATUS: ALWAYS ENABLED --}}
                            <div class="{{ (!$isAssignee || $isAdmin) ? 'col-md-4' : 'col-md-6' }}">
                                <label class="form-label fw-bold small text-muted">{{ __('tasks.status') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-list-details"></i></span>
                                    <select name="status" class="form-select border-start-0 border-primary shadow-sm" required>
                                        <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>{{ __('tasks.pending') }}</option>
                                        <option value="in-progress" {{ $task->status == 'in-progress' ? 'selected' : '' }}>{{ __('tasks.in_progress') }}</option>
                                        <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>{{ __('tasks.completed') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="{{ (!$isAssignee || $isAdmin) ? 'col-md-4' : 'col-md-6' }}">
                                <label class="form-label fw-bold small text-muted">{{ __('tasks.due_date') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar-event"></i></span>
                                    <input type="date" name="due_date" class="form-control border-start-0" 
                                           value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d') : '') }}" {{ $readonlyAttr }}>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-light px-4 fw-bold">{{ __('tasks.cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                <i class="ti ti-device-floppy me-1"></i> {{ __('tasks.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.12) !important; }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08) !important; }
    .bg-soft-secondary { background-color: rgba(108, 117, 125, 0.1) !important; }
    .form-control:disabled, .form-select:disabled { background-color: #f8f9fa; cursor: not-allowed; color: #adb5bd; }
    .border-primary { border-color: #0d6efd !important; }
</style>
@endsection