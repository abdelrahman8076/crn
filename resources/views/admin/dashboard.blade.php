@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <x-flash-success />
    <x-flash-error />

    <div class="row g-4">

        {{-- Users Card --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">{{ ('Users') }}</h6>
                        <h3 class="mb-0">{{ $data['totalUsers'] ?? 0 }}</h3>
                    </div>
                    <i class="ti ti-users fs-2 text-primary"></i>
                </div>
            </div>
        </div>

        {{-- Clients Card --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">{{ ('Clients') }}</h6>
                        <h3 class="mb-0">{{ $data['totalClients'] ?? 0 }}</h3>
                    </div>
                    <i class="ti ti-building fs-2 text-success"></i>
                </div>
            </div>
        </div>

        {{-- Leads Card --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">{{ ('Leads') }}</h6>
                        <h3 class="mb-0">{{ $data['totalLeads'] ?? 0 }}</h3>
                    </div>
                    <i class="ti ti-target fs-2 text-warning"></i>
                </div>
            </div>
        </div>

        {{-- Deals Card --}}
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase text-muted mb-2">{{ ('Deals') }}</h6>
                        <h3 class="mb-0">{{ $data['totalDeals'] ?? 0 }}</h3>
                    </div>
                    <i class="ti ti-handshake fs-2 text-danger"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- Optional: Add charts or activity feed below --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header">
                    <h5 class="mb-0">{{ ('Recent Activities') }}</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">{{ ('You can show recent leads, deals, or client activities here.') }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
