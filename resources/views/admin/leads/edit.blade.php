@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('leads.edit_title') }}</h4>

    <form action="{{ route('admin.leads.update', $lead->id) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="mb-3">
            <label for="title" class="form-label">{{ __('leads.name') }} *</label>
            <input type="text" class="form-control" id="title" name="title" 
                   value="{{ old('title', $lead->title) }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Source --}}
        <div class="mb-3">
            <label for="source" class="form-label">{{ __('leads.source') }}</label>
            <input type="text" class="form-control" id="source" name="source"
                   value="{{ old('source', $lead->source) }}">
            @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label for="status" class="form-label">{{ __('leads.status') }}</label>
            <select class="form-select" id="status" name="status">
                <option value="">{{ __('leads.select_status') }}</option>
                <option value="new" {{ old('status', $lead->status) === 'new' ? 'selected' : '' }}>New</option>
                <option value="contacted" {{ old('status', $lead->status) === 'contacted' ? 'selected' : '' }}>Contacted</option>
                <option value="qualified" {{ old('status', $lead->status) === 'qualified' ? 'selected' : '' }}>Qualified</option>
                <option value="lost" {{ old('status', $lead->status) === 'lost' ? 'selected' : '' }}>Lost</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('leads.email') }}</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="{{ old('email', $lead->email) }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>


        {{-- Assign to Client --}}
        <div class="mb-3">
            <label for="client_id" class="form-label">{{ __('leads.assign_client') }}</label>
            <select class="form-select" id="client_id" name="client_id">
                <option value="">{{ __('leads.select_client') }}</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id', $lead->client_id) == $client->id ? 'selected' : '' }}>
                        {{ $client->name }} ({{ $client->company ?? '—' }})
                    </option>
                @endforeach
            </select>
            @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

  

        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-success">{{ __('leads.update') }}</button>
            <a href="{{ route('admin.leads.index') }}" class="btn btn-secondary">{{ __('leads.cancel') }}</a>
        </div>
    </form>
</div>
@endsection
