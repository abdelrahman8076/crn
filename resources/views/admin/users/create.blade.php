@extends('layouts.admin')

@section('content')
<div class="container pb-5">
    <x-flash-success />
    <x-flash-error />

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('users.create_title') }}</h4>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> {{ __('users.back') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="card shadow-sm border-0">
        @csrf

        <div class="card-body p-4">
            <div class="row">
                {{-- Left Side: Basic Info --}}
                <div class="col-md-5 border-end">
                    <h5 class="mb-3 text-primary fw-bold">{{ __('users.basic_info') }}</h5>
                    
                    <div class="mb-3">
                        <label class="form-label">{{ __('users.name') }} *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('users.email') }} *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('users.password') }} *</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('users.password_confirmation') }} *</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                {{-- Right Side: Assignment & Targets --}}
                <div class="col-md-7 ps-md-4">
                    <h5 class="mb-3 text-primary fw-bold">{{ __('users.assignment_info') }}</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('users.role') }} *</label>
                            <select name="role_id" id="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                                <option value="">{{ __('users.select_role') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" data-name="{{ strtolower($role->name) }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3" id="manager_container">
                            <label class="form-label">{{ __('users.manager') }}</label>
                            <select name="manager_id" id="manager_id" class="form-select">
                                <option value="">{{ __('users.select_manager') }}</option>
                                @foreach($users as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-success fw-bold mb-0">{{ __('users.monthly_targets') }}</h5>
                        <button type="button" class="btn btn-sm btn-outline-success" id="add-target-row">
                            <i class="bi bi-plus-lg"></i> {{ __('users.add_another_month') }}
                        </button>
                    </div>

                    <div id="target-rows-container">
                        {{-- First Row (Default) --}}
                        <div class="row g-2 target-row mb-2">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="targets[0][amount]" class="form-control" placeholder="Target Amount">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <input type="month" name="targets[0][period]" class="form-control" value="{{ date('Y-m') }}">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger w-100 remove-row" disabled><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <small class="text-muted">{{ __('users.multiple_targets_help') }}</small>
                </div>
            </div>
        </div>

        <div class="card-footer bg-light p-3 text-end">
            <button type="submit" class="btn btn-primary px-5">
                <i class="bi bi-person-plus me-1"></i> {{ __('users.create_user') }}
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const roleSelect = document.getElementById('role_id');
        const managerContainer = document.getElementById('manager_container');
        const container = document.getElementById('target-rows-container');
        const addBtn = document.getElementById('add-target-row');
        let rowIdx = 1;

        // 1. Toggle Manager Field
        function toggleManagerField() {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName = selectedOption ? selectedOption.getAttribute('data-name') : '';
            managerContainer.style.display = (roleName === 'manager' || roleName === 'admin') ? 'none' : 'block';
        }

        roleSelect.addEventListener('change', toggleManagerField);
        toggleManagerField();

        // 2. Dynamic Target Rows
        addBtn.addEventListener('click', () => {
            const newRow = document.createElement('div');
            newRow.className = 'row g-2 target-row mb-2';
            newRow.innerHTML = `
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="targets[${rowIdx}][amount]" class="form-control" placeholder="Target Amount">
                    </div>
                </div>
                <div class="col-md-5">
                    <input type="month" name="targets[${rowIdx}][period]" class="form-control" value="{{ date('Y-m') }}">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100 remove-row"><i class="bi bi-trash"></i></button>
                </div>
            `;
            container.appendChild(newRow);
            rowIdx++;
        });

        // 3. Remove Row
        container.addEventListener('click', (e) => {
            if (e.target.closest('.remove-row')) {
                e.target.closest('.target-row').remove();
            }
        });
    });
</script>
@endsection