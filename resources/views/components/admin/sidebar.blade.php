<aside class="left-sidebar @if(app()->getLocale() == 'ar') rtl-sidebar @endif">
    <!-- Sidebar scroll-->
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

                <!-- Users -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.users.index') }}" aria-expanded="false">
                        <i class="ti ti-users"></i>
                        <span class="hide-menu">{{ __('aside.Users') }}</span>
                    </a>
                </li>

                <!-- Roles -->
           

                <!-- Admins -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.admin.index') }}" aria-expanded="false">
                        <i class="ti ti-user"></i>
                        <span class="hide-menu">{{ __('aside.admin_index') }}</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.admin.create') }}" aria-expanded="false">
                        <i class="ti ti-user-plus"></i>
                        <span class="hide-menu">{{ __('aside.admin_create') }}</span>
                    </a>
                </li>

                <!-- Clients -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.clients.index') }}" aria-expanded="false">
                        <i class="ti ti-building"></i>
                        <span class="hide-menu">{{ __('aside.Clients') }}</span>
                    </a>
                </li>

                <!-- Client Upload -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.clients.uploadForm') }}" aria-expanded="false">
                        <i class="ti ti-upload"></i>
                        <span class="hide-menu">{{ __('aside.admins_clients_upload') }}</span>
                    </a>
                </li>

                <!-- Leads -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.leads.index') }}" aria-expanded="false">
                        <i class="ti ti-target"></i>
                        <span class="hide-menu">{{ __('aside.Leads') }}</span>
                    </a>
                </li>

                <!-- Deals -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.deals.index') }}" aria-expanded="false">
                        <i class="ti ti-handshake"></i>
                        <span class="hide-menu">{{ __('aside.Deals') }}</span>
                    </a>
                </li>

                <!-- Tasks -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.tasks.index') }}" aria-expanded="false">
                        <i class="ti ti-list-check"></i>
                        <span class="hide-menu">{{ __('aside.Tasks') }}</span>
                    </a>
                </li>

                <!-- Notes -->
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.notes.index') }}" aria-expanded="false">
                        <i class="ti ti-notes"></i>
                        <span class="hide-menu">{{ __('aside.Notes') }}</span>
                    </a>
                </li>

                <!-- Divider -->
                <li><span class="sidebar-divider lg"></span></li>

                <!-- Apps Section -->
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">{{ __('aside.Apps') }}</span>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<!-- Sidebar Toggle Styles -->
<style>
.left-sidebar {
    transition: transform 0.3s ease;
    z-index: 9999; /* Keep above content */
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
