@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('clients.import_title') }}</h4>

    <form action="{{ route('admin.clients.upload') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf

        {{-- File input --}}
        <div class="mb-3">
            <label for="file" class="form-label">{{ __('clients.choose_file') }} *</label>
            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
            @error('file')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">{{ __('clients.accepted_formats') }}</div>
        </div>

        {{-- Buttons --}}
        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn btn-success">{{ __('clients.upload') }}</button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">{{ __('clients.cancel') }}</a>
        </div>
    </form>

    {{-- Tip --}}
    <div class="mt-3">
        <p class="text-muted">{{ __('clients.tip_columns') }}</p>
    </div>
</div>
@endsection
