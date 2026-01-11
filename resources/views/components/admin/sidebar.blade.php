<aside class="left-sidebar @if(app()->getLocale() == 'ar') rtl-sidebar @endif">
    <div class="sidebar-nav scroll-sidebar">
        
        {{-- Brand Logo Section with Dynamic Badge --}}
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="text-nowrap logo-img d-flex align-items-center">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" />
                
                <div class="ms-2">
                    @if(Auth::guard('admin')->check())
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold text-uppercase px-2" style="font-size: 10px;">Portal: Admin</span>
                    @elseif(Auth::guard('web')->check())
                        @php
                            $role = Auth::user()->role?->name ?? 'Staff';
                            $color = match($role) { 'Manager' => 'primary', 'Sales' => 'success', default => 'secondary' };
                        @endphp
                        <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle fw-bold text-uppercase px-2" style="font-size: 10px;">{{ $role }}</span>
                    @endif
                </div>
            </a>
            <div class="cursor-pointer d-block d-xl-none" id="sidebarClose" style="font-size: 24px; color: #333;">
                <i class="ti ti-x"></i>
            </div>
        </div>

        <ul id="sidebarnav">
            {{-- Global Dashboard --}}
            <li class="nav-small-cap">
                <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                <span class="hide-menu">{{ __('aside.Main_Menu') }}</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                    <i class="ti ti-atom"></i>
                    <span class="hide-menu">{{ __('aside.Dashboard') }}</span>
                </a>
            </li>

            @php
                $isAdmin = Auth::guard('admin')->check();
                $isManager = Auth::guard('web')->check() && Auth::user()->role?->name === 'Manager';
                $isSales = Auth::guard('web')->check() && Auth::user()->role?->name === 'Sales';
            @endphp

            {{-- ADMIN ONLY: User Management --}}
            @if($isAdmin)
                <li class="nav-small-cap mt-4">
                    <span class="hide-menu">{{ __('aside.System_Management') }}</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.admin.index') }}" aria-expanded="false">
                        <i class="ti ti-user-shield"></i>
                        <span class="hide-menu">{{ __('aside.admin_index') }}</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.users.index') }}" aria-expanded="false">
                        <i class="ti ti-users"></i>
                        <span class="hide-menu">{{ __('aside.Users') }}</span>
                    </a>
                </li>
            @endif

            {{-- SHARED: CRM & Operations (Admin, Manager, Sales) --}}
            @if($isAdmin || $isManager || $isSales)
                <li class="nav-small-cap mt-4">
                    <span class="hide-menu text-uppercase">{{ __('aside.crm_operations') }}</span>
                </li>

                {{-- Upload only for Admin & Manager --}}
                @if($isAdmin || $isManager)
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.clients.uploadForm') }}" aria-expanded="false">
                        <i class="ti ti-cloud-upload"></i>
                        <span class="hide-menu">{{ __('aside.admins_clients_upload') }}</span>
                    </a>
                </li>
                @endif

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.clients.index') }}" aria-expanded="false">
                        <i class="ti ti-building"></i>
                        <span class="hide-menu">{{ __('aside.Clients') }}</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.deals.index') }}" aria-expanded="false">
                        <i class="ti ti-currency-dollar"></i>
                        <span class="hide-menu">{{ __('aside.Deals') }}</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.tasks.index') }}" aria-expanded="false">
                        <i class="ti ti-checklist"></i>
                        <span class="hide-menu">{{ __('aside.Tasks') }}</span>
                    </a>
                </li>
            @endif

            {{-- Logout Section --}}
            <li class="sidebar-item mt-5 border-top pt-3">
                @if($isAdmin)
                    <a class="sidebar-link text-danger" href="{{ route('admin.admin.logout') }}">
                        <i class="ti ti-logout text-danger"></i>
                        <span class="hide-menu fw-bold">{{ __('aside.Logout') }}</span>
                    </a>
                @else
                    <a class="sidebar-link text-danger" href="{{ route('user.logout') }}" 
                       onclick="event.preventDefault(); document.getElementById('user-logout-form').submit();">
                        <i class="ti ti-logout text-danger"></i>
                        <span class="hide-menu fw-bold">{{ __('aside.Logout') }}</span>
                    </a>
                    <form method="POST" action="{{ route('user.logout') }}" id="user-logout-form" class="d-none">@csrf</form>
                @endif
            </li>
        </ul>
    </div>
</aside>

<style>
    /* Default state (Hidden on mobile) */
    @media (max-width: 1199.98px) {
        .left-sidebar {
            position: fixed;
            left: -270px; /* Adjust based on your sidebar width */
            transition: 0.3s ease-in-out;
            z-index: 9999;
        }

        /* RTL support for default state */
        .left-sidebar.rtl-sidebar {
            left: auto;
            right: -270px;
        }

        /* Active state (Shown) */
        .left-sidebar.active {
            left: 0;
        }

        .left-sidebar.rtl-sidebar.active {
            right: 0;
        }
    }

    #sidebarClose {
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Prevent body scrolling when sidebar is open on mobile */
    body.sidebar-active {
        overflow: hidden;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.querySelector('.left-sidebar');
        const openBtn = document.getElementById('sidebarToggle'); // Your trigger button in the header
        const closeBtn = document.getElementById('sidebarClose');  // The new X button

        // Function to toggle
        function toggleClasses() {
            sidebar.classList.toggle('active');
            document.body.classList.toggle('sidebar-active');
        }

        // // Open/Toggle listener
        // if (openBtn) {
        //     openBtn.addEventListener('click', toggleClasses);
        // }

        // // Close (X) listener
        // if (closeBtn) {
        //     closeBtn.addEventListener('click', toggleClasses);
        // }
    });
</script>