@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <x-flash-success />
    <x-flash-error />

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        @if($isSuperAdmin)
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted small text-uppercase">{{ __('admins.users') }}</h6><h3 class="fw-bold mb-0">{{ $data['totalUsers'] }}</h3></div>
                    <div class="bg-soft-primary p-3 rounded-circle"><i class="ti ti-users fs-2 text-primary"></i></div>
                </div>
            </div>
        </div>
        @endif
        <div class="col-md-{{ $isSuperAdmin ? '3' : '6' }}">
            <div class="card shadow-sm border-0 border-start border-success border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted small text-uppercase">{{ __('admins.clients') }}</h6><h3 class="fw-bold mb-0">{{ $data['totalClients'] }}</h3></div>
                    <div class="bg-soft-success p-3 rounded-circle"><i class="ti ti-building fs-2 text-success"></i></div>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-{{ $isSuperAdmin ? '3' : '4' }}">
            <div class="card shadow-sm border-0 border-start border-warning border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted small text-uppercase">Leads</h6><h3 class="fw-bold mb-0">{{ $data['totalLeads'] }}</h3></div>
                    <div class="bg-soft-warning p-3 rounded-circle"><i class="ti ti-filter fs-2 text-warning"></i></div>
                </div>
            </div>
        </div> --}}
        <div class="col-md-{{ $isSuperAdmin ? '3' : '6' }}">
            <div class="card shadow-sm border-0 border-start border-danger border-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted small text-uppercase">{{ __('admins.deals') }}</h6><h3 class="fw-bold mb-0">{{ $data['totalDeals'] }}</h3></div>
                    <div class="bg-soft-danger p-3 rounded-circle"><i class="ti ti-currency-dollar fs-2 text-danger"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Performance Monitor (Admin & Managers) --}}
    @if($isSuperAdmin)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-chart-bar me-2"></i>{{ __('admins.team_performance_monitor') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('admins.member_name') }}</th>
                            <th>{{ __('admins.target_total') }}</th>
                            <th>{{ __('admins.reached') }}</th>
                            <th style="width: 200px;">{{ __('admins.progress') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($managersData as $m)
                        <tr>
                            <td><strong>{{ $m['manager_name'] ?? $m['user_name'] ?? '-' }}</strong><br><small class="text-muted">{{ $m['role'] ?? __('users.manager') }}</small></td>
                            <td>${{ number_format($m['target_total']) }}</td>
                            <td class="text-primary fw-bold">${{ number_format($m['reached']) }}</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $m['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ min($m['progress'], 100) }}%"></div>
                                </div>
                                <small class="fw-bold">{{ $m['progress'] }}%</small>
                            </td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#manager-team-{{ $m['id'] }}">{{ __('admins.view_team') }}</button>
                            </td>
                        </tr>
                        <tr class="collapse" id="manager-team-{{ $m['id'] }}">
                            <td colspan="5" class="bg-light">
                                <div class="p-3">
                                    <h6 class="fw-bold mb-3">Team Members</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>{{ __('admins.member_name') }}</th>
                                                    <th>{{ __('admins.target_total') }}</th>
                                                    <th>{{ __('admins.reached') }}</th>
                                                    <th style="width: 200px;">{{ __('admins.progress') }}</th>
                                                    <th>{{ __('admins.status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($m['team'] as $member)
                                                <tr>
                                                    <td><strong>{{ $member['user_name'] ?? '-' }}</strong><br><small class="text-muted">{{ $member['role'] ?? __('users.role') }}</small></td>
                                                    <td>${{ number_format($member['target_total']) }}</td>
                                                    <td class="text-primary fw-bold">${{ number_format($member['reached']) }}</td>
                                                    <td>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar {{ $member['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ min($member['progress'], 100) }}%"></div>
                                                        </div>
                                                        <small class="fw-bold">{{ $member['progress'] }}%</small>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $member['progress'] >= 100 ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                                            {{ $member['progress'] >= 100 ? __('admins.finished') : __('admins.in_progress') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif(optional(Auth::user()->role)->name === 'Manager' && $managerViewData)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-chart-bar me-2"></i>{{ __('admins.team_performance_monitor') }}</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>{{ __('admins.member_name') }}</th>
                            <th>{{ __('admins.target_total') }}</th>
                            <th>{{ __('admins.reached') }}</th>
                            <th style="width: 200px;">{{ __('admins.progress') }}</th>
                            <th>{{ __('admins.status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($managerViewData['team'] as $member)
                        <tr>
                            <td><strong>{{ $member['user_name'] ?? '-' }}</strong><br><small class="text-muted">{{ $member['role'] ?? __('users.role') }}</small></td>
                            <td>${{ number_format($member['target_total']) }}</td>
                            <td class="text-primary fw-bold">${{ number_format($member['reached']) }}</td>
                            <td>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $member['progress'] >= 100 ? 'bg-success' : 'bg-primary' }}" style="width: {{ min($member['progress'], 100) }}%"></div>
                                </div>
                                <small class="fw-bold">{{ $member['progress'] }}%</small>
                            </td>
                            <td>
                                <span class="badge {{ $member['progress'] >= 100 ? 'bg-soft-success text-success' : 'bg-soft-warning text-warning' }}">
                                    {{ $member['progress'] >= 100 ? __('admins.finished') : __('admins.in_progress') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- Individual Target Section (Sales/Web Guard) --}}
    @auth('web')
    @php $myTarget = $data['allTargets']->where('user_name', Auth::user()->name)->first(); @endphp
    @if($myTarget && $myTarget['target_total'] > 0)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold text-dark"><i class="ti ti-target me-2 text-danger"></i>{{ __('users.my_target') }}</h5>
            <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#historyModal"><i class="ti ti-history me-1"></i>{{ __('users.view_history') }}</button>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <div style="max-width: 200px; margin: auto;"><canvas id="targetDoughnut"></canvas></div>
                </div>
                <div class="col-md-8">
                    <h4 class="fw-bold">{{ $myTarget['period'] }}</h4>
                    <p class="text-muted">{{ __('users.target_description', ['reached' => number_format($myTarget['reached']), 'total' => number_format($myTarget['target_total'])]) }}</p>
                    <div class="row g-3">
                        <div class="col-sm-4"><div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">{{ __('users.total_goal') }}</small>
                            <span class="fs-5 fw-bold">${{ number_format($myTarget['target_total']) }}</span>
                        </div></div>
                        <div class="col-sm-4"><div class="p-3 bg-light rounded text-primary">
                            <small class="text-muted d-block">{{ __('users.reached') }}</small>
                            <span class="fs-5 fw-bold">${{ number_format($myTarget['reached']) }}</span>
                        </div></div>
                        <div class="col-sm-4"><div class="p-3 bg-soft-danger rounded text-danger">
                            <small class="text-danger d-block">{{ __('users.remaining') }}</small>
                            <span class="fs-5 fw-bold">${{ number_format($myTarget['remaining']) }}</span>
                        </div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- History Modal --}}
    <div class="modal fade" id="historyModal" tabindex="-1">
        <div class="modal-dialog modal-lg"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">{{ __('users.target_history') }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>{{ __('users.period') }}</th><th>{{ __('users.target_total') }}</th><th>{{ __('users.status') }}</th></tr></thead>
                    <tbody>
                        @foreach(Auth::user()->targets()->orderBy('period', 'desc')->get() as $h)
                        <tr><td>{{ $h->period }}</td><td>${{ number_format($h->target_total) }}</td><td><span class="badge {{ $h->is_active ? 'bg-primary' : 'bg-secondary' }}">{{ $h->is_active ? 'Active' : 'Past' }}</span></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new Chart(document.getElementById('targetDoughnut'), {
                type: 'doughnut',
                data: {
                    labels: ['Reached', 'Remaining'],
                    datasets: [{
                        data: [{{ $myTarget['reached'] }}, {{ $myTarget['remaining'] }}],
                        backgroundColor: ['#0d6efd', '#f4f4f4'],
                        borderWidth: 0, cutout: '80%'
                    }]
                },
                options: { plugins: { legend: { display: false } } }
            });
        });
    </script>
    @endif
    @endauth
</div>
@endsection