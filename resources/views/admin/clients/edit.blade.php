@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ isset($client) ? ('clients.edit_title') : ('clients.create_title') }}</h4>

    <form action="{{ isset($client) ? route('admin.clients.update', $client->id) : route('admin.clients.store') }}" 
          method="POST" class="mt-3">
        @csrf
        @if(isset($client))
            @method('PUT')
        @endif

        {{-- Name --}}
        <div class="mb-3">
            <label for="name" class="form-label">{{ ('clients.name') }} *</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                   value="{{ old('name', $client->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">{{ ('clients.email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                   value="{{ old('email', $client->email ?? '') }}">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="mb-3">
            <label for="phone" class="form-label">{{ ('clients.phone') }}</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone"
                   value="{{ old('phone', $client->phone ?? '') }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Assigned To --}}
        <div class="mb-3">
            <label for="assigned_to" class="form-label">{{ ('clients.assigned_to') }}</label>
            <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">{{ ('clients.select_user') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" 
                        {{ (old('assigned_to', $client->assigned_to ?? '') == $user->id) ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->role->name ?? '' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-primary">
                {{ isset($client) ? ('clients.update') : ('clients.create') }}
            </button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">{{ ('clients.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
