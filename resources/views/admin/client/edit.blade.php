@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('Edit Client') }}</h4>

    <form action="{{ route('admin.clients.update', $client->id) }}" 
          method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }} *</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ old('name', $client->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="{{ old('email', $client->email) }}">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('Phone') }}</label>
            <input type="text" class="form-control" id="phone" name="phone"
                   value="{{ old('phone', $client->phone) }}">
        </div>

        <div class="mb-3">
            <label for="assigned_to" class="form-label">{{ __('Assigned To') }}</label>
            <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">{{ __('Select User') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" 
                        {{ (old('assigned_to', $client->assigned_to) == $user->id) ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->role->name ?? '' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection
