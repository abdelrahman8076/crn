@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <x-flash-success />
    <x-flash-error />

    <div class="row g-4">

@auth('admin')
    {{-- Users Card --}}
    <div class="col-12 col-sm-6 col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-uppercase text-muted mb-2">
                        {{ __('admins.users') }}
                    </h6>
                    <h3 class="mb-0">
                        {{ $data['totalUsers'] ?? 0 }}
                    </h3>
                </div>
                <i class="ti ti-users fs-2 text-primary"></i>
            </div>
        </div>
    </div>
@endauth


        {{-- Clients Card --}}
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">{{ __('admins.clients') }}</h6>
                        <h3 class="mb-0">{{ $data['totalClients'] ?? 0 }}</h3>
                    </div>
                    <i class="ti ti-building fs-2 text-success"></i>
                </div>
            </div>
        </div>



        {{-- Deals Card --}}
        <div class="col-12 col-sm-6 col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">{{ __('admins.deals') }}</h6>
                        <h3 class="mb-0">{{ $data['totalDeals'] ?? 0 }}</h3>
                    </div>
                    <i class="ti ti-handshake fs-2 text-danger"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Recent Activities --}}
    {{-- <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('admins.recent_activities') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ __('admins.recent_activities_desc') }}</p>
                </div>
            </div>
        </div>
    </div> --}}

</div>
@endsection
