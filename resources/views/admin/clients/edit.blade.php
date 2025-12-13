@extends('layouts.admin')

@section('content')
    <div class="container">
        <x-flash-success />
        <x-flash-error />

        <h4>{{ __('clients.edit_title') }}</h4>

        <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" class="mt-3">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.name') }} *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name', $client->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Company --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.company') }}</label>
                <input type="text" class="form-control @error('company') is-invalid @enderror" name="company"
                    value="{{ old('company', $client->company) }}">
                @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Address --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.address') }}</label>
                <textarea class="form-control @error('address') is-invalid @enderror" name="address"
                    rows="2">{{ old('address', $client->address) }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.email') }}</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email', $client->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Phone --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.phone') }}</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                    value="{{ old('phone', $client->phone) }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Assign to Sales sale --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.assigned_sale') }}</label>
                <select class="form-select" name="assigned_to_sale">
                    <option value="">{{ __('clients.select_sale') }}</option>

                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}" {{ old('assigned_to_sale', $client->assigned_to_sale) == $sale->id ? 'selected' : '' }}>
                            {{ $sale->name }} ({{ $sale->role->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Assign to Manager --}}
            @if(auth()->guard('admin')->check())
                <div class="mb-3">
                    <label class="form-label">{{ __('clients.assigned_manager') }}</label>
                    <select class="form-select" name="assigned_to_manager">
                        <option value="">{{ __('clients.select_manager') }}</option>

                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('assigned_to_manager', $client->assigned_to_manager) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} (Manager)
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @notrole('Sales')

            <button type="submit" class="btn btn-primary">
                {{ __('clients.update') }}
            </button>

            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                {{ __('clients.cancel') }}
            </a>
                        @endrole

        </form>
    </div>
@endsection