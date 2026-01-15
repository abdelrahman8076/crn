@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <x-flash-success />
    <x-flash-error />

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark tracking-tight mb-1">{{ __('users.title') }}</h3>
            <p class="text-muted small mb-0">{{ __('users.manage_description') }}</p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm d-flex align-items-center">
                <i class="ti ti-plus me-2 fs-5"></i> {{ __('users.create_title') }}
            </a>
        </div>
    </div>

    {{-- Datatable Card --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4 border-bottom bg-white">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                        <i class="ti ti-users text-primary fs-5"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">{{ __('users.user_list') }}</h5>
                </div>
            </div>
            
            <div class="px-2 pb-2">
                <x-datatable 
                    :ajaxUrl="route('admin.users.data')" 
                    :columns="$columns" 
                    :renderComponents="$renderComponents"
                    :customActionsView="$customActionsView" 
                />
            </div>
        </div>
    </div>
</div>
@endsection