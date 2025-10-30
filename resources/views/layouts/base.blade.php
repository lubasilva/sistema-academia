<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Academia'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        /* Mobile-First Dashboard Styles */
        :root {
            --primary: #6366f1;
            --sidebar-width: 250px;
        }
        
        body { overflow-x: hidden; }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: -100%;
            height: 100vh;
            width: 80%;
            max-width: var(--sidebar-width);
            transition: left 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }
        
        .sidebar.show {
            left: 0;
        }
        
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Content */
        .content-wrapper {
            width: 100%;
            min-height: 100vh;
        }
        
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        
        main {
            padding: 1rem;
        }
        
        /* Cards mobile */
        .card {
            margin-bottom: 1rem;
            border-radius: 0.75rem;
        }
        
        /* Desktop */
        @media (min-width: 992px) {
            .sidebar {
                position: fixed;
                left: 0;
                width: var(--sidebar-width);
            }
            
            .content-wrapper {
                margin-left: var(--sidebar-width);
                width: calc(100% - var(--sidebar-width));
            }
            
            .mobile-menu-toggle {
                display: none !important;
            }
            
            .sidebar-overlay {
                display: none;
            }
            
            main {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body class="bg-light">
    @auth
    <div class="d-flex">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        @include('partials.sidebar')
        <div class="content-wrapper">
            @include('partials.navbar')
            <main>
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>
    @else
        @yield('body')
    @endauth
    
    <script>
        // Mobile sidebar toggle
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('mobileMenuToggle');
        
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        
        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }
        
        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }
        
        // Close sidebar when clicking nav link on mobile
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    toggleSidebar();
                }
            });
        });
    </script>
    
    <!-- FullCalendar -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/pt-br.global.min.js'></script>
    
    @stack('scripts')
</body>
</html>