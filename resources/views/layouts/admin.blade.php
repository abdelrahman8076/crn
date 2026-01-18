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
        /* Container styling for Dark Mode */


/* Force the calendar icon to appear and turn white */
.dark-date-input::-webkit-calendar-picker-indicator {
    cursor: pointer;
    filter: invert(1); /* This turns the black icon white */
    opacity: 0.7;
    transition: opacity 0.2s;
}

.dark-date-input::-webkit-calendar-picker-indicator:hover {
    opacity: 1;
}

/* Adjusting for Firefox */
.dark-date-input {
    color-scheme: dark;
}
        /* Ensures the datalist input matches your theme exactly */
input[list].form-control {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%236c757d' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

/* Removes the default arrow in some browsers so it doesn't double-up */
input::-webkit-calendar-picker-indicator {
    opacity: 0;
}
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

        /* RTL Fixes for Input Groups - Fix semi-circle border-radius issues */
        /* In LTR: input-group-text (first child) has left rounded corners, form-control has right rounded corners */
        /* In RTL: input-group-text (first child) should have right rounded corners, form-control should have left rounded corners */
        
        [dir="rtl"] .input-group > .input-group-text:first-child {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-top-right-radius: var(--bs-border-radius, 0.375rem) !important;
            border-bottom-right-radius: var(--bs-border-radius, 0.375rem) !important;
        }

        [dir="rtl"] .input-group > .form-control:not(:first-child),
        [dir="rtl"] .input-group > .form-select:not(:first-child) {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            border-top-left-radius: var(--bs-border-radius, 0.375rem) !important;
            border-bottom-left-radius: var(--bs-border-radius, 0.375rem) !important;
        }

        /* Fix border-end-0 and border-start-0 for RTL */
        [dir="rtl"] .border-end-0 {
            border-left: 0 !important;
            border-right: 1px solid var(--bs-border-color, #dee2e6) !important;
        }

        [dir="rtl"] .border-start-0 {
            border-right: 0 !important;
            border-left: 1px solid var(--bs-border-color, #dee2e6) !important;
        }

        /* Ensure input-group-text border-end-0 works correctly in RTL */
        [dir="rtl"] .input-group-text.border-end-0 {
            border-left: 0 !important;
        }

        /* Ensure form-control border-start-0 works correctly in RTL */
        [dir="rtl"] .form-control.border-start-0 {
            border-right: 0 !important;
        }

        /* Fix padding adjustments for RTL input groups */
        [dir="rtl"] .input-group > .form-control.border-start-0.ps-0 {
            padding-right: 0 !important;
            padding-left: 0.75rem !important;
        }

        /* Fix for input-group-merge class if used */
        [dir="rtl"] .input-group-merge > .input-group-text:first-child {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-top-right-radius: var(--bs-border-radius, 0.375rem) !important;
            border-bottom-right-radius: var(--bs-border-radius, 0.375rem) !important;
        }

        /* ============================================
           DARK MODE STYLES
           ============================================ */
        [data-bs-theme="dark"] {
            color-scheme: dark;
        }
            [data-bs-theme="dark"] .dark-date-input {
            color-scheme: white;
        }

        /* Body & Background */
        [data-bs-theme="dark"] body {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .page-wrapper {
            background-color: #0f172a !important;
        }

        /* Sidebar Styles */
        [data-bs-theme="dark"] .left-sidebar {
            background: #1e293b !important;
            border-inline-end-color: #334155 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .text-dark,
        [data-bs-theme="dark"] .left-sidebar .text-muted {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .sidebar-link {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .sidebar-link:hover {
            background: #334155 !important;
            color: #818cf8 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .sidebar-link.active {
            background: #334155 !important;
            color: #818cf8 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .nav-small-cap {
            color: #94a3b8 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .brand-logo {
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .role-card {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .left-sidebar .role-card .text-muted {
            color: #cbd5e1 !important;
        }

        /* Cards */
        [data-bs-theme="dark"] .card {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .card-header {
            background-color: #1e293b !important;
            border-bottom-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .card-body {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .card-title {
            color: #e2e8f0 !important;
        }

        /* Forms */
        [data-bs-theme="dark"] .form-control,
        [data-bs-theme="dark"] .form-select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .form-control:focus,
        [data-bs-theme="dark"] .form-select:focus {
            background-color: #0f172a !important;
            border-color: #6366f1 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .input-group-text {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .form-label {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .form-text {
            color: #cbd5e1 !important;
        }

        /* Text Colors */
        [data-bs-theme="dark"] .text-dark {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .text-muted {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .text-secondary {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] small,
        [data-bs-theme="dark"] .small {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] h1,
        [data-bs-theme="dark"] h2,
        [data-bs-theme="dark"] h3,
        [data-bs-theme="dark"] h4,
        [data-bs-theme="dark"] h5,
        [data-bs-theme="dark"] h6 {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] p {
            color: #e2e8f0 !important;
        }

        /* Data Tables - CRITICAL FIX */
        [data-bs-theme="dark"] .dataTables_wrapper {
            color: #e2e8f0 !important;
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .d-flex {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] table.dataTable {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] table.dataTable thead th {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
            border-bottom-color: #475569 !important;
        }

        [data-bs-theme="dark"] table.dataTable tbody td {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] table.dataTable tbody tr {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] table.dataTable tbody tr:hover {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] table.dataTable tbody tr:hover td {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter label {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_filter input {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length label {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_length select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_info {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button {
            color: #e2e8f0 !important;
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button a {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
            color: #fff !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.current a {
            color: #fff !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button:hover a {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #64748b !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .dataTables_paginate .paginate_button.disabled a {
            color: #64748b !important;
        }

        /* Bootstrap Pagination in Dark Mode */
        [data-bs-theme="dark"] .pagination {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .page-link {
            color: #e2e8f0 !important;
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .page-link:hover {
            color: #e2e8f0 !important;
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .page-link:focus {
            color: #e2e8f0 !important;
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        [data-bs-theme="dark"] .page-item.active .page-link {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
            color: #fff !important;
        }

        [data-bs-theme="dark"] .page-item.disabled .page-link {
            color: #64748b !important;
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        /* Buttons in DataTables */
        [data-bs-theme="dark"] .dataTables_wrapper .btn {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .btn-primary {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
            color: #fff !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .btn-secondary {
            background-color: #475569 !important;
            border-color: #475569 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .btn-outline-primary {
            color: #818cf8 !important;
            border-color: #6366f1 !important;
        }

        [data-bs-theme="dark"] .dataTables_wrapper .btn-outline-primary:hover {
            background-color: #6366f1 !important;
            border-color: #6366f1 !important;
            color: #fff !important;
        }

        /* Nexus datatable container specific */
        [data-bs-theme="dark"] .nexus-datatable-container {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .nexus-datatable-container .dataTables_wrapper {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .nexus-datatable-container table.dataTable thead th {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .nexus-datatable-container table.dataTable tbody tr:hover {
            background-color: #334155 !important;
        }

        /* Regular Tables */
        [data-bs-theme="dark"] .table {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .table thead {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .table thead th {
            color: #e2e8f0 !important;
            border-bottom-color: #475569 !important;
        }

        [data-bs-theme="dark"] .table tbody {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .table tbody td {
            color: #e2e8f0 !important;
            border-bottom-color: #334155 !important;
        }

        [data-bs-theme="dark"] .table tbody tr:hover {
            background-color: #334155 !important;
        }

        /* Buttons */
        [data-bs-theme="dark"] .btn-light {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .btn-light:hover {
            background-color: #334155 !important;
            border-color: #475569 !important;
        }

        /* Backgrounds */
        [data-bs-theme="dark"] .bg-light {
            background-color: #1e293b !important;
        }

        [data-bs-theme="dark"] .bg-white {
            background-color: #1e293b !important;
        }

        /* Borders */
        [data-bs-theme="dark"] .border-light {
            border-color: #334155 !important;
        }

        /* Dropdowns */
        [data-bs-theme="dark"] .dropdown-menu {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        [data-bs-theme="dark"] .dropdown-item {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .dropdown-item:hover {
            background-color: #334155 !important;
        }

        [data-bs-theme="dark"] .dropdown-header {
            color: #cbd5e1 !important;
        }

        /* Breadcrumbs */
        [data-bs-theme="dark"] .breadcrumb-item a {
            color: #cbd5e1 !important;
        }

        [data-bs-theme="dark"] .breadcrumb-item.active {
            color: #e2e8f0 !important;
        }

        /* Badges */
        [data-bs-theme="dark"] .badge {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .badge.bg-light {
            background-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        /* Modals */
        [data-bs-theme="dark"] .modal-content {
            background-color: #1e293b !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .modal-header {
            border-bottom-color: #334155 !important;
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .modal-title {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .modal-body {
            color: #e2e8f0 !important;
        }

        [data-bs-theme="dark"] .modal-footer {
            border-top-color: #334155 !important;
        }

        /* Progress bars */
        [data-bs-theme="dark"] .progress {
            background-color: #334155 !important;
        }

        /* Links */
        [data-bs-theme="dark"] a:not(.btn):not(.nav-lang):not(.sidebar-link) {
            color: #818cf8 !important;
        }

        [data-bs-theme="dark"] a:not(.btn):not(.nav-lang):not(.sidebar-link):hover {
            color: #a5b4fc !important;
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
        
        // Dark Mode Toggle Functionality
        (function() {
            'use strict';

            // Get the theme from localStorage or default to 'light'
            const getStoredTheme = () => localStorage.getItem('theme');
            const setStoredTheme = theme => localStorage.setItem('theme', theme);

            // Get the preferred theme
            const getPreferredTheme = () => {
                const storedTheme = getStoredTheme();
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            };

            // Set the theme
            const setTheme = theme => {
                if (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    document.documentElement.setAttribute('data-bs-theme', 'dark');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', theme);
                }
                
                // Update icon
                const themeIcon = document.getElementById('themeIcon');
                if (themeIcon) {
                    const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                    themeIcon.className = isDark ? 'ti ti-sun fs-5' : 'ti ti-moon fs-5';
                }

                // Force DataTables to redraw if they exist
                if (window.jQuery && window.jQuery.fn.dataTable) {
                    window.jQuery('.dataTable').each(function() {
                        if (window.jQuery(this).dataTable) {
                            window.jQuery(this).DataTable().draw();
                        }
                    });
                }
            };

            // Set initial theme
            setTheme(getPreferredTheme());

            // Show active theme icon on page load
            const showActiveTheme = () => {
                const themeIcon = document.getElementById('themeIcon');
                if (themeIcon) {
                    const activeTheme = document.documentElement.getAttribute('data-bs-theme');
                    themeIcon.className = activeTheme === 'dark' ? 'ti ti-sun fs-5' : 'ti ti-moon fs-5';
                }
            };

            // Listen for dark mode toggle button click
            document.addEventListener('DOMContentLoaded', function() {
                showActiveTheme();
                
                const darkModeBtn = document.getElementById('darkModeBtn');
                if (darkModeBtn) {
                    darkModeBtn.addEventListener('click', () => {
                        const currentTheme = document.documentElement.getAttribute('data-bs-theme');
                        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                        setStoredTheme(newTheme);
                        setTheme(newTheme);
                    });
                }

                // Listen for system theme changes
                window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                    const storedTheme = getStoredTheme();
                    if (!storedTheme) {
                        setTheme(getPreferredTheme());
                    }
                });
            });
        })();
    </script>
    
 
</body>
</html>