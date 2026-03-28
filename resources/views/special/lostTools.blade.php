@extends('layout.app')

@section('content')
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Lost Tools Report</h2>
        <p class="text-sm text-gray-600 mt-2">Tools marked as lost during rental period</p>
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
            <input type="text" id="search-input" placeholder="Search tool, invoice, customer..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div id="empty-state" class="p-8 text-center hidden">
            <p class="text-gray-600">No results found</p>
        </div>

        <div class="overflow-x-auto" id="table-wrapper">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none"
                            data-col="code_tools">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Tool Code <span class="sort-icon flex flex-col leading-none text-gray-300">
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
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none"
                            data-col="name">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Tool Name <span class="sort-icon flex flex-col leading-none text-gray-300">
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
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Serial Number</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none"
                            data-col="replacement_value">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Replacement Value <span class="sort-icon flex flex-col leading-none text-gray-300">
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
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none"
                            data-col="created_at">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Lost Date <span class="sort-icon flex flex-col leading-none text-gray-300">
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
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none"
                            data-col="invoice">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Invoice <span class="sort-icon flex flex-col leading-none text-gray-300">
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
                        <th class="px-6 py-3 text-left font-semibold text-gray-700 cursor-pointer select-none"
                            data-col="customer">
                            <div class="flex items-center gap-1 hover:text-blue-600 transition-colors">
                                Customer <span class="sort-icon flex flex-col leading-none text-gray-300">
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
                    </tr>
                </thead>
                <tbody id="table-body"></tbody>
            </table>
        </div>

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
        const rawData = @json($lostTools);

        let data = [...rawData];
        let filtered = [...data];
        let currentPage = 1;
        let perPage = 10;
        let sortCol = null;
        let sortDir = 'asc';

        function formatRp(val) {
            return 'Rp. ' + parseFloat(val || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 2
            });
        }

        function formatDate(str) {
            if (!str) return '-';
            return new Date(str).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function renderTable() {
            const tbody = document.getElementById('table-body');
            const empty = document.getElementById('empty-state');
            const start = (currentPage - 1) * perPage;
            const paged = filtered.slice(start, start + perPage);

            tbody.innerHTML = '';

            if (filtered.length === 0) {
                empty.classList.remove('hidden');
                document.getElementById('table-wrapper').classList.add('hidden');
            } else {
                empty.classList.add('hidden');
                document.getElementById('table-wrapper').classList.remove('hidden');
            }

            paged.forEach(item => {
                const tool = item.tool || {};
                const rent = item.rent || {};
                const customer = rent.customer || {};

                const tr = document.createElement('tr');
                tr.className = 'border-b hover:bg-red-100 transition bg-red-50';
                tr.innerHTML = `
                    <td class="px-6 py-4 font-semibold text-red-700">${tool.code_tools ?? '-'}</td>
                    <td class="px-6 py-4 font-semibold text-red-700">${item.quantity ?? '-'}</td>
                    <td class="px-6 py-4">${tool.name ?? '-'}</td>
                    <td class="px-6 py-4 text-gray-600">${tool.serial_number ?? '-'}</td>
                    <td class="px-6 py-4 font-semibold text-red-600">${formatRp(tool.replacement_value)}</td>
                    <td class="px-6 py-4">${formatDate(item.created_at)}</td>
                    <td class="px-6 py-4">${rent.invoice_number ?? 'N/A'}</td>
                    <td class="px-6 py-4">${customer.name ?? 'N/A'}</td>
                `;
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

        // Search — cari di tool, invoice, customer
        document.getElementById('search-input').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            filtered = data.filter(item => {
                const tool = item.tool || {};
                const rent = item.rent || {};
                const customer = rent.customer || {};
                return (tool.code_tools ?? '').toLowerCase().includes(q) ||
                    (tool.name ?? '').toLowerCase().includes(q) ||
                    (tool.serial_number ?? '').toLowerCase().includes(q) ||
                    (rent.invoice_number ?? '').toLowerCase().includes(q) ||
                    (customer.name ?? '').toLowerCase().includes(q);
            });
            currentPage = 1;
            renderTable();
        });

        // Per page
        document.getElementById('per-page-select').addEventListener('change', function() {
            perPage = parseInt(this.value);
            currentPage = 1;
            renderTable();
        });

        // Sort
        document.querySelectorAll('th[data-col]').forEach(th => {
            th.addEventListener('click', () => {
                const col = th.dataset.col;
                if (sortCol === col) {
                    sortDir = sortDir === 'asc' ? 'desc' : 'asc';
                } else {
                    sortCol = col;
                    sortDir = 'asc';
                }

                document.querySelectorAll('th[data-col]').forEach(t => {
                    t.querySelector('.up').style.color = '';
                    t.querySelector('.down').style.color = '';
                });
                th.querySelector('.up').style.color = sortDir === 'asc' ? '#2563eb' : '';
                th.querySelector('.down').style.color = sortDir === 'desc' ? '#2563eb' : '';

                filtered.sort((a, b) => {
                    let valA, valB;
                    const tool = col => (a.tool || {})[col] ?? '';
                    const toolB = col => (b.tool || {})[col] ?? '';

                    if (col === 'replacement_value') {
                        valA = parseFloat((a.tool || {}).replacement_value || 0);
                        valB = parseFloat((b.tool || {}).replacement_value || 0);
                    } else if (col === 'created_at') {
                        valA = new Date(a.created_at);
                        valB = new Date(b.created_at);
                    } else if (col === 'invoice') {
                        valA = (a.rent?.invoice_number ?? '').toLowerCase();
                        valB = (b.rent?.invoice_number ?? '').toLowerCase();
                    } else if (col === 'customer') {
                        valA = (a.rent?.customer?.name ?? '').toLowerCase();
                        valB = (b.rent?.customer?.name ?? '').toLowerCase();
                    } else {
                        valA = ((a.tool || {})[col] ?? '').toString().toLowerCase();
                        valB = ((b.tool || {})[col] ?? '').toString().toLowerCase();
                    }

                    if (valA < valB) return sortDir === 'asc' ? -1 : 1;
                    if (valA > valB) return sortDir === 'asc' ? 1 : -1;
                    return 0;
                });

                currentPage = 1;
                renderTable();
            });
        });

        renderTable();
    </script>
@endsection
