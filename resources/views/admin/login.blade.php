<x-admin.header />

@php
    $isArabic = app()->getLocale() === 'ar';
@endphp

<div class="min-h-screen d-flex align-items-center justify-content-center bg-light grid-bg px-3" 
     dir="{{ $isArabic ? 'rtl' : 'ltr' }}" 
     style="font-family: 'Instrument Sans', sans-serif;">
    
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 450px; width: 100%;">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-dark rounded-3 mb-3" style="width: 48px; height: 48px;">
                    <span class="text-white fw-bold fs-4">C</span>
                </div>
                <h4 class="fw-bold tracking-tight text-dark">{{ __('admins.login_title') }}</h4>
                <p class="text-muted small">{{ __('admins.login_subtitle') }}</p>
            </div>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">{{ __('admins.email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="ti ti-mail"></i>
                        </span>
                        <input type="email" name="email" 
                               class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" 
                               placeholder="name@company.com" required autofocus>
                    </div>
                    @error('email') 
                        <div class="invalid-feedback d-block mt-1">{{ $message }}</div> 
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <label class="form-label small fw-semibold text-secondary">{{ __('admins.password') }}</label>
                        <a href="#" class="small text-decoration-none text-orange-600">{{ __('admins.forgot_password') }}</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="ti ti-lock"></i>
                        </span>
                        <input type="password" name="password" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="••••••••" required>
                    </div>
                </div>

                <div class="mb-4 d-flex align-items-center">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input border-secondary me-2">
                    <label for="remember" class="small text-secondary cursor-pointer">{{ __('admins.remember_me') }}</label>
                </div>

                <button type="submit" class="btn btn-dark w-100 py-2 fw-bold rounded-3 shadow-sm transition-transform hover-scale">
                    {{ __('admins.login_btn') }}
                </button>
            </form>

            <div class="text-center mt-5 pt-3 border-top">
                <a href="{{ url('locale/' . ($isArabic ? 'en' : 'ar')) }}" class="text-decoration-none text-muted small fw-medium">
                    <i class="ti ti-world me-1"></i>
                    {{ $isArabic ? 'Switch to English' : 'تغيير للغة العربية' }}
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .grid-bg {
        background-color: #FDFDFC;
        background-image: radial-gradient(circle at 2px 2px, rgba(0,0,0,0.05) 1px, transparent 0);
        background-size: 24px 24px;
    }
    .text-orange-600 { color: #ea580c; }
    .bg-light-orange { background-color: #fff7ed; }
    .hover-scale:hover { transform: scale(1.01); }
    .tracking-tight { letter-spacing: -0.025em; }
    input.form-control:focus {
        border-color: #000;
        box-shadow: none;
    }
    .cursor-pointer { cursor: pointer; }
</style>