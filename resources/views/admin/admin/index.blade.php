@extends('layouts.admin')

@section('content')
    <div class="container">
        <x-flash-success />
        <x-flash-error />

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>{{ __('admins.title') }}</h4>

            <a href="{{ route('admin.admin.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> {{ __('admins.create_button') }}
            </a>
        </div>

        <x-datatable :ajaxUrl="route('admin.admin.data')" :columns="$columns" :renderComponents="$renderComponents"
            :customActionsView="$customActionsView" />
    </div>
@endsection