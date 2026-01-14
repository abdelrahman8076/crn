@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ __('deals.index_title') }}</li>
        </ol>
    </nav>

    {{-- Stats Overview Bar --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3">
                            <i class="ti ti-briefcase fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small opacity-75">{{ __('deals.total_deals') ?? 'Total Deals' }}</p>
                            <h4 class="mb-0 fw-bold">{{ $totalDealsCount ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-success p-2 rounded-3 me-3 text-success">
                            <i class="ti ti-currency-dollar fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">{{ __('deals.closed_deals') ?? 'Closed Value' }}</p>
                            <h4 class="mb-0 fw-bold text-success">{{ $closedDealsValue ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-soft-warning p-2 rounded-3 me-3 text-warning">
                            <i class="ti ti-clock-play fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">{{ __('deals.pending_deals') ?? 'Active Pipeline' }}</p>
                            <h4 class="mb-0 fw-bold text-warning">{{ $pendingDealsCount ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        {{-- Card Header --}}
        <div class="card-header bg-white py-3 border-bottom-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-soft-primary p-2 rounded-circle me-3">
                        <i class="ti ti-list-details fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">{{ __('deals.index_title') }}</h5>
                        <p class="text-muted extra-small mb-0">{{ __('deals.index_subtitle') ?? 'Track and manage your sales pipeline' }}</p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.deals.create') }}" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center">
                        <i class="ti ti-plus me-1 fs-5"></i> 
                        <span class="fw-bold small">{{ __('deals.create_title') }}</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="px-4">
                <x-flash-success />
                <x-flash-error />
            </div>

            {{-- DataTable Wrapper --}}
            <div class="table-responsive p-4 pt-0">
                <x-datatable
                    :ajaxUrl="route('admin.deals.data')"
                    :columns="$columns"
                    :renderComponents="$renderComponents"
                    :customActionsView="$customActionsView"
                />
            </div>
        </div>
    </div>
</div>

<style>
    /* NexusCRM Deal Styles */
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08); }
    
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; }
    
    .extra-small { font-size: 0.75rem; }
    
    /* Styling the Datatable inside Nexus Card */
    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        padding: 0.4rem 0.6rem;
    }
    
    .table thead th {
        background-color: #f9fafb;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.025em;
        color: #6b7280;
        font-weight: 700;
        border-bottom-width: 1px;
    }
</style>
@endsection