@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}" class="text-decoration-none text-muted">{{ __('clients.index_title') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ __('clients.create_title') }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 border-top border-primary border-4">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-circle me-3">
                            <i class="ti ti-user-plus fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{ __('clients.create_title') }}</h4>
                            <p class="text-muted small mb-0">{{ __('clients.index_subtitle') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <x-flash-success />
                    <x-flash-error />

                    <form action="{{ route('admin.clients.store') }}" method="POST">
                        @csrf

                        <div class="row g-4">
                            {{-- Section 1: Contact Information --}}
                            <div class="col-12 mt-2">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-id me-1"></i> {{ __('clients.basic_information') ?? 'Basic Information' }}
                                </h6>
                            </div>

                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.name') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                        name="name" value="{{ old('name') }}" placeholder="Full Name" required>
                                </div>
                                @error('name') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.email') }}</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail"></i></span>
                                    <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                        name="email" value="{{ old('email') }}" placeholder="email@domain.com" 
                                        pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                                </div>
                                @error('email') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Phone & WhatsApp Integration --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.phone') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone"></i></span>
                                    <input type="tel" id="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" 
                                        name="phone" value="{{ old('phone') }}" placeholder="2010xxxxxxxx" 
                                        pattern="[0-9]+" inputmode="numeric" onkeypress="return isNumberKey(event)" required>
                                    <button class="btn btn-soft-success px-3 border" type="button" onclick="openWhatsApp()" data-bs-toggle="tooltip" title="Verify on WhatsApp">
                                        <i class="ti ti-brand-whatsapp fs-4"></i>
                                    </button>
                                </div>
                                <small class="text-muted extra-small">{{ __('Type number with country code (e.g., 2010...) - Numbers only') }}</small>
                                @error('phone') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                        {{-- Status Selection --}}
<div class="col-md-6">
    <label for="status" class="form-label fw-semibold small">{{ __('clients.status') }}</label>
    <div class="input-group">
        <span class="input-group-text bg-light border-end-0"><i class="ti ti-settings-automation"></i></span>
        <select class="form-select border-start-0 @error('status') is-invalid @enderror" id="status" name="status">
            {{-- Setting "New" as the default if old('status') is empty --}}
            <option value="new" {{ old('status', 'new') == 'new' ? 'selected' : '' }}>0 - New</option>
            
            <option value="potential" {{ old('status') == 'potential' ? 'selected' : '' }}>1 - Potential</option>
            <option value="not_potential" {{ old('status') == 'not_potential' ? 'selected' : '' }}>2 - Not Potential</option>
            <option value="hot_case" {{ old('status') == 'hot_case' ? 'selected' : '' }}>3 - Hot Case</option>
            <option value="no_answer" {{ old('status') == 'no_answer' ? 'selected' : '' }}>4 - No Answer</option>
            <option value="meeting_done" {{ old('status') == 'meeting_done' ? 'selected' : '' }}>5 - Meeting Done</option>
            <option value="call_again" {{ old('status') == 'call_again' ? 'selected' : '' }}>6 - Call Again</option>
        </select>
    </div>
    @error('status') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
</div>

                            {{-- Section 2: Business & Assignment --}}
                            <div class="col-12 mt-5">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-briefcase me-1"></i> {{ __('clients.business_details') ?? 'Business & Assignment' }}
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.company') }}</label>
                                <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                    name="company" value="{{ old('company') }}" placeholder="Company Name">
                            </div>

 <div class="col-md-6">
    <label class="form-label fw-semibold small">{{ __('clients.source') }}</label>
    
    {{-- This input behaves like a normal text box but shows a dropdown --}}
    <input type="text" 
           name="source" 
           list="sourceOptions" 
           class="form-control @error('source') is-invalid @enderror" 
           value="{{ old('source', $client->source ?? '') }}" 
           placeholder="{{ __('Select or type...') }}"
           autocomplete="off">

    {{-- These are the suggestions --}}
    <datalist id="sourceOptions">
        <option value="Facebook">
        <option value="Google">
        <option value="Website">
        <option value="Referral">
    </datalist>

    @error('source')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.assigned_user') }}</label>
                                <select class="form-select @error('assigned_to_sale') is-invalid @enderror" name="assigned_to_user">
                                    <option value="">{{ __('clients.select_sale') }}</option>
                                    @foreach($sales as $sale)
                                        <option value="{{ $sale->id }}" {{ old('assigned_to_sale') == $sale->id ? 'selected' : '' }}>
                                            {{ $sale->name }} ({{ $sale->role->name ?? 'Sales' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            @if(auth()->guard('admin')->check())
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.assigned_manager') }}</label>
                                <select class="form-select @error('assigned_to_manager') is-invalid @enderror" name="assigned_to_manager">
                                    <option value="">{{ __('clients.select_manager') }}</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('assigned_to_manager') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Feedback / Note --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold small">{{ __('clients.feedback') }}</label>
                                <textarea class="form-control @error('feedback') is-invalid @enderror" name="feedback"
                                    rows="4" placeholder="{{ __('Enter client feedback or internal notes here...') }}">{{ old('feedback') }}</textarea>
                                @error('feedback') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Form Footer --}}
                        <div class="mt-5 pt-4 border-top d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-light px-4 text-muted">
                                {{ __('clients.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                                <i class="ti ti-device-floppy me-1"></i> {{ __('clients.create') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* NexusCRM Custom Styling */
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08); }
    .btn-soft-success { 
        background-color: rgba(25, 135, 84, 0.08); 
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.1) !important;
    }
    .btn-soft-success:hover { 
        background-color: #198754; 
        color: white !important; 
    }
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }
    .form-label { color: #4b5563; }
    .form-control, .form-select {
        border-color: #e5e7eb;
        padding: 0.6rem 0.8rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }
    .input-group-text {
        border-color: #e5e7eb;
        color: #9ca3af;
    }
    .extra-small { font-size: 0.75rem; }
    .card { border-radius: 12px; }
</style>



<script>
    /**
     * WhatsApp Deep Linking Helper
     */
    function openWhatsApp() {
        const phoneField = document.getElementById('phone');
        const phone = phoneField.value;
        
        if (!phone) {
            phoneField.classList.add('is-invalid');
            alert("Please enter a phone number first.");
            return;
        }
        
        // Remove all non-numeric characters
        const cleanPhone = phone.replace(/\D/g, '');
        
        // Open WhatsApp Web/API in new tab
        window.open(`https://wa.me/${cleanPhone}`, '_blank');
    }

    // Restrict phone input to numbers only
    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    // Prevent paste of non-numeric characters in phone field
    document.addEventListener('DOMContentLoaded', function () {
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const numbersOnly = paste.replace(/\D/g, '');
                phoneInput.value = numbersOnly;
            });
            
            phoneInput.addEventListener('input', function(e) {
                // Remove any non-numeric characters
                this.value = this.value.replace(/\D/g, '');
            });
        }

        // Initialize Tooltips if using Bootstrap 5
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>

@endsection