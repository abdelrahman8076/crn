@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">{{ __('admins.dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}" class="text-decoration-none text-muted">{{ __('clients.index_title') }}</a></li>
            <li class="breadcrumb-item active fw-bold text-primary" aria-current="page">{{ __('clients.import_title') }}</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 border-top border-success border-4">
                {{-- Card Header --}}
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="bg-soft-success p-3 rounded-circle me-3">
                                <i class="ti ti-file-upload fs-2 text-success"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">{{ __('clients.import_title') }}</h4>
                                <p class="text-muted small mb-0">{{ __('clients.import_subtitle') ?? 'Bulk upload clients via CSV or Excel files.' }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.clients.template') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="ti ti-download me-1"></i> {{ __('clients.download_template') ?? 'Sample Template' }}
                        </a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <x-flash-success />
                    <x-flash-error />

                    <form action="{{ route('admin.clients.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Upload Zone --}}
                        <div class="upload-zone p-5 mb-4 border border-2 border-dashed rounded-3 text-center bg-light transition-all" id="drop-area">
                            <i class="ti ti-cloud-upload fs-1 text-muted mb-3 d-block"></i>
                            <label for="file" class="form-label fw-bold">{{ __('clients.choose_file') }} *</label>
                            <input type="file" class="form-control mx-auto @error('file') is-invalid @enderror" 
                                id="file" name="file" required style="max-width: 400px;">
                            <div class="form-text mt-2">{{ __('clients.accepted_formats') }} (CSV, XLSX, XLS)</div>
                            @error('file') <div class="text-danger extra-small mt-2 fw-bold">{{ $message }}</div> @enderror
                        </div>

                        {{-- Column Guide --}}
                        <div class="alert alert-info border-0 bg-soft-info text-dark rounded-3 p-3">
                            <h6 class="fw-bold"><i class="ti ti-info-circle me-2"></i>{{ __('clients.tip_columns_title') ?? 'Import Guidelines:' }}</h6>
                            <p class="small mb-2">{{ __('clients.tip_columns') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-white text-primary border border-primary">name</span>
                                <span class="badge bg-white text-primary border border-primary">phone</span>
                                <span class="badge bg-white text-primary border border-primary">email</span>
                                <span class="badge bg-white text-primary border border-primary">company</span>
                                <span class="badge bg-white text-primary border border-primary">status</span>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="mt-5 pt-3 border-top d-flex justify-content-end align-items-center gap-3">
                            <a href="{{ route('admin.clients.index') }}" class="btn btn-light px-4 text-muted">
                                {{ __('clients.cancel') }}
                            </a>
                            <button type="submit" class="btn btn-success px-5 py-2 shadow-sm fw-bold">
                                <i class="ti ti-check me-1"></i> {{ __('clients.upload') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* NexusCRM Import Styling */
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.08); }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); }
    .border-dashed { border-style: dashed !important; border-color: #dee2e6 !important; }
    .upload-zone { transition: all 0.3s ease; }
    .upload-zone:hover { background-color: #f1f4f9 !important; border-color: #0d6efd !important; }
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; }
    .extra-small { font-size: 0.75rem; }
    .card { border-radius: 12px; }
    .transition-all { transition: all 0.2s ease-in-out; }
</style>
@endsection