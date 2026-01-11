<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>CRM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css" rel="stylesheet">

    <style>
        /* Base Sidebar Styles */
        .left-sidebar {
            position: fixed;
            top: 0;
            height: 100vh;
            width: 270px;
            z-index: 1050;
            background: #fff;
            transition: transform 0.3s ease;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        @media (min-width: 1200px) {
            .left-sidebar { transform: translateX(0) !important; }
            .main-content { margin-left: 270px; transition: 0.3s; }
            .rtl-sidebar + .main-content { margin-left: 0; margin-right: 270px; }
        }

        /* Mobile Behavior (Hidden by default) */
        @media (max-width: 1199.98px) {
            /* LTR Hidden (Left) */
            .left-sidebar { left: 0; transform: translateX(-100%); }
            /* RTL Hidden (Right) */
            .left-sidebar.rtl-sidebar { left: auto; right: 0; transform: translateX(100%); }

            /* Show when Active */
            .left-sidebar.active { transform: translateX(0) !important; }
            
            .main-content { margin: 0 !important; }
        }

        /* Body overlay prevent scroll */
        body.sidebar-active { overflow: hidden; }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <x-admin.header />

    <div class="d-flex" style="flex: 1;">
        <x-admin.sidebar />

        <div class="flex-grow-1 d-flex flex-column main-content">
            <x-admin.navbar />

            <main class="flex-grow-1 container py-4 mt-5">
                @yield('content')
            </main>
        </div>
    </div>

    <x-admin.footer />

    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.querySelector('.left-sidebar');
            const openBtn = document.getElementById('sidebarToggle'); // Hamburger icon
            const closeBtn = document.getElementById('sidebarClose');  // X icon in sidebar

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                document.body.classList.toggle('sidebar-active');
            }

            // Click Hamburger
            if (openBtn) {
                openBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Click X button
            if (closeBtn) {
                closeBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleSidebar();
                });
            }

            // Optional: Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const isClickInside = sidebar.contains(event.target);
                const isToggleBtn = openBtn && openBtn.contains(event.target);

                if (!isClickInside && !isToggleBtn && sidebar.classList.contains('active')) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>