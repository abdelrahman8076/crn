<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="left-sidebar @if(app()->getLocale() == 'ar') rtl-sidebar @endif shadow-sm">
    <div class="sidebar-wrapper h-100 d-flex flex-column">
        
        <div class="brand-logo d-flex align-items-center justify-content-between px-4 py-3 border-bottom border-light">
            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <div class="logo-circle bg-primary bg-gradient rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                    <i class="ti ti-nebula text-white fs-5"></i>
                </div>
                <span class="fw-bold fs-5 text-dark tracking-tight">Nexus<span class="text-primary">CRM</span></span>
            </a>
            <button class="btn btn-link p-0 text-muted d-xl-none" id="sidebarClose">
                <i class="ti ti-x fs-6"></i>
            </button>
        </div>

        <div class="px-4 py-3 mb-2">
            <div class="role-card p-2 rounded-3 bg-light border d-flex align-items-center gap-2">
                @if(Auth::guard('admin')->check())
                    <div class="bg-danger rounded-circle" style="width: 8px; height: 8px;"></div>
                    <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">Portal: Admin</span>
                @else
                    @php
                        $role = Auth::user()->role?->name ?? 'Staff';
                        $statusColor = match($role) { 'Manager' => 'primary', 'Sales' => 'success', default => 'secondary' };
                    @endphp
                    <div class="bg-{{ $statusColor }} rounded-circle" style="width: 8px; height: 8px;"></div>
                    <span class="small fw-bold text-uppercase text-muted" style="letter-spacing: 0.5px;">{{ $role }}</span>
                @endif
            </div>
        </div>

        <div class="sidebar-nav scroll-sidebar px-3 flex-grow-1">
            <ul id="sidebarnav" class="list-unstyled">
                
                <li class="nav-small-cap text-uppercase text-muted fw-semibold small mb-2 px-2 mt-2">
                    {{ __('aside.Main_Menu') }}
                </li>

                <li class="sidebar-item mb-1">
                    <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none @if(Route::is('admin.dashboard')) active @endif" href="{{ route('admin.dashboard') }}">
                        <i class="ti ti-smart-home fs-5"></i>
                        <span class="hide-menu">{{ __('aside.Dashboard') }}</span>
                    </a>
                </li>

                @php
                    $isAdmin = Auth::guard('admin')->check();
                    $isManager = Auth::guard('web')->check() && Auth::user()->role?->name === 'Manager';
                    $isSales = Auth::guard('web')->check() && Auth::user()->role?->name === 'Sales';
                @endphp

                @if($isAdmin)
                    <li class="nav-small-cap text-uppercase text-muted fw-semibold small mb-2 px-2 mt-4">
                        {{ __('aside.System_Management') }}
                    </li>
                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none @if(Route::is('admin.admin.*')) active @endif" href="{{ route('admin.admin.index') }}">
                            <i class="ti ti-shield-lock fs-5"></i>
                            <span class="hide-menu">{{ __('aside.admin_index') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none @if(Route::is('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">
                            <i class="ti ti-users-group fs-5"></i>
                            <span class="hide-menu">{{ __('aside.Users') }}</span>
                        </a>
                    </li>
                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none @if(Route::is('admin.positions.*')) active @endif" href="{{ route('admin.positions.index') }}">
                            <i class="ti ti-hierarchy fs-5"></i>
                            <span class="hide-menu">Positions</span>
                        </a>
                    </li>
                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none @if(Route::is('admin.roles.*')) active @endif" href="{{ route('admin.roles.index') }}">
                            <i class="ti ti-shield fs-5"></i>
                            <span class="hide-menu">Roles</span>
                        </a>
                    </li>
                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none @if(Route::is('admin.permissions.*')) active @endif" href="{{ route('admin.permissions.index') }}">
                            <i class="ti ti-key fs-5"></i>
                            <span class="hide-menu">Permissions</span>
                        </a>
                    </li>
                @endif

                @if($isAdmin || $isManager || $isSales)
                    <li class="nav-small-cap text-uppercase text-muted fw-semibold small mb-2 px-2 mt-4">
                        {{ __('aside.crm_operations') }}
                    </li>

                    @if($isAdmin || $isManager)
                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none" href="{{ route('admin.clients.uploadForm') }}">
                            <i class="ti ti-cloud-upload fs-5"></i>
                            <span class="hide-menu">{{ __('aside.admins_clients_upload') }}</span>
                        </a>
                    </li>
                    @endif

                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none" href="{{ route('admin.clients.index') }}">
                            <i class="ti ti-building-store fs-5"></i>
                            <span class="hide-menu">{{ __('aside.Clients') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none" href="{{ route('admin.deals.index') }}">
                            <i class="ti ti-coin fs-5"></i>
                            <span class="hide-menu">{{ __('aside.Deals') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item mb-1">
                        <a class="sidebar-link d-flex align-items-center gap-3 px-3 py-2 rounded-3 text-decoration-none" href="{{ route('admin.tasks.index') }}">
                            <i class="ti ti-list-check fs-5"></i>
                            <span class="hide-menu">{{ __('aside.Tasks') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="p-4 border-top mt-auto">
            @if($isAdmin)
                <a class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3" href="{{ route('admin.admin.logout') }}">
                    <i class="ti ti-power fs-5"></i>
                    <span class="fw-bold">{{ __('aside.Logout') }}</span>
                </a>
            @else
                <a class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 rounded-3" href="{{ route('user.logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('user-logout-form-sidebar').submit();">
                    <i class="ti ti-power fs-5"></i>
                    <span class="fw-bold">{{ __('aside.Logout') }}</span>
                </a>
                <form method="POST" action="{{ route('user.logout') }}" id="user-logout-form-sidebar" class="d-none">@csrf</form>
            @endif
        </div>
    </div>
</aside>

<style>
    /* 3. CSS Fixes */
    .left-sidebar {
        width: 270px;
        background: #ffffff;
        height: 100vh;
        position: fixed;
        top: 0;
        z-index: 2000; /* High Z-index to be over everything */
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-inline-end: 1px solid #f1f5f9;
    }

    [dir="ltr"] .left-sidebar { left: 0; transform: translateX(0); }
    [dir="rtl"] .left-sidebar { right: 0; transform: translateX(0); }

    /* Overlay Styling */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1999;
        display: none;
        backdrop-filter: blur(2px);
    }

    .sidebar-overlay.active { display: block; }

    /* Links */
    .sidebar-link { color: #64748b; font-weight: 500; transition: 0.2s; }
    .sidebar-link:hover { background: #f8fafc; color: #6366f1; }
    .sidebar-link.active {
        background: rgba(99, 102, 241, 0.1) !important;
        color: #6366f1 !important;
        border-inline-start: 4px solid #6366f1;
    }

    /* Mobile Logic */
    @media (max-width: 1199.98px) {
        [dir="ltr"] .left-sidebar { transform: translateX(-100%); }
        [dir="rtl"] .left-sidebar { transform: translateX(100%); }
        .left-sidebar.active { transform: translateX(0) !important; }
    }

    .role-card { background-color: #f8fafc; }
    .scroll-sidebar { overflow-y: auto; }
    body.sidebar-active { overflow: hidden; }
</style>

<script>
    // 4. Fixed JavaScript Logic
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.querySelector('.left-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeBtn = document.getElementById('sidebarClose');

        const toggleSidebar = () => {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            document.body.classList.toggle('sidebar-active');
        };

        // Listen for hamburger click anywhere on the page (Event Delegation)
        document.addEventListener('click', function (e) {
            // Find if the click was on the toggle button or its children
            const toggleBtn = e.target.closest('#sidebarToggle');
            
            if (toggleBtn) {
                e.preventDefault();
                toggleSidebar();
            }
        });

        // Close button within sidebar
        if (closeBtn) closeBtn.addEventListener('click', toggleSidebar);

        // Close when clicking the overlay
        if (overlay) overlay.addEventListener('click', toggleSidebar);
    });
</script>