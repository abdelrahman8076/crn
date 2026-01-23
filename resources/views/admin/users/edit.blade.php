@extends('layouts.admin')

@section('content')
<div class="container pb-5">
    <x-flash-success />
    <x-flash-error />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('users.edit_title') }}: <span class="text-muted">{{ $user->name }}</span></h4>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> {{ __('users.back') }}
        </a>
    </div>

    <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="card p-4 shadow-sm border-0">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Column 1: Account Details --}}
            <div class="col-md-6 border-end">
                <h5 class="mb-4 text-primary fw-bold">{{ __('users.basic_info') }}</h5>
                
                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('users.name') }} *</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('users.email') }} *</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">{{ __('users.password') }}</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ __('users.leave_blank') }}">
                    <small class="text-muted">{{ __('users.password_hint') }}</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('users.password_confirmation') }}</label>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="{{ __('users.leave_blank') }}">
                    <small class="text-muted">{{ __('users.password_confirmation_hint') ?? 'Only required if changing password' }}</small>
                </div>
            </div>

            {{-- Column 2: Roles & Targets --}}
            <div class="col-md-6 ps-md-4">
                <h5 class="mb-4 text-primary fw-bold">{{ __('users.assignment_info') }}</h5>

                @if(!auth()->guard('admin')->check())
                <div class="mb-3">
                    <label for="role_id" class="form-label">{{ __('users.role') }} *</label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-name="{{ strtolower($role->name) }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="role_id" id="role_id" value="{{ old('role_id', $user->role_id) }}">
                @endif

                <div class="mb-3" id="manager-container">
                    <label for="manager_id" class="form-label">{{ __('users.manager') }}</label>
                    <select name="manager_id" id="manager_id" class="form-select @error('manager_id') is-invalid @enderror">
                        <option value="">{{ __('users.select_manager') }}</option>
                        @foreach($users as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id', $user->manager_id) == $manager->id ? 'selected' : '' }}>
                                {{ $manager->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <hr class="my-4">

                {{-- <h5 class="mb-3 text-success fw-bold">{{ __('users.set_new_target') }}</h5> --}}
                
                @php
                    // Get the most recent target to show in the form
                    $latestTarget = $user->targets->sortByDesc('period')->first();
                @endphp

                <div class="row">
                    {{-- <div class="col-sm-6 mb-3">
                        <label for="target_total" class="form-label">{{ __('users.target_amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="target_total" id="target_total" class="form-control" value="{{ old('target_total', $latestTarget?->target_total) }}" placeholder="0.00">
                        </div>
                    </div> --}}
                    {{-- <div class="col-sm-6 mb-3">
                        <label for="target_period" class="form-label">{{ __('users.target_period') }}</label>
<input type="date" 
       name="target_period" 
       id="target_period" 
       class="form-control dark-date-input" 
       value="{{ old('target_period', date('Y-m-d')) }}">                    </div>
                </div> --}}
                {{-- <small class="text-muted d-block mt-n2">{{ __('users.target_logic_hint') }}</small> --}}
            </div>
        </div>

        <div class="mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary px-5 fw-bold">
                <i class="bi bi-save me-1"></i> {{ __('users.update_user_and_targets') }}
            </button>
            
        </div>
    </form>
    {{-- Modal for Adding Multiple Targets --}}
<div class="modal fade" id="addNewTarget" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.targets.store', $user->id) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>{{ __('users.set_new_target') }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('users.target_amount') }}</label>
                    <div class="input-group">
                        <span class="input-group-text">LE</span>
                        <input type="number" name="target_total" class="form-control" placeholder="0.00" required step="0.01">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">{{ __('users.target_period') }}</label>
                    <input type="date" name="target_period" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="alert alert-info py-2 small">
                    <i class="bi bi-info-circle me-1"></i> {{ __('users.target_logic_hint') }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('users.cancel') }}</button>
                <button type="submit" class="btn btn-success px-4">{{ __('users.save_changes') }}</button>
            </div>
        </form>
    </div>
</div>

    {{-- Target History Table --}}
{{-- Target History Table --}}
<div class="mt-5">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold m-0"><i class="bi bi-clock-history me-2"></i>{{ __('users.target_history') }}</h5>
    {{-- Button to open the "Add Multiple Targets" Modal --}}
    <button type="button" class="btn btn-success btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addNewTarget">
        <i class="bi bi-plus-lg me-1"></i> {{ __('users.set_new_target') }}
    </button>
</div>    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">{{ __('users.period') }}</th>
                        <th>{{ __('users.target_amount') }}</th>
                        <th>{{ __('users.reached') }}</th>
                        <th>{{ __('users.progress') }}</th>
                        <th>{{ __('users.status') }}</th>
                        <th class="text-end pe-3">{{ __('users.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($user->targets->sortByDesc('period') as $target)
                        <tr>
                            <td class="ps-3 fw-bold">{{ \Carbon\Carbon::parse($target->period)->format('F Y') }}</td>
                            <td>${{ number_format($target->target_total, 2) }}</td>
                            <td class="text-success">${{ number_format($target->target_total - $target->target_remaining, 2) }}</td>
                            <td>
                                <div class="progress" style="height: 6px; width: 100px;">
                                    <div class="progress-bar {{ $target->progress >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                         style="width: {{ $target->progress }}%"></div>
                                </div>
                                <small>{{ $target->progress }}%</small>
                            </td>
                            <td>
                                @if($target->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ __('users.active') }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ __('users.archived') }}</span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="btn-group">
                                    {{-- Edit Button (Triggers Modal) --}}

                                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editTarget{{ $target->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    @if(!$target->is_active)
                                        <form action="{{ route('admin.targets.activate', $target->id) }}" method="POST" class="ms-1">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ __('users.set_active') }}">
                                                <i class="bi bi-play-fill"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editTarget{{ $target->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('admin.targets.update', $target->id) }}" method="POST" class="modal-content">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('users.edit_target') }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('users.target_amount') }}</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">LE</span>
                                                        <input type="number" name="target_total" class="form-control" value="{{ $target->target_total }}" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('users.period') }}</label>
                                                    <input type="date" name="period" class="form-control" value="{{ \Carbon\Carbon::parse($target->period)->format('Y-m-d') }}" required>
                                                </div>
                                                
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('users.cancel') }}</button>
                                                <button type="submit" class="btn btn-primary">{{ __('users.save_changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">{{ __('users.no_targets_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role_id');
        const managerContainer = document.getElementById('manager-container');

        function toggleManagerField() {
            // Check if roleSelect is a select element (not hidden input for admins)
            if (roleSelect && roleSelect.tagName === 'SELECT') {
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                const roleName = selectedOption ? selectedOption.getAttribute('data-name') : '';

                if (roleName === 'manager') {
                    managerContainer.style.display = 'none';
                    document.getElementById('manager_id').value = '';
                } else {
                    managerContainer.style.display = 'block';
                }
            }
        }

        if (roleSelect && roleSelect.tagName === 'SELECT') {
            toggleManagerField();
            roleSelect.addEventListener('change', toggleManagerField);
        }
    });
</script>
@endsection