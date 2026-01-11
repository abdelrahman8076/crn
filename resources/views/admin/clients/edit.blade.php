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

            {{-- Phone & WhatsApp Integration --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.phone') }}</label>
                <div class="input-group">
                    <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        value="{{ old('phone', $client->phone) }}">
                    <button class="btn btn-outline-success" type="button" onclick="openWhatsApp()">
                        <i class="bi bi-whatsapp"></i>
                    </button>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.email') }}</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email', $client->email) }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Status (Updated 6-Option List) --}}
       <div class="mb-3">
    <label for="status" class="form-label fw-bold">{{ __('clients.status') }}</label>
    <div class="input-group">
        <span class="input-group-text bg-light text-primary"><i class="ti ti-flag"></i></span>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" style="border-radius: 0 8px 8px 0;">
            <option value="">{{ __('clients.select_status') }}</option>
            @php
                $statuses = [
                    'potential'     => '1 - Potential',
                    'not_potential' => '2 - Not Potential',
                    'hot_case'      => '3 - Hot Case',
                    'closed_deal'   => '4 - Closed Deal',
                    'no_answer'     => '5 - No Answer',
                    'meeting_done'  => '6 - Meeting Done'
                ];
            @endphp
            @foreach($statuses as $key => $label)
                {{-- Use getRawOriginal to bypass the Accessor for the 'selected' check --}}
                <option value="{{ $key }}" {{ old('status', $client->getRawOriginal('status')) === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>
    @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
</div>

            {{-- Feedback / Note --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.feedback') }}</label>
                <textarea class="form-control @error('feedback') is-invalid @enderror" name="feedback"
                    rows="3">{{ old('feedback', $client->feedback) }}</textarea>
                @error('feedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                {{-- Company --}}
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('clients.company') }}</label>
                    <input type="text" class="form-control @error('company') is-invalid @enderror" name="company"
                        value="{{ old('company', $client->company) }}">
                    @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Source --}}
                <div class="col-md-6 mb-3">
                    <label for="source" class="form-label">{{ __('clients.source') }}</label>
                    <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source"
                        value="{{ old('source', $client->source) }}">
                    @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Assignment Logic --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('clients.assigned_sale') }}</label>
                    <select class="form-select" name="assigned_to_sale">
                        <option value="">{{ __('clients.select_sale') }}</option>
                        @foreach($sales as $sale)
                            <option value="{{ $sale->id }}" {{ old('assigned_to_sale', $client->assigned_to_sale) == $sale->id ? 'selected' : '' }}>
                                {{ $sale->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->guard('admin')->check() || (auth()->user() && auth()->user()->role?->name !== 'manager'))
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('clients.assigned_manager') }}</label>
                        <select class="form-select" name="assigned_to_manager">
                            <option value="">{{ __('clients.select_manager') }}</option>
                            @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('assigned_to_manager', $client->assigned_to_manager) == $manager->id ? 'selected' : '' }}>
                                    {{ $manager->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            @if (!isSales())
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('clients.update') }}
                    </button>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">
                        {{ __('clients.cancel') }}
                    </a>
                </div>
            @endif
        </form>
    </div>

    <script>
        function openWhatsApp() {
            const phone = document.getElementById('phone').value;
            if (!phone) {
                alert("Please enter a phone number first.");
                return;
            }
            const cleanPhone = phone.replace(/\D/g, '');
            window.open(`https://wa.me/${cleanPhone}`, '_blank');
        }
    </script>
@endsection