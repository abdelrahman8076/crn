    <x-admin.header />


@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

<div class="container mt-5" style="max-width: 450px" dir="{{ $isArabic ? 'rtl' : 'ltr' }}">
    <h3 class="mb-4 text-center">{{ __('admins.login_title') }}</h3>

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf

        <div class="form-group mb-3">
            <label>{{ __('admins.email') }}</label>
            <input type="email" name="email" class="form-control" required autofocus>
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="form-group mb-3">
            <label>{{ __('admins.password') }}</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label>
                <input type="checkbox" name="remember"> {{ __('admins.remember_me') }}
            </label>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            {{ __('admins.login_btn') }}
        </button>
    </form>
</div>

