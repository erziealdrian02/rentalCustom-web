{{-- ===================== TOP BAR ===================== --}}
<header class="bg-white border-b border-gray-200 px-4 md:px-6 py-3 flex items-center justify-between sticky top-0 z-20">

    <div class="flex items-center gap-3">

        {{-- Desktop hamburger (collapse/expand sidebar) --}}
        <button id="sidebar-toggle-desktop"
            class="sidebar-toggle-desktop p-2 hover:bg-gray-100 rounded-lg transition text-gray-500 hover:text-gray-700"
            title="Toggle Sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        {{-- Mobile hamburger (slide sidebar in/out) --}}
        <button id="sidebar-toggle-mobile"
            class="sidebar-toggle-mobile p-2 hover:bg-gray-100 rounded-lg transition text-gray-500 hover:text-gray-700"
            title="Open Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <h2 id="page-title" class="text-lg md:text-xl font-bold text-gray-900">
            @yield('page_title', 'Dashboard')
        </h2>
    </div>

    <div class="flex items-center gap-2 md:gap-4">

        {{-- Search (hidden on mobile) --}}
        <div class="header-search hidden md:block">
            <input type="text" placeholder="Search..."
                class="px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-52">
        </div>

        {{-- Notifications --}}
        <button class="p-2 hover:bg-gray-100 rounded-lg transition relative" title="Notifications">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            {{-- Notification badge --}}
            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        {{-- Date --}}
        <span class="text-sm text-gray-500 hidden md:block">{{ now()->format('d M Y') }}</span>

        {{-- User avatar --}}
        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold cursor-pointer select-none"
            title="Profile">
            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
        </div>
    </div>
</header>