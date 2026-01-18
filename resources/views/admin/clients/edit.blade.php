@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}" class="text-decoration-none text-muted">{{ __('clients.index_title') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ __('clients.edit_title') }}</li>
        </ol>
    </nav>

    @php
        // Check if the user is a Sales person (web guard)
        $isSales = auth()->guard('web')->check() && auth()->guard('web')->user()?->role?->name === 'Sales';
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 border-top border-primary border-4">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-primary p-3 rounded-circle me-3">
                                <i class="ti ti-edit fs-2 text-primary"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">{{ __('clients.edit_title') }}</h4>
                                <p class="text-muted small mb-0">{{ $client->name }} (ID: #{{ $client->id }})</p>
                            </div>
                        </div>
                        <span class="badge bg-soft-info text-info px-3 py-2 rounded-pill">
                            <i class="ti ti-history me-1"></i> {{ __('Updated:') }} {{ $client->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="card-body p-4">
                    <x-flash-success />
                    <x-flash-error />

                    <form action="{{ route('admin.clients.update', $client->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Section 1: Contact Information --}}
                            <div class="col-12 mt-2">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-id me-1"></i> {{ __('clients.basic_information') }}
                                </h6>
                            </div>

                            {{-- Name --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.name') }} <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('name') is-invalid @enderror" 
                                        name="name" value="{{ old('name', $client->name) }}" required {{ $isSales ? 'readonly' : '' }}>
                                </div>
                                @error('name') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.email') }}</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-mail"></i></span>
                                    <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                        name="email" value="{{ old('email', $client->email) }}" {{ $isSales ? 'readonly' : '' }}>
                                </div>
                                @error('email') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Phone & WhatsApp Integration --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.phone') }}</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-phone"></i></span>
                                    <input type="text" id="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" 
                                        name="phone" value="{{ old('phone', $client->phone) }}" {{ $isSales ? 'readonly' : '' }}>
                                    <button class="btn btn-soft-success px-3 border" type="button" onclick="openWhatsApp()" data-bs-toggle="tooltip" title="Message on WhatsApp">
                                        <i class="ti ti-brand-whatsapp fs-4"></i>
                                    </button>
                                </div>
                                @error('phone') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Status Selection - ALWAYS EDITABLE --}}
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-semibold small">{{ __('clients.status') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-settings-automation"></i></span>
                                    <select class="form-select border-start-0 @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="">{{ __('clients.select_status') }}</option>
                                        @php
                                            $statuses = [
                                                'new'           => '0 - New',
                                                'potential'     => '1 - Potential',
                                                'not_potential' => '2 - Not Potential',
                                                'hot_case'      => '3 - Hot Case',
                                                'no_answer'     => '4 - No Answer',
                                                'meeting_done'  => '5 - Meeting Done',
                                                'call_again'     => '6 - Call Again',
                                            ];
                                        @endphp
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ old('status', $client->getRawOriginal('status')) === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('status') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Section 2: Business & Assignment --}}
                            <div class="col-12 mt-5">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-briefcase me-1"></i> {{ __('clients.business_details') }}
                                </h6>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.company') }}</label>
                                <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                    name="company" value="{{ old('company', $client->company) }}" {{ $isSales ? 'readonly' : '' }}>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.source') }}</label>
                                <input type="text" 
                                       name="source" 
                                       list="sourceOptions" 
                                       class="form-control @error('source') is-invalid @enderror" 
                                       value="{{ old('source', $client->source ?? '') }}" 
                                       placeholder="{{ __('Select or type...') }}"
                                       autocomplete="off"
                                       {{ $isSales ? 'readonly' : '' }}>
                                <datalist id="sourceOptions">
                                    <option value="Facebook">
                                    <option value="Google">
                                    <option value="Website">
                                    <option value="Referral">
                                </datalist>
                            </div>

                            {{-- Hide Assigned Sales Rep if user is Sales --}}
                            @if(!$isSales)
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.assigned_user') }}</label>
                                <select class="form-select @error('assigned_to_sale') is-invalid @enderror" name="assigned_to_user">
                                    <option value="">{{ __('clients.select_sale') }}</option>
                                    @foreach($sales as $sale)
                                        <option value="{{ $sale->id }}" {{ old('assigned_to_sale', $client->assigned_to_sale) == $sale->id ? 'selected' : '' }}>
                                            {{ $sale->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Only show Manager assignment to Admin guard --}}
                            @if(auth()->guard('admin')->check())
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">{{ __('clients.assigned_manager') }}</label>
                                <select class="form-select @error('assigned_to_manager') is-invalid @enderror" name="assigned_to_manager">
                                    <option value="">{{ __('clients.select_manager') }}</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('assigned_to_manager', $client->assigned_to_manager) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            {{-- Feedback - ALWAYS EDITABLE --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold small">{{ __('clients.feedback') }}</label>
                                <textarea class="form-control @error('feedback') is-invalid @enderror" name="feedback"
                                    rows="4">{{ old('feedback', $client->feedback) }}</textarea>
                            </div>
                        </div>

                        {{-- Form Footer --}}
                        <div class="mt-5 pt-4 border-top d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-light px-4 text-muted">
                                {{ __('clients.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                                <i class="ti ti-refresh me-1"></i> {{ __('clients.update') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .text-info { color: #0dcaf0 !important; }
    .btn-soft-success { 
        background-color: rgba(25, 135, 84, 0.08); 
        color: #198754;
        border: 1px solid rgba(25, 135, 84, 0.1) !important;
    }
    .btn-soft-success:hover { background-color: #198754; color: white !important; }
    .form-control, .form-select { border-color: #e5e7eb; padding: 0.6rem 0.8rem; }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
    .form-control[readonly] { background-color: #f8f9fa; opacity: 0.8; cursor: not-allowed; }
    .extra-small { font-size: 0.75rem; }
    .card { border-radius: 12px; }
</style>

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

    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el) });
    });
</script>
@endsection