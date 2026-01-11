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

            {{-- Phone & WhatsApp Integration --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.phone') }}</label>
                <div class="input-group">
                    <input type="text" id="phone" class="form-control @error('phone') is-invalid @enderror" name="phone"
                        value="{{ old('phone') }}">
                    <button class="btn btn-outline-success" type="button" onclick="openWhatsApp()">
                        <i class="bi bi-whatsapp"></i>
                    </button>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <small class="text-muted">{{ __('Type number with country code (e.g., 2010...)') }}</small>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.email') }}</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Status (Updated List) --}}
            <div class="mb-3">
                <label for="status" class="form-label">{{ __('clients.status') }}</label>
                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                    <option value="">{{ __('clients.select_status') }}</option>
                    <option value="potential" {{ old('status') == 'potential' ? 'selected' : '' }}>1 - Potential</option>
                    <option value="not_potential" {{ old('status') == 'not_potential' ? 'selected' : '' }}>2 - Not Potential</option>
                    <option value="hot_case" {{ old('status') == 'hot_case' ? 'selected' : '' }}>3 - Hot Case</option>
                    <option value="closed_deal" {{ old('status') == 'closed_deal' ? 'selected' : '' }}>4 - Closed Deal</option>
                    <option value="no_answer" {{ old('status') == 'no_answer' ? 'selected' : '' }}>5 - No Answer</option>
                    <option value="meeting_done" {{ old('status') == 'meeting_done' ? 'selected' : '' }}>6 - Meeting Done</option>
                </select>
                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Feedback / Note --}}
            <div class="mb-3">
                <label class="form-label">{{ __('clients.feedback') }}</label>
                <textarea class="form-control @error('feedback') is-invalid @enderror" name="feedback"
                    rows="3" placeholder="{{ __('Enter client feedback or notes here...') }}">{{ old('feedback') }}</textarea>
                @error('feedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- Company & Address (Optional details) --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('clients.company') }}</label>
                    <input type="text" class="form-control @error('company') is-invalid @enderror" name="company"
                        value="{{ old('company') }}">
                    @error('company') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('clients.source') }}</label>
                    <input type="text" class="form-control @error('source') is-invalid @enderror" name="source" 
                        value="{{ old('source') }}">
                    @error('source') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- Assignments --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('clients.assigned_user') }}</label>
                    <select class="form-select @error('assigned_to_user') is-invalid @enderror" name="assigned_to_user">
                        <option value="">{{ __('clients.select_user') }}</option>
                        @foreach($sales as $sale)
                            <option value="{{ $sale->id }}" {{ old('assigned_to_user') == $sale->id ? 'selected' : '' }}>
                                {{ $sale->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to_user') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                @if(auth()->guard('admin')->check())
                <div class="col-md-6 mb-3">
                    <label class="form-label">{{ __('clients.assigned_manager') }}</label>
                    <select class="form-select @error('assigned_to_manager') is-invalid @enderror" name="assigned_to_manager">
                        <option value="">{{ __('clients.select_manager') }}</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('assigned_to_manager') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to_manager') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @endif
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">{{ __('clients.create') }}</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">{{ __('clients.cancel') }}</a>
        </form>
    </div>

    {{-- WhatsApp Helper Script --}}
    <script>
        function openWhatsApp() {
            const phone = document.getElementById('phone').value;
            if (!phone) {
                alert("Please enter a phone number first.");
                return;
            }
            // Clean phone number (remove spaces, plus signs, dashes)
            const cleanPhone = phone.replace(/\D/g, '');
            window.open(`https://wa.me/${cleanPhone}`, '_blank');
        }
    </script>
@endsection