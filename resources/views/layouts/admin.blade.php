<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>NexusCRM | Smart Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
                <x-admin.header />

    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --nexus-primary: #0f172a; 
            --nexus-accent: #6366f1;  
            --nexus-bg: #f8fafc;
            --nexus-sidebar-width: 280px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--nexus-bg);
            color: #1e293b;
            margin: 0;
        }

        /* Sidebar Design */
        .left-sidebar {
            position: fixed;
            top: 0;
            height: 100vh;
            width: var(--nexus-sidebar-width);
            z-index: 1050;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-inline-end: 1px solid #e2e8f0;
        }

        /* RTL/LTR Logic */
        [dir="ltr"] .left-sidebar { left: 0; }
        [dir="rtl"] .left-sidebar { right: 0; }

        @media (min-width: 1200px) {
            .main-content { margin-left: var(--nexus-sidebar-width); }
            [dir="rtl"] .main-content { margin-left: 0; margin-right: var(--nexus-sidebar-width); }
        }

        /* Mobile Sidebar */
        @media (max-width: 1199.98px) {
            .left-sidebar { transform: translateX(-100%); }
            [dir="rtl"] .left-sidebar { transform: translateX(100%); }
            .left-sidebar.active { transform: translateX(0) !important; }
        }

        .nexus-navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .card {
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }

        .bg-soft-warning { background-color: rgba(255, 193, 7, 0.12) !important; }
        .bg-soft-primary { background-color: rgba(99, 102, 241, 0.1) !important; }
        
        /* Form Styling */
        .form-control:focus, .form-select:focus {
            border-color: var(--nexus-accent);
            box-shadow: 0 0 0 0.25rem rgba(99, 102, 241, 0.1);
        }

        .form-control:disabled, .form-select:disabled {
            background-color: #f1f5f9;
            cursor: not-allowed;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        
            <x-admin.sidebar />

        <div class="flex-grow-1 main-content">
                            <x-admin.navbar />


            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // document.addEventListener("DOMContentLoaded", function () {
        //     const sidebar = document.querySelector('.left-sidebar');
        //     const toggle = document.getElementById('sidebarToggle');
        //     const close = document.getElementById('sidebarClose');

        //     const toggleAction = () => {
        //         sidebar.classList.toggle('active');
        //     };

        //     if(toggle) toggle.addEventListener('click', toggleAction);
        //     if(close) close.addEventListener('click', toggleAction);
        // });
    </script>
</body>
</html>