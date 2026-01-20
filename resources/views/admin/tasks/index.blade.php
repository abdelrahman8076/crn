@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ __('tasks.title') }}</li>
        </ol>
    </nav>

    {{-- Quick Stats & Task Overview --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-2 rounded-3 me-3">
                            <i class="ti ti-list-check fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small opacity-75">{{ __('tasks.total_tasks') ?? 'Total Tasks' }}</p>
                            <h4 class="mb-0 fw-bold">{{ $totalTasksCount ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center text-warning">
                        <div class="bg-soft-warning p-2 rounded-3 me-3">
                            <i class="ti ti-clock fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">{{ __('tasks.pending') ?? 'Pending' }}</p>
                            <h4 class="mb-0 fw-bold">{{ $pendingTasksCount ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center text-success">
                        <div class="bg-soft-success p-2 rounded-3 me-3">
                            <i class="ti ti-circle-check fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">{{ __('tasks.completed') ?? 'Completed' }}</p>
                            <h4 class="mb-0 fw-bold">{{ $completedTasksCount ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center text-danger">
                        <div class="bg-soft-danger p-2 rounded-3 me-3">
                            <i class="ti ti-calendar-event fs-3"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">{{ __('tasks.overdue') ?? 'Overdue' }}</p>
                            <h4 class="mb-0 fw-bold">{{ $overdueTasksCount ?? '0' }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-3">
        {{-- Card Header --}}
        <div class="card-header bg-white py-3 border-bottom-0">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="bg-soft-primary p-2 rounded-circle me-3">
                        <i class="ti ti-clipboard-text fs-4 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-dark">{{ __('tasks.title') }}</h5>
                        <p class="text-muted extra-small mb-0">{{ __('tasks.subtitle') ?? 'Manage daily operations and assignments' }}</p>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    @permission('create-tasks')
                        <a href="{{ route('admin.tasks.create') }}" class="btn btn-primary px-4 py-2 shadow-sm d-flex align-items-center rounded-pill">
                            <i class="ti ti-plus me-1 fs-5"></i> 
                            <span class="fw-bold small">{{ __('tasks.create_button') }}</span>
                        </a>
                    @endpermission
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
                    :ajaxUrl="route('admin.tasks.data')" 
                    :columns="$columns" 
                    :renderComponents="$renderComponents"
                    :customActionsView="$customActionsView" 
                />
            </div>
        </div>
    </div>
</div>

<style>
    /* NexusCRM Task Styling */
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.08); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08); }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.08); }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.08); }
    
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; }
    .extra-small { font-size: 0.75rem; }

    /* Modern Table Feel */
    .table thead th {
        background-color: #f9fafb;
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #edf2f7;
    }
    
    [data-bs-theme="dark"] .table thead th {
        background-color: #334155 !important;
        color: #e2e8f0 !important;
        border-bottom-color: #475569 !important;
    }
</style>
@endsection