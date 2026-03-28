@extends('layout.app')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Return List</h2>
        <a href="{{ route('transactions.rentals.form') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Return
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Returning</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalReturn }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">Rp. {{ number_format($totalRevenue) }}</p>
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
            <input type="text" id="search-input" placeholder="Search invoice, customer, status..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">

        <div id="empty-state" class="p-8 text-center hidden">
            <p class="text-gray-600">No results found.</p>
        </div>

        <div class="overflow-x-auto" id="table-wrapper">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        {{-- Sortable --}}
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer select-none"
                            data-col="invoice_number">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Invoice
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer select-none"
                            data-col="customer_name">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Customer
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
                        {{-- Non-sortable --}}
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tools</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer select-none"
                            data-col="rental_start_date">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Rental Period
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer select-none"
                            data-col="total_price">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Total Price
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer select-none"
                            data-col="rental_status">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Rental Status
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase cursor-pointer select-none"
                            data-col="payment_status">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Payment Status
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody id="table-body" class="divide-y divide-gray-200"></tbody>
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

    {{-- Modal --}}
    <div id="rentalModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        onclick="handleBackdropClick(event)">
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl">
            <div class="flex justify-between items-start p-6 pb-4">
                <div>
                    <h3 id="modal-invoice" class="text-2xl font-bold text-gray-900"></h3>
                    <p id="modal-created" class="text-gray-500 text-sm mt-1"></p>
                </div>
                <button onclick="closeRentalModal()"
                    class="text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="px-6 pb-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Customer Information
                        </p>
                        <p id="modal-customer-name" class="text-base font-bold text-gray-900"></p>
                        <p id="modal-customer-email" class="text-sm text-gray-500 mt-1"></p>
                        <p id="modal-customer-phone" class="text-sm text-gray-500"></p>
                    </div>
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Rental Status</p>
                        <span id="modal-status-badge"
                            class="inline-block px-3 py-1 rounded-full text-sm font-medium"></span>
                        <p class="text-sm text-gray-500 mt-3">Rental Period:</p>
                        <p id="modal-period" class="text-sm font-medium text-gray-900 mt-0.5"></p>
                    </div>
                </div>
                <div>
                    <h4 id="modal-items-title" class="text-base font-semibold text-gray-900 mb-3"></h4>
                    <div id="modal-items-list" class="space-y-3"></div>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p id="modal-total-amount" class="text-4xl font-bold text-blue-600 mt-1"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Total Items</p>
                        <p id="modal-total-items" class="text-4xl font-bold text-gray-900 mt-1"></p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 pt-1">
                    <button
                        class="flex-1 bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Details
                    </button>
                    <button onclick="closeRentalModal()"
                        class="flex-1 bg-gray-100 text-gray-800 py-3 rounded-xl hover:bg-gray-200 transition font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ── Data dari Laravel ────────────────────────────────────────────────
        const allRentals = @json($rentals);
        const customersById = @json($customersById);
        const movementsByRentalId = @json($movementsByRentalId);

        // ── State ────────────────────────────────────────────────────────────
        let filtered = [...allRentals];
        let currentPage = 1;
        let perPage = 10;
        let sortCol = null;
        let sortDir = 'asc';

        // ── Helpers ──────────────────────────────────────────────────────────
        function formatCurrency(amount) {
            return 'Rp. ' + new Intl.NumberFormat('id-ID').format(amount ?? 0);
        }

        function formatDate(str) {
            if (!str) return '-';
            return new Date(str).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        const statusClasses = {
            'Pending': 'bg-yellow-100 text-yellow-800',
            'Delivered': 'bg-green-100 text-green-800',
            'On Track': 'bg-blue-100 text-blue-800',
            'Overdue': 'bg-red-100 text-red-800',
            'Returning': 'bg-purple-100 text-purple-800',
            'On Check': 'bg-gray-100 text-gray-800',
        };

        function statusBadge(status) {
            const cls = statusClasses[status] ?? 'bg-gray-100 text-gray-800';
            return `<span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${cls}">${status ?? '-'}</span>`;
        }

        function toolsCount(rental) {
            try {
                return (JSON.parse(rental.movement_id) ?? []).length;
            } catch {
                return 0;
            }
        }

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

            paged.forEach(rental => {
                const count = toolsCount(rental);
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition cursor-pointer';
                tr.innerHTML = `
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${rental.invoice_number ?? '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">${rental.customer?.name ?? 'N/A'}</td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                            ${count} tool${count !== 1 ? 's' : ''}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">
                        ${formatDate(rental.rental_start_date)} – ${formatDate(rental.rental_end_date)}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">${formatCurrency(rental.total_price)}</td>
                    <td class="px-4 py-3 text-sm">${statusBadge(rental.rental_status)}</td>
                    <td class="px-4 py-3 text-sm">${statusBadge(rental.payment_status)}</td>
                    <td class="px-4 py-3 text-sm">
                        <button class="text-blue-600 hover:text-blue-700 font-medium view-btn"
                            data-id="${rental.id}">View</button>
                    </td>
                `;
                // Klik row buka modal
                tr.addEventListener('click', () => openRentalModal(rental.id));
                // Klik tombol View tidak bubble ke row dua kali
                tr.querySelector('.view-btn').addEventListener('click', e => {
                    e.stopPropagation();
                    openRentalModal(rental.id);
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
            filtered = allRentals.filter(r =>
                (r.invoice_number ?? '').toLowerCase().includes(q) ||
                (r.customer?.name ?? '').toLowerCase().includes(q) ||
                (r.rental_status ?? '').toLowerCase().includes(q) ||
                (r.payment_status ?? '').toLowerCase().includes(q) ||
                (r.return_invoice_number ?? '').toLowerCase().includes(q)
            );
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

                // Reset semua icon, highlight aktif
                document.querySelectorAll('th[data-col]').forEach(t => {
                    t.querySelector('.up').style.color = '';
                    t.querySelector('.down').style.color = '';
                });
                th.querySelector('.up').style.color = sortDir === 'asc' ? '#2563eb' : '';
                th.querySelector('.down').style.color = sortDir === 'desc' ? '#2563eb' : '';

                filtered.sort((a, b) => {
                    let valA, valB;
                    if (col === 'customer_name') {
                        valA = (a.customer?.name ?? '').toLowerCase();
                        valB = (b.customer?.name ?? '').toLowerCase();
                    } else if (col === 'total_price') {
                        valA = parseFloat(a.total_price ?? 0);
                        valB = parseFloat(b.total_price ?? 0);
                    } else if (col === 'rental_start_date') {
                        valA = new Date(a.rental_start_date ?? 0);
                        valB = new Date(b.rental_start_date ?? 0);
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

        // ── Modal ────────────────────────────────────────────────────────────
        function openRentalModal(rentalId) {
            const rental = allRentals.find(r => r.id === rentalId);
            if (!rental) return;

            const customer = customersById[rental.customer_id] ?? null;
            const movements = movementsByRentalId[rentalId] ?? [];

            document.getElementById('modal-invoice').textContent = rental.invoice_number;
            document.getElementById('modal-created').textContent = 'Created: ' + formatDate(rental.created_at);
            document.getElementById('modal-customer-name').textContent = rental.customer?.name ?? 'N/A';
            document.getElementById('modal-customer-email').textContent = customer?.email ?? '';
            document.getElementById('modal-customer-phone').textContent = customer?.phone ?? '';

            const badge = document.getElementById('modal-status-badge');
            badge.textContent = rental.rental_status;
            badge.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium ' +
                (statusClasses[rental.rental_status] ?? 'bg-gray-100 text-gray-800');

            document.getElementById('modal-period').textContent =
                formatDate(rental.rental_start_date) + ' – ' + formatDate(rental.rental_end_date);

            document.getElementById('modal-items-title').textContent = `Rental Items (${movements.length})`;
            document.getElementById('modal-items-list').innerHTML = movements.map(mov => {
                const tool = mov.tool ?? {};
                const dailyRate = parseFloat(tool.daily_rate ?? 0);
                const start = new Date(rental.rental_start_date);
                const end = new Date(rental.rental_end_date);
                const days = Math.max(1, Math.ceil((end - start) / 86400000));
                const subtotal = dailyRate * (mov.quantity ?? 1) * days;

                return `
                    <div class="border-l-4 border-blue-400 bg-blue-50 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-semibold text-gray-900">${tool.name ?? mov.tool_id}</p>
                                <p class="text-sm text-gray-500">Quantity: ${mov.quantity ?? 1}</p>
                            </div>
                            <span class="text-lg font-bold text-blue-600">${formatCurrency(subtotal)}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-gray-500">Daily Rate:</p>
                                <p class="font-semibold text-gray-900">${formatCurrency(dailyRate)}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Duration:</p>
                                <p class="font-semibold text-gray-900">${days} days</p>
                            </div>
                        </div>
                    </div>`;
            }).join('') || '<p class="text-gray-400 text-sm">No items found.</p>';

            document.getElementById('modal-total-amount').textContent = formatCurrency(rental.total_price);
            document.getElementById('modal-total-items').textContent = movements.length;

            document.getElementById('rentalModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRentalModal() {
            document.getElementById('rentalModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function handleBackdropClick(e) {
            if (e.target === document.getElementById('rentalModal')) closeRentalModal();
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeRentalModal();
        });

        // ── Init ─────────────────────────────────────────────────────────────
        renderTable();
    </script>
@endsection
