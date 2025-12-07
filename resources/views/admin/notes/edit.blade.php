@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('notes.edit_title') }}</h4>

    <form action="{{ route('admin.notes.update', $note->id) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="title" class="form-label">{{ __('notes.title_field') }} *</label>
            <input type="text" class="form-control" id="title" name="title" 
                   value="{{ old('title', $note->title) }}" required>
        </div>

        <div class="mb-3">
            <label for="content" class="form-label">{{ __('notes.content') }} *</label>
            <textarea class="form-control" id="content" name="content" rows="4" required>{{ old('content', $note->content) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="assigned_to" class="form-label">{{ __('notes.assigned_to') }}</label>
            <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">{{ __('Select User') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ ($note->assigned_to == $user->id) ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->role->name ?? '' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-success">{{ __('notes.update') }}</button>
            <a href="{{ route('admin.notes.index') }}" class="btn btn-secondary">{{ __('notes.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
