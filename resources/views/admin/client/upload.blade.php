@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <h4>{{ __('Import Clients from Excel') }}</h4>

    <form action="{{ route('admin.clients.upload') }}" method="POST" enctype="multipart/form-data" class="mt-3">
        @csrf

        <div class="mb-3">
            <label for="file" class="form-label">{{ __('Choose Excel File') }} *</label>
            <input type="file" class="form-control" id="file" name="file" required>
            <div class="form-text">{{ __('Accepted formats: .xlsx, .xls, .csv') }}</div>
        </div>

        <button type="submit" class="btn btn-success">{{ __('Upload') }}</button>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
    </form>

    <div class="mt-3">
        <p class="text-muted">{{ __('Tip: Make sure the Excel columns match: Name, Email, Phone, Assigned To') }}</p>
    </div>
</div>
@endsection
