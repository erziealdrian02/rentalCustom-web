// Layout Components - Sidebar and Header HTML with Responsive Design + Collapsible Sections + Active State + Icons

const responsiveStyles = `
  <style>
    #sidebar {
      transition: width 300ms ease-in-out, margin-left 300ms ease-in-out;
      scrollbar-width: none;
    }

    #sidebar::-webkit-scrollbar {
      display: none;
    }

    #sidebar.collapsed {
      width: 88px;
    }

    #main-content {
      transition: margin-left 300ms ease-in-out;
    }

    #main-content.sidebar-collapsed {
      margin-left: 88px;
    }

    /* Hide text when collapsed */
    #sidebar.collapsed .sidebar-text {
      opacity: 0;
      visibility: hidden;
      width: 0;
      overflow: hidden;
      white-space: nowrap;
    }

    /* Hide section labels when collapsed */
    #sidebar.collapsed .nav-section-label {
      display: none;
    }

    /* Hide chevron when collapsed */
    #sidebar.collapsed .nav-section-chevron {
      display: none;
    }

    /* Collapsed: section items still use transition */
    #sidebar.collapsed .nav-section-items {
      overflow: hidden;
    }

    /* Section header collapsed: show as chevron button */
    #sidebar.collapsed .nav-section-header {
      justify-content: center;
      align-items: center;
      padding: 4px 0;
      border-left: none !important;
      background: transparent !important;
      cursor: pointer;
      pointer-events: auto;
      position: relative;
    }

    #sidebar.collapsed .nav-section-header:hover {
      background: rgba(255,255,255,0.04) !important;
      border-radius: 6px;
    }

    /* Hide text label when collapsed */
    #sidebar.collapsed .nav-section-label {
      display: none !important;
    }

    /* Show a small chevron icon as the section toggle when collapsed */
    #sidebar.collapsed .nav-section-header::after {
      font-size: 0;
      content: '';
      display: block;
      width: 14px;
      height: 14px;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%234b5563' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: center;
      background-size: contain;
      transition: transform 200ms ease, filter 200ms ease;
    }

    /* When section is closed (collapsed), chevron points right (►) meaning "expand" */
    #sidebar.collapsed .nav-section-header.is-collapsed-section::after {
      transform: rotate(-90deg);
    }

    /* Active section chevron is blue */
    #sidebar.collapsed .nav-section-header.section-has-active::after {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' stroke='%233b82f6' stroke-width='2.5' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
    }

    /* Active dot - hidden (replaced by chevron color) */
    .section-active-dot {
      display: none;
    }

    /* Nav items collapsed: center the icon, hide text */
    #sidebar.collapsed .nav-item {
      justify-content: center;
      padding: 9px 0 !important;
      border-left: none !important;
    }

    #sidebar.collapsed .nav-item.active-nav {
      border-left: none !important;
      padding-left: 0 !important;
      border-radius: 10px;
      margin: 2px 10px;
      background: rgba(59, 130, 246, 0.15) !important;
    }

    /* Active indicator bar on left for collapsed */
    #sidebar.collapsed .nav-item.active-nav::before {
      content: '';
      position: absolute;
      left: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 70%;
      background: #3b82f6;
      border-radius: 0 3px 3px 0;
    }

    #sidebar.collapsed .nav-item {
      position: relative;
    }

    /* Tooltip on hover when collapsed */
    #sidebar.collapsed .nav-item .nav-tooltip {
      display: block;
    }

    .nav-tooltip {
      display: none;
      position: absolute;
      left: calc(100% + 12px);
      top: 50%;
      transform: translateY(-50%);
      background: #1e293b;
      color: #e2e8f0;
      font-size: 12px;
      font-weight: 500;
      padding: 5px 10px;
      border-radius: 6px;
      white-space: nowrap;
      pointer-events: none;
      z-index: 100;
      box-shadow: 0 4px 12px rgba(0,0,0,0.4);
      border: 1px solid rgba(255,255,255,0.08);
    }

    .nav-tooltip::before {
      content: '';
      position: absolute;
      right: 100%;
      top: 50%;
      transform: translateY(-50%);
      border: 5px solid transparent;
      border-right-color: #1e293b;
    }

    #sidebar-overlay {
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      opacity: 0;
      visibility: hidden;
      transition: opacity 300ms ease-in-out;
      z-index: 30;
    }

    #sidebar-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    @media (max-width: 768px) {
      #sidebar {
        width: 256px;
        margin-left: -256px;
        z-index: 40;
      }

      #sidebar.mobile-open {
        margin-left: 0;
      }

      #main-content {
        margin-left: 0 !important;
      }

      #sidebar.collapsed {
        width: 256px;
      }

      #main-content.sidebar-collapsed {
        margin-left: 0;
      }

      .sidebar-text {
        display: inline !important;
        opacity: 1 !important;
        visibility: visible !important;
        width: auto !important;
      }

      .nav-section-label {
        display: block !important;
      }

      .sidebar-toggle-mobile {
        display: flex;
      }
    }

    @media (min-width: 769px) {
      #sidebar {
        position: fixed;
        margin-left: 0;
      }

      #sidebar-overlay {
        display: none;
      }

      .sidebar-toggle-mobile {
        display: none;
      }
    }

    @media (max-width: 768px) {
      .header-search { display: none !important; }
    }

    @media (max-width: 640px) {
      .grid { grid-template-columns: 1fr !important; }
      .md\\:grid-cols-2 { grid-template-columns: 1fr !important; }
      .lg\\:grid-cols-2 { grid-template-columns: 1fr !important; }
      .lg\\:col-span-2 { grid-column: span 1 !important; }
      .lg\\:col-span-1 { grid-column: span 1 !important; }
    }

    /* ===== COLLAPSIBLE SECTION STYLES ===== */

    .nav-section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 6px 14px;
      cursor: pointer;
      user-select: none;
      border-radius: 6px;
      transition: background 150ms ease, border-left-color 150ms ease;
      margin-bottom: 2px;
      border-left: 3px solid transparent;
    }

    .nav-section-header:hover {
      background: rgba(255,255,255,0.05);
    }

    .nav-section-label {
      font-size: 10px;
      font-weight: 700;
      color: #4b5563;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      transition: color 150ms ease;
    }

    .nav-section-header.section-has-active .nav-section-label {
      color: #60a5fa;
    }

    .nav-section-header.section-has-active {
      border-left-color: #3b82f6;
      background: rgba(59, 130, 246, 0.08);
    }

    .nav-section-chevron {
      width: 13px;
      height: 13px;
      color: #4b5563;
      transition: transform 200ms ease, color 150ms ease;
      flex-shrink: 0;
    }

    .nav-section-header.section-has-active .nav-section-chevron {
      color: #60a5fa;
    }

    .nav-section-chevron.open {
      transform: rotate(180deg);
    }

    .nav-section-items {
      overflow: hidden;
      transition: max-height 250ms ease, opacity 200ms ease;
      max-height: 500px;
      opacity: 1;
    }

    .nav-section-items.closed {
      max-height: 0;
      opacity: 0;
    }

    /* ===== ACTIVE NAV ITEM STYLES ===== */

    a.nav-item.active-nav {
      background: rgba(59, 130, 246, 0.15) !important;
      color: #93c5fd !important;
      border-left: 3px solid #3b82f6 !important;
      padding-left: 13px !important;
    }

    a.nav-item.active-nav .sidebar-text {
      color: #93c5fd;
      font-weight: 600;
    }

    a.nav-item.active-nav .nav-icon {
      color: #3b82f6 !important;
    }

    a.nav-item.active-nav .nav-dot {
      background: #3b82f6 !important;
      width: 6px !important;
      height: 6px !important;
      box-shadow: 0 0 6px rgba(59, 130, 246, 0.8);
    }

    /* Nav icon default color */
    .nav-icon {
      color: #6b7280;
      transition: color 150ms ease;
      flex-shrink: 0;
    }

    a.nav-item:hover .nav-icon {
      color: #9ca3af;
    }

    /* Dashboard link active */
    a#dashboard-link.active-nav .nav-icon {
      color: #3b82f6 !important;
    }
  </style>
`;

// SVG Icons library
const icons = {
    dashboard: `<svg class="nav-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 13a1 1 0 011-1h4a1 1 0 011 1v6a1 1 0 01-1 1h-4a1 1 0 01-1-1v-6z"/></svg>`,
    tools: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>`,
    categories: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>`,
    warehouses: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9.5L12 4l9 5.5V19a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5zM9 21V12h6v9"/></svg>`,
    customers: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>`,
    pricing: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    users: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    stockOverview: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>`,
    stockMovement: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>`,
    rentalList: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>`,
    createRental: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    shippingList: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>`,
    createDelivery: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>`,
    activeRentals: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`,
    returnTools: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>`,
    returnForm: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>`,
    lostTools: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
    soldTools: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"/></svg>`,
    rentalReport: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`,
    revenueReport: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>`,
    inventoryReport: `<svg class="nav-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>`,
};

function buildSection(id, label, links) {
    const currentPath = window.location.pathname;

    const items = links
        .map(([href, title, iconKey]) => {
            const isActive =
                currentPath.endsWith(href) ||
                currentPath.includes(href.replace(".html", ""));
            const activeClass = isActive ? "active-nav" : "";
            const icon =
                icons[iconKey] ||
                `<span class="nav-dot w-1.5 h-1.5 bg-gray-500 rounded-full flex-shrink-0"></span>`;
            return `
      <a href="${href}" class="nav-item block px-4 py-2 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition text-sm flex items-center gap-3 ${activeClass}" title="${title}" data-href="${href}">
        ${icon}
        <span class="sidebar-text">${title}</span>
        <span class="nav-tooltip">${title}</span>
      </a>
    `;
        })
        .join("");

    const sectionHasActive = links.some(
        ([href]) =>
            currentPath.endsWith(href) ||
            currentPath.includes(href.replace(".html", "")),
    );
    const activeHeaderClass = sectionHasActive ? "section-has-active" : "";

    return `
    <div class="pt-2" data-section="${id}">
      <div class="nav-section-header ${activeHeaderClass}" id="header-${id}" onclick="toggleNavSection('${id}')">
        <p class="nav-section-label">${label}</p>
        <span class="section-active-dot"></span>
        <svg class="nav-section-chevron open" id="chevron-${id}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </div>
      <div class="nav-section-items" id="section-${id}">
        ${items}
      </div>
    </div>
  `;
}

const layoutHTML = {
    sidebar: `
    <div id="sidebar" class="w-64 bg-gray-900 text-white transition-all duration-300 h-screen fixed left-0 top-0 overflow-y-auto z-40">
      <!-- Logo -->
      <div class="p-5 border-b border-gray-800">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
          </div>
          <div class="sidebar-text overflow-hidden">
            <h1 class="font-bold text-base leading-tight">ToolRental</h1>
            <p class="text-xs text-gray-500">Pro</p>
          </div>
        </div>
      </div>

      <!-- Navigation -->
      <nav class="p-3 space-y-0.5">
        <!-- Dashboard -->
        <a id="dashboard-link" href="../dashboard.html" class="nav-item block px-4 py-2.5 rounded-lg text-gray-400 hover:bg-gray-800 hover:text-gray-200 transition flex items-center gap-3" data-href="dashboard.html">
          ${icons.dashboard}
          <span class="sidebar-text font-medium">Dashboard</span>
          <span class="nav-tooltip">Dashboard</span>
        </a>

        <!-- Collapsible Sections -->
        <div id="nav-sections"></div>
      </nav>

      <!-- Logout -->
      <div class="p-3 border-t border-gray-800 mt-auto">
        <button id="logout-btn" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm flex items-center justify-center gap-2">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
          </svg>
          <span class="sidebar-text">Logout</span>
        </button>
      </div>
    </div>
  `,

    header: `
    <div class="bg-white border-b border-gray-200 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-20">
      <div class="flex items-center gap-4">
        <button id="sidebar-toggle" class="p-2 hover:bg-gray-100 rounded-lg transition hidden md:block" title="Toggle Sidebar">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <button id="mobile-sidebar-toggle" class="p-2 hover:bg-gray-100 rounded-lg transition sidebar-toggle-mobile" title="Open Menu">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
          </svg>
        </button>
        <h1 id="page-title" class="text-lg md:text-xl font-bold text-gray-900">Dashboard</h1>
      </div>

      <div class="flex items-center gap-2 md:gap-4">
        <div class="hidden md:block header-search">
          <input type="text" placeholder="Search..." class="px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-56">
        </div>
        <button class="p-2 hover:bg-gray-100 rounded-lg transition relative" title="Notifications">
          <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
          </svg>
        </button>
        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold cursor-pointer text-sm" title="User Menu">A</div>
      </div>
    </div>
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-30"></div>
  `,
};

// ===== COLLAPSIBLE SECTION TOGGLE =====
window.toggleNavSection = function (id) {
    const items = document.getElementById("section-" + id);
    const chevron = document.getElementById("chevron-" + id);
    const header = document.getElementById("header-" + id);
    if (!items) return;

    const isClosed = items.classList.contains("closed");
    items.classList.toggle("closed", !isClosed);
    if (chevron) chevron.classList.toggle("open", isClosed);

    // Sync the dashed-line indicator for collapsed sidebar
    if (header) header.classList.toggle("is-collapsed-section", !isClosed);

    const saved = JSON.parse(localStorage.getItem("navSections") || "{}");
    saved[id] = !isClosed ? "closed" : "open";
    localStorage.setItem("navSections", JSON.stringify(saved));
};

function injectNavSections() {
    const container = document.getElementById("nav-sections");
    if (!container) return;

    const sections = [
        [
            "master",
            "Master Data",
            [
                ["master/tools.html", "Tools", "tools"],
                ["master/categories.html", "Categories", "categories"],
                ["master/warehouses.html", "Warehouses", "warehouses"],
                ["master/customers.html", "Customers", "customers"],
                ["master/pricing.html", "Pricing", "pricing"],
                ["master/users.html", "Users", "users"],
            ],
        ],
        [
            "stock",
            "Warehouse Stock",
            [
                [
                    "stock/stock-overview.html",
                    "Stock Overview",
                    "stockOverview",
                ],
                [
                    "stock/stock-movement.html",
                    "Stock Movement",
                    "stockMovement",
                ],
            ],
        ],
        [
            "rentals",
            "Rental Transactions",
            [
                ["rentals/rentals.html", "Rental List", "rentalList"],
                ["rentals/create-rental.html", "Create Rental", "createRental"],
            ],
        ],
        [
            "shipping",
            "Shipping",
            [
                [
                    "shipping/shipping-list.html",
                    "Shipping List",
                    "shippingList",
                ],
                [
                    "shipping/create-shipping.html",
                    "Create Delivery",
                    "createDelivery",
                ],
            ],
        ],
        [
            "monitoring",
            "Rental Monitoring",
            [
                [
                    "monitoring/active-rentals.html",
                    "Active Rentals",
                    "activeRentals",
                ],
            ],
        ],
        [
            "returns",
            "Returns",
            [
                ["returns/returns.html", "Return Tools", "returnTools"],
                ["returns/return-form.html", "Return Form", "returnForm"],
            ],
        ],
        [
            "special",
            "Special Status",
            [
                ["special/lost-tools.html", "Lost Tools", "lostTools"],
                ["special/sold-tools.html", "Sold Tools", "soldTools"],
            ],
        ],
        [
            "reports",
            "Reports",
            [
                ["reports/rental-report.html", "Rental Report", "rentalReport"],
                [
                    "reports/revenue-report.html",
                    "Revenue Report",
                    "revenueReport",
                ],
                [
                    "reports/inventory-report.html",
                    "Inventory Report",
                    "inventoryReport",
                ],
            ],
        ],
    ];

    container.innerHTML = sections
        .map(([id, label, links]) => buildSection(id, label, links))
        .join("");

    // Restore saved collapse states (but never collapse active section)
    const saved = JSON.parse(localStorage.getItem("navSections") || "{}");
    const currentPath = window.location.pathname;

    sections.forEach(([id, , links]) => {
        const sectionHasActive = links.some(
            ([href]) =>
                currentPath.endsWith(href) ||
                currentPath.includes(href.replace(".html", "")),
        );
        if (saved[id] === "closed" && !sectionHasActive) {
            const items = document.getElementById("section-" + id);
            const chevron = document.getElementById("chevron-" + id);
            const header = document.getElementById("header-" + id);
            if (items) items.classList.add("closed");
            if (chevron) chevron.classList.remove("open");
            if (header) header.classList.add("is-collapsed-section");
        }
    });
}

function injectLayout() {
    const body = document.body;

    if (!localStorage.getItem("isLoggedIn")) {
        window.location.href = "../login.html";
        return;
    }

    const styleElement = document.createElement("div");
    styleElement.innerHTML = responsiveStyles;
    body.insertBefore(styleElement, body.firstChild);

    const mainContainer = document.createElement("div");
    mainContainer.className = "flex h-screen bg-gray-100";
    mainContainer.innerHTML += layoutHTML.sidebar;

    const contentWrapper = document.createElement("div");
    contentWrapper.id = "main-content";
    contentWrapper.className =
        "ml-64 flex-1 flex flex-col overflow-hidden transition-all duration-300";
    contentWrapper.innerHTML =
        layoutHTML.header +
        '<div class="flex-1 overflow-y-auto p-4 md:p-8" id="page-content"></div>';

    mainContainer.appendChild(contentWrapper);
    body.insertBefore(mainContainer, body.firstChild);

    // Set Dashboard active if applicable
    const currentPath = window.location.pathname;
    const dashLink = document.getElementById("dashboard-link");
    if (dashLink) {
        const isDashActive =
            currentPath.endsWith("/") || currentPath.endsWith("dashboard.html");
        if (isDashActive) dashLink.classList.add("active-nav");
    }

    injectNavSections();
    initializeSidebarToggle();
}

function initializeSidebarToggle() {
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const mobileSidebarToggle = document.getElementById(
        "mobile-sidebar-toggle",
    );
    const sidebar = document.getElementById("sidebar");
    const sidebarOverlay = document.getElementById("sidebar-overlay");
    const mainContent = document.getElementById("main-content");
    let isCollapsed = false;
    let isMobileOpen = false;

    if (sidebarToggle) {
        sidebarToggle.addEventListener("click", () => {
            isCollapsed = !isCollapsed;
            sidebar.classList.toggle("collapsed", isCollapsed);
            mainContent.classList.toggle("sidebar-collapsed", isCollapsed);
            localStorage.setItem("sidebarCollapsed", isCollapsed);
        });
    }

    if (mobileSidebarToggle) {
        mobileSidebarToggle.addEventListener("click", () => {
            isMobileOpen = !isMobileOpen;
            sidebar.classList.toggle("mobile-open", isMobileOpen);
            if (sidebarOverlay)
                sidebarOverlay.classList.toggle("active", isMobileOpen);
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", () => {
            sidebar.classList.remove("mobile-open");
            sidebarOverlay.classList.remove("active");
            isMobileOpen = false;
        });
    }

    document.querySelectorAll("#sidebar a").forEach((link) => {
        link.addEventListener("click", () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove("mobile-open");
                if (sidebarOverlay) sidebarOverlay.classList.remove("active");
                isMobileOpen = false;
            }
        });
    });

    const wasCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
    if (wasCollapsed && window.innerWidth > 768) {
        sidebar.classList.add("collapsed");
        mainContent.classList.add("sidebar-collapsed");
        isCollapsed = true;
    }

    const logoutBtn = document.getElementById("logout-btn");
    if (logoutBtn) {
        logoutBtn.addEventListener("click", () => {
            localStorage.removeItem("isLoggedIn");
            localStorage.removeItem("sidebarCollapsed");
            localStorage.removeItem("navSections");
            window.location.href = "../login.html";
        });
    }

    window.addEventListener("resize", () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove("mobile-open");
            if (sidebarOverlay) sidebarOverlay.classList.remove("active");
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    injectLayout();
});
