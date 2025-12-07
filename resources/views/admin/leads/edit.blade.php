@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('leads.edit_title') }}</h4>

    <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('leads.name') }} *</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ old('name', $lead->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('leads.email') }}</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="{{ old('email', $lead->email) }}">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">{{ __('leads.phone') }}</label>
            <input type="text" class="form-control" id="phone" name="phone"
                   value="{{ old('phone', $lead->phone) }}">
        </div>

        <div class="mb-3">
            <label for="company" class="form-label">{{ __('leads.company') }}</label>
            <input type="text" class="form-control" id="company" name="company"
                   value="{{ old('company', $lead->company) }}">
        </div>

        <div class="mb-3">
            <label for="assigned_to" class="form-label">{{ __('leads.assigned_to') }}</label>
            <select class="form-select" id="assigned_to" name="assigned_to">
                <option value="">{{ __('Select User') }}</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ ($lead->assigned_to == $user->id) ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->role->name ?? '' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-success">{{ __('leads.update') }}</button>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">{{ __('leads.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
