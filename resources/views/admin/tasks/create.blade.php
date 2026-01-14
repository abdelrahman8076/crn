@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}" class="text-decoration-none text-muted">{{ __('tasks.title') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ isset($task) ? __('tasks.edit_button') : __('tasks.create_button') }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 rounded-3">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-circle me-3">
                            <i class="ti ti-clipboard-list fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{ isset($task) ? __('tasks.edit_button') : __('tasks.create_button') }}</h4>
                            <p class="text-muted small mb-0">{{ __('tasks.subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <x-flash-success />
                    <x-flash-error />

                    <form action="{{ isset($task) ? route('admin.tasks.update', $task->id) : route('admin.tasks.store') }}" method="POST">
                        @csrf
                        @if(isset($task)) @method('PUT') @endif

                        <div class="row g-4">
                            {{-- Section 1: Task Content --}}
                            <div class="col-12">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-info-circle me-1"></i> {{ __('tasks.task_details') ?? 'Task Content' }}
                                </h6>
                            </div>

                            <div class="col-md-12">
                                <label for="title" class="form-label fw-bold small">{{ __('tasks.subject') }} *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-pencil"></i></span>
                                    <input type="text" name="title" id="title" 
                                           class="form-control border-start-0 @error('title') is-invalid @enderror" 
                                           value="{{ old('title', $task->title ?? '') }}" required placeholder="{{ __('tasks.subject_placeholder') ?? 'Enter task subject...' }}">
                                </div>
                                @error('title') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label for="description" class="form-label fw-bold small">{{ __('tasks.description') }} *</label>
                                <textarea name="description" id="description" rows="4" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          required placeholder="{{ __('tasks.desc_placeholder') ?? 'Describe the task requirements...' }}">{{ old('description', $task->description ?? '') }}</textarea>
                                @error('description') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Section 2: Assignment & Timeline --}}
                            <div class="col-12 mt-5">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-user-check me-1"></i> {{ __('tasks.assignment_timeline') ?? 'Assignment & Timeline' }}
                                </h6>
                            </div>

                            <div class="col-md-4">
                                <label for="assigned_to" class="form-label fw-bold small">{{ __('tasks.assigned_to') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user"></i></span>
                                    <select name="assigned_to" id="assigned_to" class="form-select border-start-0 @error('assigned_to') is-invalid @enderror">
                                        <option value="">{{ __('tasks.select_user') ?? 'Select User' }}</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to ?? '') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('assigned_to') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label fw-bold small">{{ __('tasks.status') }} *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-loader"></i></span>
                                    <select name="status" id="status" class="form-select border-start-0 @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $task->status ?? '') == 'pending' ? 'selected' : '' }}>{{ __('tasks.pending') }}</option>
                                        <option value="in-progress" {{ old('status', $task->status ?? '') == 'in-progress' ? 'selected' : '' }}>{{ __('tasks.in_progress') }}</option>
                                        <option value="completed" {{ old('status', $task->status ?? '') == 'completed' ? 'selected' : '' }}>{{ __('tasks.completed') }}</option>
                                    </select>
                                </div>
                                @error('status') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="due_date" class="form-label fw-bold small">{{ __('tasks.due_date') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar"></i></span>
                                    <input type="date" name="due_date" id="due_date" 
                                           class="form-control border-start-0 @error('due_date') is-invalid @enderror" 
                                           value="{{ old('due_date', $task->due_date ?? '') }}">
                                </div>
                                @error('due_date') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tasks.index') }}" class="btn btn-light px-4 fw-bold">{{ __('tasks.cancel') }}</a>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm fw-bold">
                                <i class="ti ti-device-floppy me-1"></i> {{ isset($task) ? __('tasks.update') : __('tasks.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08); }
    .extra-small { font-size: 0.75rem; }
    .form-label { color: #4b5563; }
    .input-group-text { color: #6b7280; border-color: #dee2e6; }
    .form-control, .form-select { border-color: #dee2e6; }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25 mil rgba(13, 110, 253, 0.1); }
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; }
</style>
@endsection