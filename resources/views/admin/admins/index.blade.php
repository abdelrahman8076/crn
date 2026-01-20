@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1 tracking-tight">{{ __('admins.title') }}</h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">{{ __('admins.title') }}</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @permission('create-admins')
            <a href="{{ route('admin.admin.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center gap-2">
                <i class="ti ti-plus fs-5"></i> 
                <span class="fw-bold">{{ __('admins.create_button') }}</span>
            </a>
            @endpermission
        </div>
    </div>

    <div class="nexus-alerts">
        <x-flash-success />
        <x-flash-error />
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom border-light d-flex align-items-center gap-2">
            <div class="bg-primary bg-opacity-10 p-2 rounded-2">
                <i class="ti ti-shield-lock text-primary fs-5"></i>
            </div>
            <h5 class="card-title mb-0 fw-bold">System Administrators</h5>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive nexus-datatable-container">
                <x-datatable 
                    :ajaxUrl="route('admin.admin.data')" 
                    :columns="$columns" 
                    :renderComponents="$renderComponents"
                    :customActionsView="$customActionsView" 
                />
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling to match the NexusCRM Aesthetic */
    .tracking-tight { letter-spacing: -0.5px; }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "•";
        color: #cbd5e1;
    }

    /* Target the generated datatable wrapper */
    .nexus-datatable-container .dataTables_wrapper {
        padding: 1.5rem;
    }

    /* Style the table headers inside the component */
    .nexus-datatable-container table.dataTable thead th {
        background-color: #f8fafc !important;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748b;
        border-top: none;
        padding: 12px 20px;
    }

    [data-bs-theme="dark"] .nexus-datatable-container table.dataTable thead th {
        background-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    /* Row hover effect */
    .nexus-datatable-container table.dataTable tbody tr:hover {
        background-color: #fcfdfe !important;
    }

    [data-bs-theme="dark"] .nexus-datatable-container table.dataTable tbody tr:hover {
        background-color: #334155 !important;
    }

    /* Style DataTables Search & Pagination to match Nexus Blue */
    .dataTables_filter input {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 12px !important;
    }

    [data-bs-theme="dark"] .dataTables_filter input {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #e2e8f0 !important;
    }

    .page-item.active .page-link {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
        border-radius: 6px;
    }

    [data-bs-theme="dark"] .page-item.active .page-link {
        background-color: #6366f1 !important;
        border-color: #6366f1 !important;
        color: #fff !important;
    }

    [data-bs-theme="dark"] .page-link {
        color: #e2e8f0 !important;
        background-color: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .page-link:hover {
        color: #e2e8f0 !important;
        background-color: #334155 !important;
        border-color: #475569 !important;
    }
</style>
@endsection