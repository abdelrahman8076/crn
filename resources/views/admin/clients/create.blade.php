@extends('layouts.admin')

@section('content')
    <div class="container">
        <x-flash-success />
        <x-flash-error />

        <h4>{{ __('clients.create_title') }}</h4>

        <form action="{{ route('admin.clients.store') }}" method="POST" class="mt-3">
            @csrf

            {{-- Name --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.name') }} *</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Company --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.company') }}</label>
                <input type="text" class="form-control @error('company') is-invalid @enderror" name="company"
                    value="{{ old('company') }}">
                @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Address --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.address') }}</label>
                <textarea class="form-control @error('address') is-invalid @enderror" name="address"
                    rows="2">{{ old('address') }}</textarea>
                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.email') }}</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Phone --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.phone') }}</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone"
                    value="{{ old('phone') }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            {{-- Source --}}
            <div class="mb-3">
                <label for="source" class="form-label">{{ __('clients.source') }}</label>
                <input type="text" class="form-control" id="source" name="source" value="{{ old('source') }}">
                @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label for="status" class="form-label">{{ __('clients.status') }}</label>
                <select class="form-select" id="status" name="status">
                    <option value="">{{ __('clients.select_status') }}</option>
                    <option value="new" {{ old('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ old('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="qualified" {{ old('status') === 'qualified' ? 'selected' : '' }}>Qualified</option>
                    <option value="lost" {{ old('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Assign to Sales User --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.assigned_user') }}</label>
                <select class="form-select" name="assigned_to_user">
                    <option value="">{{ __('clients.select_user') }}</option>

                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}" {{ old('assigned_to_user') == $sale->id ? 'selected' : '' }}>
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
                            <option value="{{ $manager->id }}" {{ old('assigned_to_manager') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }} (Manager)
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif


            <button type="submit" class="btn btn-primary">{{ __('clients.create') }}</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">{{ __('clients.cancel') }}</a>
        </form>
    </div>
@endsection