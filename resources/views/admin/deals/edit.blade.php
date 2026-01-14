@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.deals.index') }}" class="text-decoration-none text-muted">{{ __('deals.index_title') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">
                {{ isset($deal) ? __('deals.edit_title') : __('deals.create_title') }}
            </li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0 border-top border-primary border-4">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-primary p-3 rounded-circle me-3">
                            <i class="ti ti-{{ isset($deal) ? 'edit' : 'plus' }} fs-2 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold text-dark">{{ isset($deal) ? __('deals.edit_title') : __('deals.create_title') }}</h4>
                            <p class="text-muted small mb-0">{{ __('deals.form_subtitle') ?? 'Define deal parameters and track sales progress.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <x-flash-success />
                    <x-flash-error />

                    <form action="{{ isset($deal) ? route('admin.deals.update', $deal->id) : route('admin.deals.store') }}" method="POST">
                        @csrf
                        @if(isset($deal)) @method('PUT') @endif

                        <div class="row g-4">
                            {{-- Section: Deal Details --}}
                            <div class="col-12 mt-2">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-info-circle me-1"></i> {{ __('deals.deal_details') ?? 'Deal Information' }}
                                </h6>
                            </div>

                            {{-- Deal Name --}}
                            <div class="col-md-8">
                                <label for="deal_name" class="form-label fw-semibold small">{{ __('deals.deal_name') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-hash"></i></span>
                                    <input type="text" name="deal_name" id="deal_name" 
                                        class="form-control border-start-0 @error('deal_name') is-invalid @enderror" 
                                        value="{{ old('deal_name', $deal->deal_name ?? '') }}" required placeholder="e.g. Website Redesign Project">
                                </div>
                                @error('deal_name') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Amount --}}
                            <div class="col-md-4">
                                <label for="amount" class="form-label fw-semibold small">{{ __('deals.amount') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-coin"></i></span>
                                    <input type="number" step="0.01" name="amount" id="amount" 
                                        class="form-control border-start-0 @error('amount') is-invalid @enderror" 
                                        value="{{ old('amount', $deal->amount ?? '') }}" required placeholder="0.00">
                                </div>
                                @error('amount') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Section: Relationship & Status --}}
                            <div class="col-12 mt-5">
                                <h6 class="fw-bold text-uppercase small text-primary mb-3 border-bottom pb-2">
                                    <i class="ti ti-settings-automation me-1"></i> {{ __('deals.pipeline_status') ?? 'Pipeline & Relationship' }}
                                </h6>
                            </div>

                            {{-- Client Selection --}}
                            <div class="col-md-6">
                                <label for="client_id" class="form-label fw-semibold small">{{ __('deals.client') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-user"></i></span>
                                    <select name="client_id" id="client_id" class="form-select border-start-0 @error('client_id') is-invalid @enderror" required>
                                        <option value="">{{ __('deals.select_client') }}</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $deal->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                                {{ $client->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('client_id') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>

                            {{-- Stage Selection --}}
                            <div class="col-md-6">
                                <label for="stage" class="form-label fw-semibold small">{{ __('deals.stage') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-adjustments-horizontal"></i></span>
                                    <select name="stage" id="stage" class="form-select border-start-0 @error('stage') is-invalid @enderror" required>
                                        <option value="proposal" {{ old('stage', $deal->stage ?? '') == 'proposal' ? 'selected' : '' }}>{{ __('deals.proposal') }}</option>
                                        <option value="negotiation" {{ old('stage', $deal->stage ?? '') == 'negotiation' ? 'selected' : '' }}>{{ __('deals.negotiation') }}</option>
                                        <option value="closed-won" {{ old('stage', $deal->stage ?? '') == 'closed-won' ? 'selected' : '' }}>{{ __('deals.closed_won') }}</option>
                                        <option value="closed-lost" {{ old('stage', $deal->stage ?? '') == 'closed-lost' ? 'selected' : '' }}>{{ __('deals.closed_lost') }}</option>
                                    </select>
                                </div>
                                @error('stage') <div class="text-danger extra-small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Form Footer Buttons --}}
                        <div class="mt-5 pt-4 border-top d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('admin.deals.index') }}" class="btn btn-light px-4 text-muted">
                                {{ __('deals.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm fw-bold">
                                <i class="ti ti-device-floppy me-1"></i> 
                                {{ isset($deal) ? __('deals.update') : __('deals.save') }}
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
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); }
    .extra-small { font-size: 0.75rem; }
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; }
</style>
@endsection