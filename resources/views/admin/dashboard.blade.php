@extends('layouts.admin')

@section('content')
<div class="page-wrapper p-4" style="background-color: #f4f7fa; min-height: 100vh;">
    <x-flash-success />
    <x-flash-error />

    <div class="mb-4">
        <h3 class="fw-bold text-dark tracking-tight mb-1">Nexus Dashboard</h3>
        <p class="text-muted small">Welcome back, <span class="fw-semibold text-primary">{{ Auth::user()->name ?? 'Admin' }}</span></p>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        @if($isSuperAdmin)
        <div class="col-md-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold">{{ __('admins.users') }}</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $data['totalUsers'] }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="ti ti-users fs-2 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div class="col-md-{{ $isSuperAdmin ? '3' : '6' }}">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold">{{ __('admins.clients') }}</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $data['totalClients'] }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="ti ti-building fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-{{ $isSuperAdmin ? '3' : '6' }}">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted small text-uppercase fw-bold">{{ __('admins.deals') }}</h6>
                        <h3 class="fw-bold mb-0 text-dark">{{ $data['totalDeals'] }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3">
                        <i class="ti ti-coin fs-2 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Monitor --}}
  @if($isSuperAdmin || (optional(Auth::user()->role)->name === 'Manager'))
<div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
        <div class="bg-primary bg-opacity-10 p-2 rounded-2">
            <i class="ti ti-chart-bar text-primary fs-5"></i>
        </div>
        <h5 class="mb-0 fw-bold">{{ __('admins.team_performance_monitor') }}</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-uppercase text-muted fw-bold">
                        <th class="ps-4">{{ __('admins.member_name') }}</th>
                        <th>{{ __('admins.target_total') }}</th>
                        <th>{{ __('admins.reached') }}</th>
                        <th style="width: 200px;">{{ __('admins.progress') }}</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $loopData = $isSuperAdmin ? $managersData : ($managerViewData['team'] ?? []); @endphp

                    @foreach($loopData as $m)
                        {{-- Main Member Row --}}
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 35px; height: 35px;">
                                        {{ substr($m['manager_name'] ?? $m['user_name'] ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $m['manager_name'] ?? $m['user_name'] ?? '-' }}</div>
                                        <small class="text-muted">{{ $m['role'] ?? 'Member' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="fw-semibold text-dark">${{ number_format($m['target_total']) }}</td>
                            <td class="text-primary fw-bold">${{ number_format($m['reached']) }}</td>
                            <td>
                                <div class="progress rounded-pill" style="height: 6px;">
                                    <div class="progress-bar {{ $m['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ min($m['progress'], 100) }}%"></div>
                                </div>
                                <small class="fw-bold text-dark mt-1 d-block">{{ $m['progress'] }}%</small>
                            </td>
                            <td class="pe-4 text-end">
                                @if($isSuperAdmin && isset($m['id']))
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#manager-team-{{ $m['id'] }}" 
                                            aria-expanded="false">
                                        <i class="ti ti-users me-1"></i> {{ __('admins.view_team') }}
                                    </button>
                                @endif
                            </td>
                        </tr>

                        {{-- Collapsible Team Details Row --}}
                        @if($isSuperAdmin && isset($m['id']))
                        <tr class="collapse" id="manager-team-{{ $m['id'] }}">
                            <td colspan="5" class="bg-light p-0">
                                <div class="p-4 border-start border-primary border-4 ms-4 my-2 shadow-inner">
                                    <h6 class="fw-bold mb-3 text-primary">
                                        <i class="ti ti-arrow-forward-up me-2"></i>Team Members for {{ $m['manager_name'] ?? 'Manager' }}
                                    </h6>
                                    
                         @if(!empty($m['team']))
                <div class="row g-3">
                    @foreach($m['team'] as $member)
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-3">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="fw-bold text-dark small">{{ $member['user_name'] }}</div>
                                            <small class="text-muted" style="font-size: 10px;">{{ $member['role'] ?? 'Member' }}</small>
                                        </div>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">
                                            {{ $member['progress'] }}%
                                        </span>
                                    </div>
                                    
                                    {{-- Mini Progress Bar --}}
                                    <div class="progress mb-2" style="height: 4px;">
                                        <div class="progress-bar {{ $member['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                             role="progressbar" 
                                             style="width: {{ min($member['progress'], 100) }}%"></div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <div class="text-start">
                                            <small class="text-muted d-block" style="font-size: 9px;">TARGET</small>
                                            <span class="fw-bold small">${{ number_format($member['target_total']) }}</span>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted d-block" style="font-size: 9px;">REACHED</small>
                                            <span class="fw-bold text-primary small">${{ number_format($member['reached']) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-muted small italic p-3 bg-white rounded border border-dashed text-center">
<i class="ti ti-info-circle me-1"></i> {{ __('admins.no_team_data') }}                </div>
            @endif
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

    {{-- Individual Target Section --}}
    @auth('web')
    @php $myTarget = $data['allTargets']->where('user_name', Auth::user()->name)->first(); @endphp
    @if($myTarget && $myTarget['target_total'] > 0)
    <div class="card shadow-sm border-0 rounded-4 mb-4 overflow-hidden">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="ti ti-target me-2 text-danger"></i>{{ __('users.my_target') }}</h5>
            {{-- Fixed Button: Uses both ID for JS and Data attributes for standard Bootstrap --}}
            <button type="button" id="openHistory" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#historyModal">
                <i class="ti ti-history me-1"></i>{{ __('users.view_history') }}
            </button>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div style="max-width: 180px; margin: auto; position: relative;">
                        <canvas id="targetDoughnut"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                            <h4 class="fw-bold mb-0 text-dark">{{ round($myTarget['progress']) }}%</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <h4 class="fw-bold text-dark mb-3">{{ $myTarget['period'] }}</h4>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="p-3 bg-light rounded-4 border">
                                <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">{{ __('users.total_goal') }}</small>
                                <span class="fs-5 fw-bold text-dark">${{ number_format($myTarget['target_total']) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-primary bg-opacity-10 rounded-4 border border-primary border-opacity-25">
                                <small class="text-primary d-block text-uppercase fw-bold" style="font-size: 10px;">{{ __('users.reached') }}</small>
                                <span class="fs-5 fw-bold text-primary">${{ number_format($myTarget['reached']) }}</span>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="p-3 bg-danger bg-opacity-10 rounded-4 border border-danger border-opacity-25">
                                <small class="text-danger d-block text-uppercase fw-bold" style="font-size: 10px;">{{ __('users.remaining') }}</small>
                                <span class="fs-5 fw-bold text-danger">${{ number_format($myTarget['remaining']) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endauth
</div>

{{-- Modal Section: Placed outside wrapper but within content section --}}
<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bold" id="historyModalLabel">
                    <i class="ti ti-history me-2 text-primary"></i>{{ __('users.target_history') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small text-uppercase fw-bold text-muted">
                            <tr>
                                <th class="ps-4">{{ __('users.period') }}</th>
                                <th>{{ __('users.target_total') }}</th>
                                <th class="pe-4">{{ __('users.status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php 
                                // FIX: Check if user exists and has the targets relation (prevents 500 error for Admins)
                                $targets = (Auth::user() && method_exists(Auth::user(), 'targets')) ? Auth::user()->targets()->orderBy('period', 'desc')->get() : collect(); 
                            @endphp
                            @forelse($targets as $h)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $h->period }}</td>
                                <td class="fw-semibold text-dark">${{ number_format($h->target_total) }}</td>
                                <td class="pe-4">
                                    <span class="badge rounded-pill {{ $h->is_active ? 'bg-primary' : 'bg-light text-muted' }}">
                                        {{ $h->is_active ? 'Active' : 'Past' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-4 text-muted">No history found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-top p-3">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Initialize Chart
        const ctx = document.getElementById('targetDoughnut');
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Reached', 'Remaining'],
                    datasets: [{
                        data: [{{ $myTarget['reached'] ?? 0 }}, {{ $myTarget['remaining'] ?? 0 }}],
                        backgroundColor: ['#6366f1', '#f1f5f9'],
                        hoverOffset: 0,
                        borderWidth: 0,
                        cutout: '85%'
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    interaction: { intersect: false }
                }
            });
        }

        // 2. Fixed Modal Trigger & Backdrop Cleanup
        const historyBtn = document.getElementById('openHistory');
        const myModalEl = document.getElementById('historyModal');
        
        if (historyBtn && myModalEl) {
            historyBtn.addEventListener('click', function(e) {
                e.preventDefault();
                // Check for bootstrap variable (standard or window)
                const bs = window.bootstrap || bootstrap;
                if (bs) {
                    const myModal = new bs.Modal(myModalEl);
                    myModal.show();
                } else if (window.$) {
                    $(myModalEl).modal('show');
                }
            });

            // Cleanup "Gray Screen" on hide
            myModalEl.addEventListener('hidden.bs.modal', function () {
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        }
    });
</script>

<style>
    .tracking-tight { letter-spacing: -0.5px; }
    .progress-bar { border-radius: 50px; }
    .avatar-sm { font-size: 14px; }
    
    /* Z-Index Fixes to ensure modal sits on top of Nexus Theme elements */
    .modal { z-index: 1060 !important; }
    .modal-backdrop { z-index: 1050 !important; }
    body.modal-open { overflow: auto !important; }
</style>
@endsection