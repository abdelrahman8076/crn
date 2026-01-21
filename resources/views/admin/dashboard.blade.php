@extends('layouts.admin')

@section('content')
{{-- Added dir and text alignment logic for AR/EN support --}}
<div class="page-wrapper p-4 {{ app()->getLocale() == 'ar' ? 'text-end' : '' }}" 
     style="background-color: #f4f7fa; min-height: 100vh;" 
     dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    
    <x-flash-success />
    <x-flash-error />

    <div class="mb-4">
        <h3 class="fw-bold text-dark tracking-tight mb-1">{{ __('dashboard.title') }}</h3>
        <p class="text-muted small">{{ __('dashboard.welcome') }}, <span class="fw-semibold text-primary">{{ Auth::user()->name ?? 'Admin' }}</span></p>
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
                            <th style="width: 150px;">{{ __('admins.progress') }}</th>
                            <th class="pe-4 text-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">{{ __('admins.action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $loopData = $isSuperAdmin ? $managersData : ($managerViewData['team'] ?? []); @endphp

                        @foreach($loopData as $m)
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
                                <td class="fw-semibold text-dark">{{ number_format($m['target_total']) }}</td>
                                <td class="text-success fw-bold">{{ number_format($m['subtarget_total'] ?? 0) }} {{ __('dashboard.currency') }}</td>
                                <td>
                                    <div class="progress rounded-pill" style="height: 6px;">
                                        <div class="progress-bar {{ $m['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ min($m['progress'], 100) }}%"></div>
                                    </div>
                                    <small class="fw-bold text-dark mt-1 d-block">{{ $m['progress'] }}%</small>
                                </td>
                                <td class="pe-4 text-{{ app()->getLocale() == 'ar' ? 'start' : 'end' }}">
                                    @if($isSuperAdmin && isset($m['id']))
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#manager-team-{{ $m['id'] }}">
                                            <i class="ti ti-users me-1"></i> {{ __('admins.view_team') }}
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            {{-- Collapsible Team Details Row --}}
                            @if($isSuperAdmin && isset($m['id']))
                            <tr class="collapse" id="manager-team-{{ $m['id'] }}">
                                <td colspan="6" class="bg-light p-0">
                                    <div class="p-4 border-start border-primary border-4 ms-4 my-2 shadow-inner">
                                        <h6 class="fw-bold mb-3 text-primary">
                                            <i class="ti ti-arrow-forward-up me-2"></i>{{ __('admins.team_members_for') }} {{ $m['manager_name'] }}
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
                                                                        <small class="text-muted" style="font-size: 10px;">{{ $member['role'] }}</small>
                                                                    </div>
                                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">{{ $member['progress'] }}%</span>
                                                                </div>
                                                                <div class="progress mb-2" style="height: 4px;">
                                                                    <div class="progress-bar {{ $member['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ min($member['progress'], 100) }}%"></div>
                                                                </div>
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="text-start">
                                                                        <small class="text-muted d-block" style="font-size: 9px;">TARGET</small>
                                                                        <span class="fw-bold small">{{ number_format($member['target_total']) }}</span>
                                                                    </div>
                                                                    {{-- <div class="text-end">
                                                                        <small class="text-muted d-block" style="font-size: 9px;">REACHED</small>
                                                                        <span class="fw-bold text-primary small">{{ number_format($member['reached']) }}</span>
                                                                    </div> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-muted small italic p-3 bg-white rounded border border-dashed text-center">
                                                <i class="ti ti-info-circle me-1"></i> {{ __('admins.no_team_data') }}
                                            </div>
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

    {{-- Individual Target Sections (Two charts for Managers) --}}
    @auth
    @php 
        $myTarget = $data['myTarget'] ?? null;
        $isManager = optional(Auth::user()->role)->name === 'Manager';
    @endphp

    @if($myTarget && $myTarget['target_total'] > 0)
    <div class="row g-4 mb-4">
        {{-- Card 1: Main Goal --}}
        <div class="col-md-{{ $isManager ? '6' : '12' }}">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="ti ti-target me-2 text-danger"></i>{{ $isManager ? __('dashboard.team_performance') : __('users.my_target') }}</h5>
                    <button type="button" id="openHistory" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#historyModal">
                        <i class="ti ti-history me-1"></i>{{ __('users.view_history') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center">
                            <div style="max-width: 150px; margin: auto; position: relative;">
                                <canvas id="targetDoughnut"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <h4 class="fw-bold mb-0 text-dark">{{ round($myTarget['progress']) }}%</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h4 class="fw-bold text-dark mb-3">{{ $myTarget['period'] }}</h4>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-2 bg-light rounded-3 border">
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">{{ __('users.total_goal') }}</small>
                                        <span class="fs-6 fw-bold text-dark">{{ number_format($myTarget['target_total']) }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-2 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-25">
                                        <small class="text-primary d-block text-uppercase fw-bold" style="font-size: 10px;">{{ __('users.reached') }}</small>
                                        <span class="fs-6 fw-bold text-primary">{{ number_format($myTarget['reached']) }} {{ __('dashboard.currency') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Personal Sales (Subtarget) for Managers --}}
        @if($isManager)
        <div class="col-md-6">
            <div class="card shadow-sm border-0 rounded-4 h-100 bg-dark text-white">
                <div class="card-header bg-transparent py-3 border-0">
                    <h5 class="mb-0 fw-bold text-white"><i class="ti ti-user-star me-2 text-success"></i>{{ __('dashboard.personal_sales') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center">
                            <div style="max-width: 150px; margin: auto; position: relative;">
                                <canvas id="subtargetDoughnut"></canvas>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <i class="ti ti-trending-up fs-2 text-success"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <small class="text-white-50 d-block text-uppercase fw-bold" style="font-size: 10px;">{{ __('dashboard.total_won_subtarget') }}</small>
                            <h3 class="fw-bold text-success mb-2">{{ number_format($myTarget['subtarget_total'] ?? 0) }} {{ __('dashboard.currency') }}</h3>
                            <p class="small text-white-50 mb-0">{{ __('dashboard.personal_deals_help') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
    @endauth
</div>

{{-- (Modal Section remains the same as your source) --}}

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(isset($data['myTarget']) && $data['myTarget'])
            const myTarget = @json($data['myTarget']);

            // 1. Team/Main Target
            const ctx1 = document.getElementById('targetDoughnut');
            if (ctx1) {
                new Chart(ctx1, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [myTarget.reached, Math.max(0, myTarget.target_total - myTarget.reached)],
                            backgroundColor: ['#6366f1', '#f1f5f9'],
                            borderWidth: 0, cutout: '85%'
                        }]
                    },
                    options: { plugins: { legend: { display: false } } }
                });
            }

            // 2. Personal Subtarget (Accumulator)
            const ctx2 = document.getElementById('subtargetDoughnut');
            if (ctx2) {
                new Chart(ctx2, {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            // Uses subtarget_total which accumulates personal manager wins
                            data: [myTarget.subtarget_total, 1], 
                            backgroundColor: ['#10b981', '#334155'],
                            borderWidth: 0, cutout: '85%'
                        }]
                    },
                    options: { plugins: { legend: { display: false } } }
                });
            }
        @endif
    });
</script>
@endsection