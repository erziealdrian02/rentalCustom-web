@extends('layout.app')

@section('content')
    <script>
        const shippingsData = @json($shippings);
        const rentalsData = @json($rentals);
        const driversData = @json($drivers);
        const warehousesData = @json($warehouses);
        const movementsData = @json($movements);
    </script>

    {{-- Modal --}}
    <div id="deliveryModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        onclick="handleModalBackdrop(event)">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">Delivery Details</h2>
                <button onclick="closeDeliveryModal()"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="deliveryModalContent" class="p-6 space-y-5"></div>
            <div class="border-t px-6 py-4 flex justify-end bg-gray-50">
                <button onclick="closeDeliveryModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 text-sm font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Shipping Deliveries</h2>
        <a href="{{ route('shipping.form') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Delivery
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Total Shipments</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalShipments }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Delivered</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $delivered }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">In Transit</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $onTrack }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-500 text-sm font-medium">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pending }}</p>
        </div>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <div class="relative max-w-sm w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" id="search-input" placeholder="Search delivery #, driver, destination, status..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        <div id="empty-state" class="p-8 text-center hidden">
            <p class="text-gray-600">No shipments found.</p>
        </div>

        <div class="overflow-x-auto" id="table-wrapper">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none"
                            data-col="delivery_number">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Delivery #
                                <span class="sort-icon flex flex-col leading-none text-gray-300">
                                    <svg class="w-2.5 h-2.5 up" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M182.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19 224 32 224H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 down" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M137.4 470.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8H32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z" />
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none"
                            data-col="driver_name">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Driver
                                <span class="sort-icon flex flex-col leading-none text-gray-300">
                                    <svg class="w-2.5 h-2.5 up" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M182.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19 224 32 224H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 down" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M137.4 470.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8H32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z" />
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rentals</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none"
                            data-col="to_location">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Destination
                                <span class="sort-icon flex flex-col leading-none text-gray-300">
                                    <svg class="w-2.5 h-2.5 up" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M182.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19 224 32 224H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 down" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M137.4 470.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8H32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z" />
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none"
                            data-col="delivery_status">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Status
                                <span class="sort-icon flex flex-col leading-none text-gray-300">
                                    <svg class="w-2.5 h-2.5 up" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M182.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19 224 32 224H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 down" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M137.4 470.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8H32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z" />
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase cursor-pointer select-none"
                            data-col="created_at">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Created
                                <span class="sort-icon flex flex-col leading-none text-gray-300">
                                    <svg class="w-2.5 h-2.5 up" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M182.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-128 128c-9.2 9.2-11.9 22.9-6.9 34.9S19 224 32 224H288c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-128-128z" />
                                    </svg>
                                    <svg class="w-2.5 h-2.5 down" fill="currentColor" viewBox="0 0 320 512">
                                        <path
                                            d="M137.4 470.6c12.5 12.5 32.8 12.5 45.3 0l128-128c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8H32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l128 128z" />
                                    </svg>
                                </span>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>

        {{-- Footer --}}
        <div
            class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 border-t border-gray-200 bg-white">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <select id="per-page-select"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="10">10</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>entries</span>
                <span class="text-gray-400" id="page-info"></span>
            </div>
            <div class="flex items-center gap-1" id="pagination"></div>
        </div>
    </div>

    <script>
        // ── Helpers ──────────────────────────────────────────────────────────
        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function formatDateTime(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatCurrency(amount) {
            return 'Rp. ' + new Intl.NumberFormat('id-ID').format(amount ?? 0);
        }

        const statusMap = {
            'Delivered': 'bg-green-100 text-green-800',
            'delivered': 'bg-green-100 text-green-800',
            'On Track': 'bg-blue-100 text-blue-800',
            'in_transit': 'bg-blue-100 text-blue-800',
            'Pending': 'bg-yellow-100 text-yellow-800',
            'pending': 'bg-yellow-100 text-yellow-800',
            'failed': 'bg-red-100 text-red-800',
            'cancelled': 'bg-gray-100 text-gray-700',
        };

        function statusBadge(status) {
            const cls = statusMap[status] ?? 'bg-gray-100 text-gray-700';
            const label = (status ?? '-').replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
            return `<span class="inline-block px-2 py-1 rounded-full text-xs font-semibold ${cls}">${label}</span>`;
        }

        function getDriver(shipping) {
            return driversData[shipping.driver_id] ?? null;
        }

        function getRentalIds(shipping) {
            try {
                return Array.isArray(shipping.rental_id) ?
                    shipping.rental_id :
                    JSON.parse(shipping.rental_id ?? '[]');
            } catch {
                return [];
            }
        }

        // ── State ────────────────────────────────────────────────────────────
        // shippingsData dari Laravel adalah array (bukan keyed object)
        const allShippings = Array.isArray(shippingsData) ?
            shippingsData :
            Object.values(shippingsData);

        let filtered = [...allShippings];
        let currentPage = 1;
        let perPage = 10;
        let sortCol = null;
        let sortDir = 'asc';

        // ── Render tabel ─────────────────────────────────────────────────────
        function renderTable() {
            const tbody = document.getElementById('table-body');
            const empty = document.getElementById('empty-state');
            const wrapper = document.getElementById('table-wrapper');
            const start = (currentPage - 1) * perPage;
            const paged = filtered.slice(start, start + perPage);

            tbody.innerHTML = '';

            if (filtered.length === 0) {
                empty.classList.remove('hidden');
                wrapper.classList.add('hidden');
            } else {
                empty.classList.add('hidden');
                wrapper.classList.remove('hidden');
            }

            paged.forEach(shipping => {
                const driver = getDriver(shipping);
                const rentalIds = getRentalIds(shipping);
                const count = rentalIds.length;

                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition cursor-pointer';
                tr.innerHTML = `
                    <td class="px-5 py-4 font-semibold text-gray-900">${shipping.delivery_number ?? '-'}</td>
                    <td class="px-5 py-4 text-gray-700">${driver?.name ?? 'N/A'}</td>
                    <td class="px-5 py-4">
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">
                            ${count} rental${count !== 1 ? 's' : ''}
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-700">${shipping.to_location ?? '-'}</td>
                    <td class="px-5 py-4">${statusBadge(shipping.delivery_status)}</td>
                    <td class="px-5 py-4 text-gray-500 text-xs">${formatDate(shipping.created_at)}</td>
                    <td class="px-5 py-4">
                        <button class="view-btn text-blue-600 hover:text-blue-800 font-medium text-sm"
                            data-id="${shipping.id}">View</button>
                    </td>
                `;

                tr.addEventListener('click', () => openDeliveryModal(shipping.id));
                tr.querySelector('.view-btn').addEventListener('click', e => {
                    e.stopPropagation();
                    openDeliveryModal(shipping.id);
                });

                tbody.appendChild(tr);
            });

            renderPageInfo();
            renderPagination();
        }

        function renderPageInfo() {
            const start = filtered.length === 0 ? 0 : (currentPage - 1) * perPage + 1;
            const end = Math.min(currentPage * perPage, filtered.length);
            document.getElementById('page-info').textContent =
                filtered.length > 0 ? `— Showing ${start}-${end} of ${filtered.length}` : '';
        }

        function renderPagination() {
            const totalPages = Math.ceil(filtered.length / perPage);
            const container = document.getElementById('pagination');
            container.innerHTML = '';

            const btn = (label, page, disabled, active) => {
                const el = document.createElement(disabled ? 'span' : 'a');
                el.innerHTML = label;
                el.className = `px-3 py-1.5 text-sm rounded-lg border transition ` +
                    (active ? 'bg-blue-600 text-white font-semibold border-blue-600' :
                        disabled ? 'border-gray-200 text-gray-300 cursor-not-allowed' :
                        'border-gray-200 text-gray-600 hover:bg-gray-100 cursor-pointer');
                if (!disabled && !active) el.addEventListener('click', () => {
                    currentPage = page;
                    renderTable();
                });
                return el;
            };

            container.appendChild(btn('‹', currentPage - 1, currentPage === 1, false));
            for (let p = 1; p <= totalPages; p++) {
                container.appendChild(btn(p, p, false, p === currentPage));
            }
            container.appendChild(btn('›', currentPage + 1, currentPage === totalPages || totalPages === 0, false));
        }

        // ── Search ───────────────────────────────────────────────────────────
        document.getElementById('search-input').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            filtered = allShippings.filter(s => {
                const driver = getDriver(s);
                return (s.delivery_number ?? '').toLowerCase().includes(q) ||
                    (s.to_location ?? '').toLowerCase().includes(q) ||
                    (s.delivery_status ?? '').toLowerCase().includes(q) ||
                    (driver?.name ?? '').toLowerCase().includes(q);
            });
            currentPage = 1;
            renderTable();
        });

        // ── Per page ─────────────────────────────────────────────────────────
        document.getElementById('per-page-select').addEventListener('change', function() {
            perPage = parseInt(this.value);
            currentPage = 1;
            renderTable();
        });

        // ── Sort ─────────────────────────────────────────────────────────────
        document.querySelectorAll('th[data-col]').forEach(th => {
            th.addEventListener('click', () => {
                const col = th.dataset.col;
                sortDir = sortCol === col ? (sortDir === 'asc' ? 'desc' : 'asc') : 'asc';
                sortCol = col;

                document.querySelectorAll('th[data-col]').forEach(t => {
                    t.querySelector('.up').style.color = '';
                    t.querySelector('.down').style.color = '';
                });
                th.querySelector('.up').style.color = sortDir === 'asc' ? '#2563eb' : '';
                th.querySelector('.down').style.color = sortDir === 'desc' ? '#2563eb' : '';

                filtered.sort((a, b) => {
                    let valA, valB;
                    if (col === 'driver_name') {
                        valA = (driversData[a.driver_id]?.name ?? '').toLowerCase();
                        valB = (driversData[b.driver_id]?.name ?? '').toLowerCase();
                    } else if (col === 'created_at') {
                        valA = new Date(a.created_at ?? 0);
                        valB = new Date(b.created_at ?? 0);
                    } else {
                        valA = (a[col] ?? '').toString().toLowerCase();
                        valB = (b[col] ?? '').toString().toLowerCase();
                    }
                    if (valA < valB) return sortDir === 'asc' ? -1 : 1;
                    if (valA > valB) return sortDir === 'asc' ? 1 : -1;
                    return 0;
                });

                currentPage = 1;
                renderTable();
            });
        });

        // ── Modal (sama persis dengan original, tidak diubah) ────────────────
        function openDeliveryModal(shippingId) {
            const shipping = allShippings.find(s => s.id === shippingId);
            if (!shipping) return;

            const driver = getDriver(shipping);
            const rentalIds = getRentalIds(shipping);
            const fromLocs = (() => {
                try {
                    return Array.isArray(shipping.from_location) ?
                        shipping.from_location :
                        JSON.parse(shipping.from_location ?? '[]');
                } catch {
                    return [];
                }
            })();

            const rentalsHtml = rentalIds.map((rid, idx) => {
                const rental = rentalsData[rid] ?? null;
                const customer = rental?.customer ?? null;
                const whIds = fromLocs[idx] ?? [];
                const fromNames = Array.isArray(whIds) ?
                    whIds.map(wid => warehousesData[wid]?.name ?? `Warehouse #${wid}`).join(', ') :
                    `Warehouse #${whIds}`;

                const movIds = (() => {
                    try {
                        return JSON.parse(rental?.movement_id ?? '[]');
                    } catch {
                        return [];
                    }
                })();

                const toolsHtml = movIds.map(mid => {
                    const mov = movementsData[mid] ?? null;
                    if (!mov) return '';
                    const tool = mov.tool ?? {};
                    const rate = parseFloat(tool.daily_rate ?? 0);
                    const start = new Date(rental.rental_start_date);
                    const end = new Date(rental.rental_end_date);
                    const days = Math.max(1, Math.ceil((end - start) / 86400000));
                    const sub = rate * (mov.quantity ?? 1) * days;

                    return `
                        <div class="border-l-4 border-blue-400 bg-blue-50 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-1">
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">${tool.name ?? mid}</p>
                                    <p class="text-xs text-gray-500">Qty: ${mov.quantity ?? 1}</p>
                                </div>
                                <span class="text-sm font-bold text-blue-600">${formatCurrency(sub)}</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs mt-1">
                                <div><p class="text-gray-500">Daily Rate:</p><p class="font-semibold">${formatCurrency(rate)}</p></div>
                                <div><p class="text-gray-500">Duration:</p><p class="font-semibold">${days} days</p></div>
                            </div>
                        </div>`;
                }).join('');

                return `
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">${rental?.invoice_number ?? rid}</p>
                                <p class="text-xs text-gray-500">${customer?.name ?? 'N/A'}</p>
                            </div>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded font-medium">
                                ${movIds.length} tools
                            </span>
                        </div>
                        <div class="p-4 space-y-3">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-500 text-xs mb-1">From</p>
                                    <p class="font-semibold text-gray-900">${fromNames}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-gray-500 text-xs mb-1">To</p>
                                    <p class="font-semibold text-gray-900">${shipping.to_location ?? '-'}</p>
                                </div>
                            </div>
                            <div class="space-y-2">${toolsHtml || '<p class="text-gray-400 text-xs">No tools data.</p>'}</div>
                        </div>
                    </div>`;
            }).join('');

            document.getElementById('deliveryModalContent').innerHTML = `
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Delivery Number</p>
                        <p class="text-xl font-bold text-gray-900">${shipping.delivery_number}</p>
                        <p class="text-xs text-gray-400 mt-1">Created: ${formatDate(shipping.created_at)}</p>
                    </div>
                    ${statusBadge(shipping.delivery_status)}
                </div>

                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Driver Information</p>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><p class="text-gray-500 text-xs">Name</p><p class="font-semibold">${driver?.name ?? 'N/A'}</p></div>
                        <div><p class="text-gray-500 text-xs">Vehicle</p><p class="font-semibold">${driver?.vehicle_type ?? '-'}</p></div>
                        <div><p class="text-gray-500 text-xs">License Plate</p><p class="font-semibold">${driver?.license_plate ?? '-'}</p></div>
                        <div><p class="text-gray-500 text-xs">Phone</p><p class="font-semibold">${driver?.phone ?? '-'}</p></div>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Timeline</p>
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full ${shipping.departure_time ? 'bg-green-500' : 'bg-gray-300'}"></div>
                            <div>
                                <p class="text-xs text-gray-500">Departure</p>
                                <p class="text-sm font-semibold">${formatDateTime(shipping.departure_time)}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full ${shipping.estimated_arrival_time ? 'bg-blue-500' : 'bg-gray-300'}"></div>
                            <div>
                                <p class="text-xs text-gray-500">Estimated Arrival</p>
                                <p class="text-sm font-semibold">${formatDateTime(shipping.estimated_arrival_time)}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-2.5 h-2.5 rounded-full ${shipping.actual_arrival_time ? 'bg-green-600' : 'bg-gray-300'}"></div>
                            <div>
                                <p class="text-xs text-gray-500">Actual Arrival</p>
                                <p class="text-sm font-semibold">${shipping.actual_arrival_time ? formatDateTime(shipping.actual_arrival_time) : 'Not yet arrived'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Rental Items (${rentalIds.length})</p>
                    <div class="space-y-3">${rentalsHtml || '<p class="text-gray-400 text-sm">No rentals.</p>'}</div>
                </div>

                ${shipping.notes ? `
                        <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Notes</p>
                            <p class="text-sm text-gray-700">${shipping.notes}</p>
                        </div>` : ''}
            `;

            document.getElementById('deliveryModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeliveryModal() {
            document.getElementById('deliveryModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function handleModalBackdrop(e) {
            if (e.target === document.getElementById('deliveryModal')) closeDeliveryModal();
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeDeliveryModal();
        });

        // ── Init ─────────────────────────────────────────────────────────────
        renderTable();
    </script>
@endsection
