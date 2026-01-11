<body>
  <!-- Body Wrapper -->
  <div class="page-wrapper mt-5 mb-5" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!-- App Topstrip -->
    <div class="app-topstrip bg-dark py-3 px-4 w-100 d-flex flex-column flex-lg-row align-items-center justify-content-between">
      
      <!-- Left Logo & Hamburger -->
      <div class="d-flex align-items-center mb-2 mb-lg-0 gap-3">
        
        <!-- Hamburger Menu Button -->
        <a class="nav-link sidebar-toggle text-white " href="javascript:void(0)"id="sidebarToggle">
          <i class="ti ti-menu-2 fs-4 ml-5"></i>
        </a>

        <!-- Logo -->
        <a href="#" class="d-flex justify-content-center">
          <img src="{{ asset('assets/images/logos/logo-wrappixel.svg') }}" alt="Logo" width="150">
        </a>
      </div>

      <!-- Right Content Section -->
      <div class="d-flex flex-column flex-lg-row align-items-center gap-2 text-center text-lg-start">

        <!-- Language Switcher -->
        <div class="d-flex align-items-center gap-2 text-white">
          <a class="text-white text-decoration-none" href="{{ route('locale.switch', 'en') }}">English</a>
          <span>|</span>
          <a class="text-white text-decoration-none" href="{{ route('locale.switch', 'ar') }}">العربية</a>
        </div>
      </div>
    </div>
    <!-- End App Topstrip -->


    <!-- Sidebar -->
    {{-- <aside id="sidebar" class="sidebar bg-dark text-white">
      <div class="sidebar-header d-flex justify-content-between align-items-center p-2">
        <h5 class="mb-0">Menu</h5>
        <a href="javascript:void(0)" class="sidebar-close text-white">
          <i class="ti ti-x fs-5"></i>
        </a>
      </div>
      <div class="p-3">
        Sidebar content here
      </div>
    </aside> --}}

    <style>
      .sidebar {
        position: fixed;
        top: 0;
        left: -260px;
        width: 260px;
        height: 100%;
        transition: left 0.3s ease-in-out;
        z-index: 1050;
      }
      .sidebar.active {
        left: 0;
      }
    </style>



