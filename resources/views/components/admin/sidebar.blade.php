<aside class="left-sidebar @if(app()->getLocale() == 'ar') rtl-sidebar @endif">
    <div>
        <!-- Brand Logo -->
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/images/logos/logo.svg') }}" alt="Logo" />
            </a>
        </div>

        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">

                <!-- Small Cap -->
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu"></span>
                </li>

                <!-- Dashboard -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i class="ti ti-atom"></i>
                        <span class="hide-menu">{{ __('aside.Dashboard') }}</span>
                    </a>
                </li>

                @php
                    $webUser = Auth::guard('web')->user();
                @endphp

                {{-- Admin Guard --}}
                @if(Auth::guard('admin')->check())
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.admin.index') }}" aria-expanded="false">
                            <i class="ti ti-user"></i>
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
                  @if(Auth::guard('admin')->check() ||  ($webUser && $webUser->role?->name === 'Manager'))
   

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.clients.uploadForm') }}" aria-expanded="false">
                            <i class="ti ti-upload"></i>
                            <span class="hide-menu">{{ __('aside.admins_clients_upload') }}</span>
                        </a>
                    </li>

      
                @endif


                {{-- Sidebar items for Admin or Sales --}}
                @if(Auth::guard('admin')->check() || ($webUser && $webUser->role?->name === 'Sales') || ($webUser && $webUser->role?->name === 'Manager'))
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.clients.index') }}" aria-expanded="false">
                            <i class="ti ti-building"></i>
                            <span class="hide-menu">{{ __('aside.Clients') }}</span>
                        </a>
                    </li>

           
{{-- 
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.leads.index') }}" aria-expanded="false">
                            <i class="ti ti-target"></i>
                            <span class="hide-menu">{{ __('aside.Leads') }}</span>
                        </a>
                    </li> --}}

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.deals.index') }}" aria-expanded="false">
                            <i class="ti ti-currency-dollar"></i>
                            <span class="hide-menu">{{ __('aside.Deals') }}</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.tasks.index') }}" aria-expanded="false">
                            <i class="ti ti-list-check"></i>
                            <span class="hide-menu">{{ __('aside.Tasks') }}</span>
                        </a>
                    </li>
                @endif

                <!-- Logout -->
                <li class="sidebar-item mt-3">
                    @if(Auth::guard('admin')->check())
                        <a class="sidebar-link" href="{{ route('admin.admin.logout') }}" aria-expanded="false">
                            <i class="ti ti-power"></i>
                            <span class="hide-menu">{{ __('aside.Logout') }}</span>
                        </a>
                    @else
                        <a class="sidebar-link" href="{{ route('user.logout') }}" aria-expanded="false"
                            onclick="event.preventDefault(); document.getElementById('user-logout-form').submit();">
                            <i class="ti ti-power"></i>
                            <span class="hide-menu">{{ __('aside.Logout') }}</span>
                        </a>
                        <form method="POST" action="{{ route('user.logout') }}" id="user-logout-form" class="d-none">
                            @csrf
                        </form>
                    @endif
                </li>

            </ul>
        </nav>
    </div>
</aside>


<!-- Sidebar Toggle Styles -->
<style>
    .left-sidebar {
        transition: transform 0.3s ease;
        z-index: 9999;
        /* Keep above content */
    }

    .left-sidebar.closed {
        transform: translateX(-100%);
    }

    .left-sidebar.rtl-sidebar.closed {
        transform: translateX(100%);
    }
</style>

<!-- Sidebar Toggle Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebarToggler = document.getElementById("sidebarCollapse");
        const sidebar = document.querySelector(".left-sidebar");

        if (sidebarToggler && sidebar) {
            sidebarToggler.addEventListener("click", function () {
                sidebar.classList.toggle("closed");
            });
        }
    });
</script>