@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('notes.create_title') }}</h4>

    <form action="{{ route('admin.notes.store') }}" method="POST" class="mt-3">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">{{ __('notes.title_field') }} *</label>
            <input type="text" class="form-control" id="title" name="title" 
                   value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">{{ __('notes.content') }} *</label>
            <textarea class="form-control" id="content" name="content" rows="4" required>{{ old('content') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="assigned_to" class="form-label">{{ __('notes.assigned_to') }}</label>
            <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">{{ __('Select User') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? '' }})</option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">{{ __('notes.create') }}</button>
            <a href="{{ route('admin.notes.index') }}" class="btn btn-secondary">{{ __('notes.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
