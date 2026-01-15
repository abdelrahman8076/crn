@php
    $user = auth()->user();
    $isAdmin = $user && ($user->role_id == 1 || (isset($user->role) && $user->role->name === 'admin'));
@endphp

<div class="app-topstrip py-2 px-4 w-100 d-flex flex-row align-items-center justify-content-between">
    
    <div class="d-flex align-items-center gap-3">
        <a class="nav-link text-white p-2 rounded-circle hover-bg" href="javascript:void(0)" id="sidebarToggle">
            <i class="ti ti-menu-2 fs-6"></i>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <span class="text-white fw-bold fs-5">Nexus<span class="text-indigo-400">CRM</span></span>
        </a>
    </div>

    <div class="d-flex align-items-center gap-2 gap-md-4">

        <button class="btn btn-link text-white p-2 border-0 shadow-none" id="darkModeBtn" type="button">
            <i class="ti ti-moon fs-5" id="themeIcon"></i>
        </button>

        <div class="d-flex align-items-center gap-2 px-2 px-md-3 border-start border-white border-opacity-25">
            <a class="nav-lang {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('locale.switch', 'en') }}">EN</a>
            <span class="text-white opacity-25">|</span>
            <a class="nav-lang {{ app()->getLocale() == 'ar' ? 'active' : '' }}" href="{{ route('locale.switch', 'ar') }}">AR</a>
        </div>

        <div class="dropdown">
            <a href="javascript:void(0)" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold">
                    {{ substr($user->name ?? 'N', 0, 1) }}
                </div>
                {{-- <i class="ti ti-chevron-down text-white small d-none d-md-block"></i> --}}
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 animate-up">
                <li><h6 class="dropdown-header">{{ $user->name ?? 'User' }}</h6></li>
                <li><a class="dropdown-item" href="#"><i class="ti ti-user me-2"></i> {{ __('Profile') }}</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    @if($isAdmin)
                        <a class="dropdown-item text-danger" href="{{ route('admin.admin.logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                            <i class="ti ti-logout me-2"></i> {{ __('Logout') }}
                        </a>
                        <form id="admin-logout-form" action="{{ route('admin.admin.logout') }}" method="POST" class="d-none">@csrf</form>
                    @else
                        <a class="dropdown-item text-danger" href="{{ route('user.logout') }}" 
                           onclick="event.preventDefault(); document.getElementById('user-logout-form').submit();">
                            <i class="ti ti-logout me-2"></i> {{ __('Logout') }}
                        </a>
                        <form id="user-logout-form" action="{{ route('user.logout') }}" method="POST" class="d-none">@csrf</form>
                    @endif
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    /* Ensure the topstrip doesn't wrap and stays on top */
    .app-topstrip {
        background: #0f172a !important;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 70px; /* Explicit height */
        z-index: 1100;
        white-space: nowrap; /* Prevent items from jumping to next line */
    }

    .hover-bg:hover { background: rgba(255,255,255,0.1); }
    .text-indigo-400 { color: #818cf8; }
    
    .nav-lang { 
        color: rgba(255,255,255,0.6); 
        text-decoration: none; 
        font-weight: 600; 
        font-size: 0.85rem; 
        padding: 2px 4px;
    }
    .nav-lang.active { color: #fff; border-bottom: 2px solid #6366f1; }
    
    .avatar-sm { width: 35px; height: 35px; font-size: 0.9rem; flex-shrink: 0; }
    
    /* Layout adjustments */
    body { padding-top: 70px; }
    
    [data-bs-theme="dark"] .app-topstrip { background: #020617 !important; }

    /* Fix dropdown positioning in RTL */
    [dir="rtl"] .dropdown-menu-end {
        --bs-position-horizontal: 0;
        right: auto;
        left: 0;
    }
</style>