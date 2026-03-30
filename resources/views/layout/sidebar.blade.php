{{--
    layout/sidebar.blade.php
    CSS & JS sudah dipindah ke layout/app.blade.php supaya tidak duplikat.
    Di-include via: @include('layout.sidebar')
--}}

@php
    $currentRoute = Route::currentRouteName() ?? '';
@endphp

{{-- ===================== SIDEBAR ===================== --}}
<div id="sidebar" class="bg-gray-900 text-white h-screen fixed left-0 top-0 overflow-y-auto z-40 flex flex-col">

    {{-- Logo --}}
    <div class="p-5 border-b border-gray-800 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </div>
            <div class="sidebar-text overflow-hidden">
                <h1 class="font-bold text-base leading-tight whitespace-nowrap">ToolRental</h1>
                <p class="text-xs text-gray-500">Pro</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="p-3 space-y-0.5 flex-1 overflow-y-auto">

        {{-- ── DASHBOARD ── --}}
        <a id="dashboard-link" href="{{ route('dashboard') }}"
            class="nav-item block px-4 py-2.5 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition flex items-center gap-3 {{ $currentRoute === 'dashboard' ? 'active-nav' : '' }}">
            <svg class="nav-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                    d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z" />
            </svg>
            <span class="sidebar-text font-medium">Dashboard</span>
            <span class="nav-tooltip">Dashboard</span>
        </a>

        {{-- ── MASTER DATA ── --}}
        @php
            $masterRoutes = [
                'master.tools',
                'master.categories',
                'master.warehouses',
                'master.customers',
                'master.drivers',
                'master.pricing',
                'master.users',
            ];
            $masterActive = in_array($currentRoute, $masterRoutes);
        @endphp
        <div class="pt-2" data-section="master">
            <div class="nav-section-header {{ $masterActive ? 'section-has-active' : '' }}" id="header-master"
                onclick="toggleNavSection('master')">
                <p class="nav-section-label">Master Data</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-master" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-master">
                @php
                    $masterItems = [
                        [
                            'master.tools',
                            'Tools',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
                        ],
                        [
                            'master.categories',
                            'Categories',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>',
                        ],
                        [
                            'master.warehouses',
                            'Warehouses',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9.5L12 4l9 5.5V19a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5zM9 21V12h6v9"/></svg>',
                        ],
                        [
                            'master.customers',
                            'Customers',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>',
                        ],
                        [
                            'master.drivers',
                            'Drivers',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 13l2-5h14l2 5M5 13h14M6 18a2 2 0 100-4 2 2 0 000 4zM18 18a2 2 0 100-4 2 2 0 000 4z"/></svg>',
                        ],
                        [
                            'master.pricing',
                            'Pricing',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        ],
                        [
                            'master.users',
                            'Users',
                            '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                        ],
                    ];
                @endphp
                @foreach ($masterItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ── WAREHOUSE STOCK ── --}}
        @php
            $stockRoutes = ['stock.overview', 'stock.movement'];
            $stockActive = in_array($currentRoute, $stockRoutes);
        @endphp
        <div class="pt-2" data-section="stock">
            <div class="nav-section-header {{ $stockActive ? 'section-has-active' : '' }}" id="header-stock"
                onclick="toggleNavSection('stock')">
                <p class="nav-section-label">Warehouse Stock</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-stock" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-stock">
                @php $stockItems = [['stock.overview', 'Stock Overview', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'], ['stock.movement', 'Stock Movement', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>']]; @endphp
                @foreach ($stockItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ── RENTAL TRANSACTIONS ── --}}
        @php
            $rentalRoutes = ['transactions.rentals', 'transactions.rentals.form'];
            $rentalActive = in_array($currentRoute, $rentalRoutes);
        @endphp
        <div class="pt-2" data-section="rentals">
            <div class="nav-section-header {{ $rentalActive ? 'section-has-active' : '' }}" id="header-rentals"
                onclick="toggleNavSection('rentals')">
                <p class="nav-section-label">Rental Transactions</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-rentals" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-rentals">
                @php $rentalItems = [['transactions.rentals', 'Rental List', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>'], ['transactions.rentals.form', 'Create Rental', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>']]; @endphp
                @foreach ($rentalItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ── SHIPPING ── --}}
        @php
            $shippingRoutes = ['shipping.list', 'shipping.form'];
            $shippingActive = in_array($currentRoute, $shippingRoutes);
        @endphp
        <div class="pt-2" data-section="shipping">
            <div class="nav-section-header {{ $shippingActive ? 'section-has-active' : '' }}" id="header-shipping"
                onclick="toggleNavSection('shipping')">
                <p class="nav-section-label">Shipping</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-shipping" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-shipping">
                @php $shippingItems = [['shipping.list', 'Shipping List', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>'], ['shipping.form', 'Create Delivery', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>']]; @endphp
                @foreach ($shippingItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ── RENTAL MONITORING ── --}}
        @php $monitorActive = $currentRoute === 'monitoring.active'; @endphp
        <div class="pt-2" data-section="monitoring">
            <div class="nav-section-header {{ $monitorActive ? 'section-has-active' : '' }}" id="header-monitoring"
                onclick="toggleNavSection('monitoring')">
                <p class="nav-section-label">Rental Monitoring</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-monitoring" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-monitoring">
                <a href="{{ route('monitoring.active') }}"
                    class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $monitorActive ? 'active-nav' : '' }}">
                    <svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="sidebar-text">Active Rentals</span>
                    <span class="nav-tooltip">Active Rentals</span>
                </a>
            </div>
        </div>

        {{-- ── RETURNS ── --}}
        @php
            $returnsRoutes = ['returns.tools', 'returns.form'];
            $returnsActive = in_array($currentRoute, $returnsRoutes);
        @endphp
        <div class="pt-2" data-section="returns">
            <div class="nav-section-header {{ $returnsActive ? 'section-has-active' : '' }}" id="header-returns"
                onclick="toggleNavSection('returns')">
                <p class="nav-section-label">Returns</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-returns" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-returns">
                @php $returnsItems = [['returns.tools', 'Return Tools', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>'], ['returns.form', 'Return Form', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>']]; @endphp
                @foreach ($returnsItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ── SPECIAL STATUS ── --}}
        @php
            $specialRoutes = ['lost.tools', 'sold.tools'];
            $specialActive = in_array($currentRoute, $specialRoutes);
        @endphp
        <div class="pt-2" data-section="special">
            <div class="nav-section-header {{ $specialActive ? 'section-has-active' : '' }}" id="header-special"
                onclick="toggleNavSection('special')">
                <p class="nav-section-label">Special Status</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-special" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-special">
                @php $specialItems = [['lost.tools', 'Lost Tools', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'], ['sold.tools', 'Sold Tools', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>']]; @endphp
                @foreach ($specialItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ── REPORTS ── --}}
        @php
            $reportsRoutes = ['reports.rental', 'reports.revenue', 'reports.inventory'];
            $reportsActive = in_array($currentRoute, $reportsRoutes);
        @endphp
        <div class="pt-2" data-section="reports">
            <div class="nav-section-header {{ $reportsActive ? 'section-has-active' : '' }}" id="header-reports"
                onclick="toggleNavSection('reports')">
                <p class="nav-section-label">Reports</p>
                <span class="section-active-dot"></span>
                <svg class="nav-section-chevron open" id="chevron-reports" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <div class="nav-section-items" id="section-reports">
                @php $reportsItems = [['reports.rental', 'Rental Report', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'], ['reports.revenue', 'Revenue Report', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>'], ['reports.inventory', 'Inventory Report', '<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>']]; @endphp
                @foreach ($reportsItems as [$route, $label, $icon])
                    <a href="{{ route($route) }}"
                        class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 {{ $currentRoute === $route ? 'active-nav' : '' }}">
                        {!! $icon !!}
                        <span class="sidebar-text">{{ $label }}</span>
                        <span class="nav-tooltip">{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </div>

    </nav>

    {{-- Logout --}}
    <div class="p-3 border-t border-gray-800 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span class="sidebar-text">Logout</span>
            </button>
        </form>
    </div>
</div>

{{-- Mobile overlay --}}
<div id="sidebar-overlay"></div>
