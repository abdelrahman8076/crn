    <x-admin.header />

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

<div class="container mt-5" style="max-width: 500px" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    <h3 class="mb-4 text-center">
        {{ __('admins.create_account') }}
    </h3>

    <form method="POST" action="{{ route('admin.register.submit') }}">
        @csrf

        <div class="form-group mb-3">
            <label>{{ __('admins.name') }}</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label>{{ __('admins.email') }}</label>
            <input type="email" name="email" class="form-control" required>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label>{{ __('admins.password') }}</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label>{{ __('admins.confirm_password') }}</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">
            {{ __('admins.register_btn') }}
        </button>
    </form>
</div>

