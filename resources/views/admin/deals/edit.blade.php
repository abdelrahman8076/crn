@extends('layouts.admin')

@section('content')
    <div class="container">
        <x-flash-success />
        <x-flash-error />

        <h4>{{ isset($deal) ? __('deals.edit_title') : __('deals.create_title') }}</h4>

        <form action="{{ isset($deal) ? route('admin.deals.update', $deal->id) : route('admin.deals.store') }}"
            method="POST" class="mt-3">
            @csrf
            @if(isset($deal))
                @method('PUT')
            @endif

            {{-- Deal Name --}}
            <div class="mb-3">
                <label for="deal_name" class="form-label">{{ __('deals.deal_name') }} *</label>
                <input type="text" name="deal_name" id="deal_name"
                    class="form-control @error('deal_name') is-invalid @enderror"
                    value="{{ old('deal_name', $deal->deal_name ?? '') }}" required>
                @error('deal_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="mb-3">
                <label for="amount" class="form-label">{{ __('deals.amount') }} *</label>
                <input type="number" step="0.01" name="amount" id="amount"
                    class="form-control @error('amount') is-invalid @enderror"
                    value="{{ old('amount', $deal->amount ?? '') }}" required>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Stage --}}
            <div class="mb-3">
                <label for="stage" class="form-label">{{ __('deals.stage') }} *</label>
                <select name="stage" id="stage" class="form-select @error('stage') is-invalid @enderror" required>
                    <option value="proposal" {{ old('stage', $deal->stage ?? '') == 'proposal' ? 'selected' : '' }}>
                        {{ __('deals.proposal') }}</option>
                    <option value="negotiation" {{ old('stage', $deal->stage ?? '') == 'negotiation' ? 'selected' : '' }}>
                        {{ __('deals.negotiation') }}</option>
                    <option value="closed-won" {{ old('stage', $deal->stage ?? '') == 'closed-won' ? 'selected' : '' }}>
                        {{ __('deals.closed_won') }}</option>
                    <option value="closed-lost" {{ old('stage', $deal->stage ?? '') == 'closed-lost' ? 'selected' : '' }}>
                        {{ __('deals.closed_lost') }}</option>
                </select>
                @error('stage')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Lead --}}
            <div class="mb-3">
                <label for="client_id" class="form-label">
                    {{ __('deals.client') }} *
                </label>

                <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror"
                    required>
                    <option value="">
                        {{ __('deals.select_client') }}
                    </option>

                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ old('client_id', $deal->client_id ?? '') == $client->id ? 'selected' : '' }}>
                            {{ $client->title }}
                        </option>
                    @endforeach
                </select>

                @error('client_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            {{-- Assigned To --}}
            {{-- <div class="mb-3">
                <label for="assigned_to" class="form-label">{{ __('deals.assigned_to') }}</label>
                <select name="assigned_to" id="assigned_to" class="form-select @error('assigned_to') is-invalid @enderror">
                    <option value="">{{ __('deals.select_user') }}</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to', $deal->assigned_to ?? '') == $user->id ?
                        'selected' : '' }}>
                        {{ $user->name }} ({{ $user->role->name ?? '' }})
                    </option>
                    @endforeach
                </select>
                @error('assigned_to')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> --}}

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit"
                    class="btn btn-primary">{{ isset($deal) ? __('deals.update') : __('deals.save') }}</button>
                <a href="{{ route('admin.deals.index') }}" class="btn btn-secondary">{{ __('deals.cancel') }}</a>
            </div>
        </form>
    </div>
@endsection