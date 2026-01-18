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
                        <label class="form-label">{{ __('users.password_confirmation') }} *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
            </div>

            {{-- Column 2: Roles & Targets --}}
            <div class="col-md-6 ps-md-4">
                <h5 class="mb-4 text-primary fw-bold">{{ __('users.assignment_info') }}</h5>

                <div class="mb-3">
                    <label for="role_id" class="form-label">{{ __('users.role') }} *</label>
                    <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" data-name="{{ strtolower($role->name) }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Primary role (legacy)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Position (Hierarchy)</label>
                    <select name="position_id" id="position_id" class="form-select @error('position_id') is-invalid @enderror">
                        <option value="">No Position</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id', $user->position_id) == $position->id ? 'selected' : '' }}>
                                {{ str_repeat('— ', $position->level) }}{{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Determines hierarchy level</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Additional Roles</label>
                    <select name="role_ids[]" id="role_ids" class="form-select" multiple size="4">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ in_array($role->id, old('role_ids', $userRoleIds)) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                </div>

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
                    <small class="text-muted">Legacy manager assignment</small>
                </div>

                <div class="mb-3">
                    <label class="form-label">Direct Permissions</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @php
                            $permissionGroups = $permissions->groupBy('group');
                        @endphp
                        @foreach($permissionGroups as $group => $groupPermissions)
                            <div class="mb-2">
                                <strong class="text-primary d-block mb-1">{{ ucfirst($group) }}</strong>
                                @foreach($groupPermissions as $permission)
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permission_ids[]" 
                                               value="{{ $permission->id }}" 
                                               id="perm_{{ $permission->id }}"
                                               {{ in_array($permission->id, old('permission_ids', $userPermissionIds)) ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                            {{ str_replace($group . '.', '', $permission->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted">Direct permissions override role permissions</small>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 text-success fw-bold">{{ __('users.set_new_target') }}</h5>
                
                @php
                    // Get the most recent target to show in the form
                    $latestTarget = $user->targets->sortByDesc('period')->first();
                @endphp

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="target_total" class="form-label">{{ __('users.target_amount') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="target_total" id="target_total" class="form-control" value="{{ old('target_total', $latestTarget?->target_total) }}" placeholder="0.00">
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="target_period" class="form-label">{{ __('users.target_period') }}</label>
<input type="date" 
       name="target_period" 
       id="target_period" 
       class="form-control dark-date-input" 
       value="{{ old('target_period', date('Y-m-d')) }}">                    </div>
                </div>
                <small class="text-muted d-block mt-n2">{{ __('users.target_logic_hint') }}</small>
            </div>
        </div>

        <div class="mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary px-5 fw-bold">
                <i class="bi bi-save me-1"></i> {{ __('users.update_user_and_targets') }}
            </button>
        </div>
    </form>

    {{-- Target History Table --}}
    <div class="mt-5">
        <h5 class="mb-3 fw-bold"><i class="bi bi-clock-history me-2"></i>{{ __('users.target_history') }}</h5>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">{{ __('users.period') }}</th>
                            <th>{{ __('users.target_amount') }}</th>
                            <th>{{ __('users.reached') }}</th>
                            <th>{{ __('users.progress') }}</th>
                            <th class="text-end pe-3">{{ __('users.status') }}</th>
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
                                <td class="text-end pe-3">
                                    @if($target->is_active)
                                        <span class="badge bg-success">{{ __('users.active') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('users.archived') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">{{ __('users.no_targets_found') }}</td>
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
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName = selectedOption ? selectedOption.getAttribute('data-name') : '';

            if (roleName === 'manager' || roleName === 'admin') {
                managerContainer.style.display = 'none';
                document.getElementById('manager_id').value = '';
            } else {
                managerContainer.style.display = 'block';
            }
        }

        toggleManagerField();
        roleSelect.addEventListener('change', toggleManagerField);
    });
</script>
@endsection