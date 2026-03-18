<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tool Rental Management')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <style>
        /* ===== MODAL ===== */
        .modal { display: none; }
        .modal.active { display: flex; }
        .modal-enter { animation: modalEnter 0.3s ease-out; }
        @keyframes modalEnter {
            from { opacity: 0; transform: scale(0.95); }
            to   { opacity: 1; transform: scale(1); }
        }

        /* ===== SIDEBAR TRANSITION ===== */
        #sidebar {
            transition: width 300ms ease-in-out, transform 300ms ease-in-out;
            scrollbar-width: none;
            width: 256px;    /* w-64 */
        }
        #sidebar::-webkit-scrollbar { display: none; }

        /* Collapsed state — desktop */
        #sidebar.collapsed { width: 88px; }

        /* ===== MAIN CONTENT pushes right ===== */
        #main-content {
            transition: margin-left 300ms ease-in-out;
            margin-left: 256px; /* default: sidebar open */
        }
        #main-content.sidebar-collapsed { margin-left: 88px; }

        /* ===== Hide text when collapsed ===== */
        #sidebar.collapsed .sidebar-text {
            opacity: 0; visibility: hidden; width: 0; overflow: hidden; white-space: nowrap;
        }
        #sidebar.collapsed .nav-section-label  { display: none; }
        #sidebar.collapsed .nav-section-chevron { display: none; }
        #sidebar.collapsed .nav-section-items  { overflow: hidden; }

        #sidebar.collapsed .nav-section-header {
            justify-content: center; align-items: center; padding: 4px 0;
            border-left: none !important; background: transparent !important;
            cursor: pointer; pointer-events: auto; position: relative;
        }
        #sidebar.collapsed .nav-section-header:hover {
            background: rgba(255,255,255,0.04) !important; border-radius: 6px;
        }
        #sidebar.collapsed .nav-section-label { display: none !important; }
        #sidebar.collapsed .nav-section-header::after {
            content: ''; display: block; width: 14px; height: 14px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%234b5563' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: center; background-size: contain;
            transition: transform 200ms ease;
        }
        #sidebar.collapsed .nav-section-header.is-collapsed-section::after { transform: rotate(-90deg); }
        #sidebar.collapsed .nav-section-header.section-has-active::after {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%233b82f6' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        }
        .section-active-dot { display: none; }

        #sidebar.collapsed .nav-item {
            justify-content: center; padding: 9px 0 !important; border-left: none !important;
        }
        #sidebar.collapsed .nav-item.active-nav {
            border-left: none !important; padding-left: 0 !important;
            border-radius: 10px; margin: 2px 10px;
            background: rgba(59,130,246,0.15) !important;
        }
        #sidebar.collapsed .nav-item { position: relative; }
        #sidebar.collapsed .nav-item.active-nav::before {
            content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
            width: 3px; height: 70%; background: #3b82f6; border-radius: 0 3px 3px 0;
        }
        #sidebar.collapsed .nav-item .nav-tooltip { display: block; }

        /* ===== TOOLTIP ===== */
        .nav-tooltip {
            display: none; position: absolute; left: calc(100% + 12px); top: 50%;
            transform: translateY(-50%); background: #1e293b; color: #e2e8f0;
            font-size: 12px; font-weight: 500; padding: 5px 10px; border-radius: 6px;
            white-space: nowrap; pointer-events: none; z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.08);
        }
        .nav-tooltip::before {
            content: ''; position: absolute; right: 100%; top: 50%; transform: translateY(-50%);
            border: 5px solid transparent; border-right-color: #1e293b;
        }

        /* ===== MOBILE OVERLAY ===== */
        #sidebar-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            opacity: 0; visibility: hidden; transition: opacity 300ms ease; z-index: 30;
        }
        #sidebar-overlay.active { opacity: 1; visibility: visible; }

        /* ===== MOBILE ===== */
        @media (max-width: 768px) {
            #sidebar {
                width: 256px !important;
                transform: translateX(-256px);
                z-index: 40;
            }
            #sidebar.mobile-open { transform: translateX(0); }
            #main-content,
            #main-content.sidebar-collapsed { margin-left: 0 !important; }

            .sidebar-text {
                opacity: 1 !important; visibility: visible !important;
                width: auto !important; display: inline !important;
            }
            .nav-section-label { display: block !important; }
            .sidebar-toggle-desktop { display: none !important; }
            .sidebar-toggle-mobile  { display: flex !important; }
        }
        @media (min-width: 769px) {
            #sidebar { transform: none !important; position: fixed; left: 0; top: 0; }
            #sidebar-overlay { display: none !important; }
            .sidebar-toggle-mobile  { display: none !important; }
            .sidebar-toggle-desktop { display: flex !important; }
        }
        @media (max-width: 768px) { .header-search { display: none !important; } }

        /* ===== COLLAPSIBLE SECTIONS ===== */
        .nav-section-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 6px 14px; cursor: pointer; user-select: none; border-radius: 6px;
            transition: background 150ms ease, border-left-color 150ms ease;
            margin-bottom: 2px; border-left: 3px solid transparent;
        }
        .nav-section-header:hover { background: rgba(255,255,255,0.05); }
        .nav-section-label {
            font-size: 10px; font-weight: 700; color: #4b5563;
            text-transform: uppercase; letter-spacing: 0.08em; transition: color 150ms ease;
        }
        .nav-section-header.section-has-active .nav-section-label { color: #60a5fa; }
        .nav-section-header.section-has-active {
            border-left-color: #3b82f6; background: rgba(59,130,246,0.08);
        }
        .nav-section-chevron {
            width: 13px; height: 13px; color: #4b5563;
            transition: transform 200ms ease, color 150ms ease; flex-shrink: 0;
        }
        .nav-section-header.section-has-active .nav-section-chevron { color: #60a5fa; }
        .nav-section-chevron.open { transform: rotate(180deg); }
        .nav-section-items {
            overflow: hidden; transition: max-height 250ms ease, opacity 200ms ease;
            max-height: 500px; opacity: 1;
        }
        .nav-section-items.closed { max-height: 0; opacity: 0; }

        /* ===== ACTIVE NAV ITEM ===== */
        a.nav-item.active-nav {
            background: rgba(59,130,246,0.15) !important; color: #93c5fd !important;
            border-left: 3px solid #3b82f6 !important; padding-left: 13px !important;
        }
        a.nav-item.active-nav .sidebar-text { color: #93c5fd; font-weight: 600; }
        a.nav-item.active-nav .nav-icon     { color: #3b82f6 !important; }
        a.nav-item.active-nav .nav-dot {
            background: #3b82f6 !important; width: 6px !important; height: 6px !important;
            box-shadow: 0 0 6px rgba(59,130,246,0.8);
        }
        .nav-icon { color: #6b7280; transition: color 150ms ease; flex-shrink: 0; }
        a.nav-item:hover .nav-icon { color: #9ca3af; }
        a#dashboard-link.active-nav .nav-icon { color: #3b82f6 !important; }
    </style>
</head>

<body class="bg-gray-100">

    {{-- ===================== SIDEBAR ===================== --}}
    @include('layout.sidebar')

    {{-- ===================== MAIN CONTENT ===================== --}}
    <div id="main-content" class="flex flex-col min-h-screen">

        {{-- Top Bar --}}
        @include('layout.topbar')

        {{-- Page Content --}}
        <main class="flex-1 p-4 md:p-8">
            @yield('content')
        </main>

        @include('layout.modal')

    </div>

    {{-- ===================== SIDEBAR JS ===================== --}}
    <script>
        // ===== Collapsible sections =====
        window.toggleNavSection = function(id) {
            const items   = document.getElementById('section-'  + id);
            const chevron = document.getElementById('chevron-'  + id);
            const header  = document.getElementById('header-'   + id);
            if (!items) return;

            const isClosed = items.classList.contains('closed');
            items.classList.toggle('closed', !isClosed);
            if (chevron) chevron.classList.toggle('open', isClosed);
            if (header)  header.classList.toggle('is-collapsed-section', !isClosed);

            const saved = JSON.parse(localStorage.getItem('navSections') || '{}');
            saved[id] = !isClosed ? 'closed' : 'open';
            localStorage.setItem('navSections', JSON.stringify(saved));
        };

        // ===== Restore saved collapse states =====
        (function restoreNavSections() {
            const saved    = JSON.parse(localStorage.getItem('navSections') || '{}');
            const sections = ['master','stock','rentals','shipping','monitoring','returns','special','reports'];
            sections.forEach(id => {
                const header    = document.getElementById('header-'  + id);
                const hasActive = header && header.classList.contains('section-has-active');
                if (saved[id] === 'closed' && !hasActive) {
                    const items   = document.getElementById('section-' + id);
                    const chevron = document.getElementById('chevron-' + id);
                    if (items)   items.classList.add('closed');
                    if (chevron) chevron.classList.remove('open');
                    if (header)  header.classList.add('is-collapsed-section');
                }
            });
        })();

        document.addEventListener('DOMContentLoaded', function () {
            const sidebar        = document.getElementById('sidebar');
            const mainContent    = document.getElementById('main-content');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const desktopToggle  = document.getElementById('sidebar-toggle-desktop');
            const mobileToggle   = document.getElementById('sidebar-toggle-mobile');

            let isCollapsed  = false;
            let isMobileOpen = false;

            // ── Restore desktop collapsed state ──
            if (localStorage.getItem('sidebarCollapsed') === 'true' && window.innerWidth > 768) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
                isCollapsed = true;
            }

            // ── Desktop hamburger: collapse/expand ──
            desktopToggle && desktopToggle.addEventListener('click', () => {
                isCollapsed = !isCollapsed;
                sidebar.classList.toggle('collapsed', isCollapsed);
                mainContent.classList.toggle('sidebar-collapsed', isCollapsed);
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            });

            // ── Mobile hamburger: slide in/out ──
            mobileToggle && mobileToggle.addEventListener('click', () => {
                isMobileOpen = !isMobileOpen;
                sidebar.classList.toggle('mobile-open', isMobileOpen);
                sidebarOverlay && sidebarOverlay.classList.toggle('active', isMobileOpen);
            });

            // ── Close on overlay click ──
            sidebarOverlay && sidebarOverlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
                isMobileOpen = false;
            });

            // ── Close sidebar when nav link clicked (mobile) ──
            document.querySelectorAll('#sidebar a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= 768) {
                        sidebar.classList.remove('mobile-open');
                        sidebarOverlay && sidebarOverlay.classList.remove('active');
                        isMobileOpen = false;
                    }
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay && sidebarOverlay.classList.remove('active');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>