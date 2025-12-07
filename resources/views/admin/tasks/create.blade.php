@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('tasks.create_title') }}</h4>

    <form action="{{ route('admin.tasks.store') }}" method="POST" class="mt-3">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">{{ __('tasks.title_field') }} *</label>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">{{ __('tasks.description') }} *</label>
            <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="assigned_to" class="form-label">{{ __('tasks.assigned_to') }}</label>
            <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">{{ __('Select User') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? '' }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">{{ __('tasks.status') }}</label>
            <select class="form-select" id="status" name="status" required>
                <option value="pending">{{ __('tasks.status_pending') }}</option>
                <option value="in-progress">{{ __('tasks.status_in_progress') }}</option>
                <option value="completed">{{ __('tasks.status_completed') }}</option>
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">{{ __('tasks.create') }}</button>
            <a href="{{ route('admin.tasks.index') }}" class="btn btn-secondary">{{ __('tasks.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
