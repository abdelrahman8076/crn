@extends('layouts.admin')

@section('content')
<div class="container">
    <x-flash-success />
    <x-flash-error />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('deals.index_title') }}</h4>

        <a href="{{ route('admin.deals.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> {{ __('deals.create_title') }}
        </a>
    </div>

    <x-datatable
        :ajaxUrl="route('admin.deals.data')"
        :columns="$columns"
        :renderComponents="$renderComponents"
        :customActionsView="$customActionsView"
    />
</div>
@endsection
