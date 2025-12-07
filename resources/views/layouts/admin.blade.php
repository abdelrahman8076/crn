<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Webinar' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css" rel="stylesheet">

 @if(app()->getLocale() == 'ar')
<style>
    /* Right sidebar (Arabic) transition */
    .left-sidebar {
        right: 0;
        left: auto;
        transition: transform 0.3s ease;
        transform: translateX(250px); /* hidden by default (off-screen to right) */
    }

    /* Show when active */
    .left-sidebar.active {
        transform: translateX(0);
    }

    /* Push content left when sidebar is active */
    .sidebar-active .main-content {
        margin-right: 250px;
        transition: margin-right 0.3s ease;
    }
</style>
@else
<style>
    /* Left sidebar (English) transition */
    .left-sidebar {
        left: 0;
        right: auto;
        transition: transform 0.3s ease;
        transform: translateX(-250px); /* hidden by default (off-screen to left) */
    }

    /* Show when active */
    .left-sidebar.active {
        transform: translateX(0);
    }

    /* Push content right when sidebar is active */
    .sidebar-active .main-content {
        margin-left: 250px;
        transition: margin-left 0.3s ease;
    }
</style>
@endif


</head>

<body class="d-flex flex-column min-vh-100">
    <x-admin.header />

    {{-- Layout Wrapper --}}
    <div class="d-flex" style="flex: 1;">
        {{-- Sidebar --}}
        <x-admin.sidebar />

        {{-- Main Area --}}
        <div class="flex-grow-1 d-flex flex-column main-content">
            {{-- Navbar --}}
            <x-admin.navbar />



            {{-- Main Content --}}
            <main class="flex-grow-1 container py-4 mt-5">
                {{-- Toggle Button --}}

                @yield('content')
            </main>
        </div>
    </div>

    <x-admin.footer />

    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            const sidebar = document.querySelector('.left-sidebar');
            sidebar.classList.toggle('active');
            document.body.classList.toggle('sidebar-active');
        });
    </script>
</body>

</html>