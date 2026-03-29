{{-- resources/views/rentals/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Header --}}
    <script>
        const uploadProofUrl = '{{ route('rentals.upload.proof', ':id') }}';
    </script>

    @if (session('success'))
        <div id="notif"
            class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('notif')?.remove(), 3000);
        </script>
    @endif

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Rental List</h2>
        <a href="{{ route('transactions.rentals.form') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Rental
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Rentals</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Active Rentals</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Completed</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $completedRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">Rp. {{ number_format($totalRevenue) }}</p>
        </div>
    </div>

    {{-- Tabel Rentals --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tools</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Rental Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Rental Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Payment Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rentals as $rental)
                        @php
                            $statusColor =
                                $rental->rental_status === 'Pending'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : 'bg-green-100 text-green-800';
                            $toolsCount = count(json_decode($rental->movement_id, true) ?? []);
                            $startDate = \Carbon\Carbon::parse($rental['rentalStartDate'])->format('d M Y');
                            $endDate = \Carbon\Carbon::parse($rental['rentalEndDate'])->format('d M Y');
                        @endphp
                        <tr class="hover:bg-gray-50 transition cursor-pointer"
                            onclick="openRentalModal('{{ $rental->id }}')">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $rental->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $rental->customer->name ?? 'N/A' }} -
                                {{ $rental->driver->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $toolsCount }} tool{{ $toolsCount > 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($rental['rental_start_date'])->format('d M Y') }} -
                                {{ \Carbon\Carbon::parse($rental['rental_end_date'])->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                Rp. {{ number_format($rental->total_price) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $rental->rental_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $rental->payment_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <button onclick="event.stopPropagation(); openRentalModal({{ $rental->id }})"
                                    class="text-blue-600 hover:text-blue-700 font-medium">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No rentals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div
            class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 border-t border-gray-200 bg-white">

            {{-- Kiri: Per Page Selector + Info --}}
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <form method="GET" action="{{ route('transactions.rentals') }}" id="per-page-form">
                    <select name="per_page" onchange="document.getElementById('per-page-form').submit()"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach ([10, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <span>entries</span>
                <span class="text-gray-400">
                    &mdash; Showing {{ $rentals->firstItem() }}-{{ $rentals->lastItem() }} of
                    {{ $rentals->total() }}
                </span>
            </div>

            {{-- Kanan: Pagination custom (tanpa teks "Showing X to Y") --}}
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if ($rentals->onFirstPage())
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $rentals->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">‹</a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($rentals->getUrlRange(1, $rentals->lastPage()) as $page => $url)
                    @if ($page == $rentals->currentPage())
                        <span
                            class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&per_page={{ request('per_page', 10) }}"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($rentals->hasMorePages())
                    <a href="{{ $rentals->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">›</a>
                @else
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">›</span>
                @endif
            </div>

        </div>
    </div>

    {{-- Modal --}}
    <div id="rentalModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        onclick="handleBackdropClick(event)">
        <div class="bg-white rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-y-auto shadow-xl">

            {{-- Modal Header --}}
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

                {{-- Customer Info + Rental Status --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Customer Information --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Customer Information
                        </p>
                        <p id="modal-customer-name" class="text-base font-bold text-gray-900"></p>
                        <p id="modal-customer-email" class="text-sm text-gray-500 mt-1"></p>
                        <p id="modal-customer-phone" class="text-sm text-gray-500"></p>
                    </div>

                    {{-- Driver Information --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Driver Information
                        </p>
                        <p id="modal-driver-name" class="text-base font-bold text-gray-900"></p>
                        <p id="modal-driver-email" class="text-sm text-gray-500 mt-1"></p>
                        <p id="modal-driver-phone" class="text-sm text-gray-500"></p>
                    </div>

                    {{-- Rental Status --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Rental Status</p>
                        <span id="modal-status-badge"
                            class="inline-block px-3 py-1 rounded-full text-sm font-medium"></span>
                        <p class="text-sm text-gray-500 mt-3">Rental Period:</p>
                        <p id="modal-period" class="text-sm font-medium text-gray-900 mt-0.5"></p>
                    </div>

                    {{-- Rental Status --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Status</p>

                        <span id="modal-payment-badge"
                            class="inline-block px-3 py-1 rounded-full text-sm font-medium mb-3"></span>

                        {{-- Container dynamic --}}
                        <div id="modal-payment-content"></div>
                    </div>
                </div>

                {{-- Rental Items --}}
                <div>
                    <h4 id="modal-items-title" class="text-base font-semibold text-gray-900 mb-3"></h4>
                    <div id="modal-items-list" class="space-y-3"></div>
                </div>

                {{-- Total Summary --}}
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

                    {{-- Export Excel --}}
                    <a id="btn-export-recap-excel" href="#"
                        class="flex-1 bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        Export Excel
                    </a>

                    <a id="btn-export-stock-excel" href="#"
                        class="flex-1 bg-emerald-600 text-white py-3 rounded-xl hover:bg-emerald-700 transition font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Rekap Stock
                    </a>

                    {{-- Print --}}
                    <a id="btn-export-invoice-excel" href="#"
                        class="flex-1 bg-indigo-600 text-white py-3 rounded-xl hover:bg-indigo-700 transition font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Invoice
                    </a>
                    {{-- <button
                        class="flex-1 bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Details
                    </button> --}}

                    <button onclick="closeRentalModal()"
                        class="flex-1 bg-gray-100 text-gray-800 py-3 rounded-xl hover:bg-gray-200 transition font-medium">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- Modal Upload Payment Proof --}}
    <div id="uploadProofModal" class="hidden fixed inset-0 bg-black/60 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md shadow-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Upload Payment Proof</h3>
                <button onclick="closeUploadModal()"
                    class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="uploadProofForm" enctype="multipart/form-data">
                @csrf
                {{-- Preview --}}
                <div id="uploadPreviewWrapper" class="hidden mb-4">
                    <img id="uploadPreviewImg" src="" alt="Preview"
                        class="w-full max-h-56 object-cover rounded-xl border border-gray-200" />
                </div>

                {{-- Dropzone --}}
                <label for="paymentProofInput"
                    class="flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition group">
                    <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500 mb-2" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span class="text-sm text-gray-500 group-hover:text-blue-600">Click to upload image</span>
                    <span class="text-xs text-gray-400 mt-1">JPG, PNG — max 5MB</span>
                    <input id="paymentProofInput" type="file" class="hidden" accept="image/jpg,image/jpeg,image/png"
                        onchange="previewUpload(event)" />
                </label>

                <div id="uploadError" class="hidden mt-2 text-sm text-red-500"></div>

                <div class="flex gap-3 mt-5">
                    <button type="button" onclick="submitPaymentProof()"
                        class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl hover:bg-blue-700 transition font-medium">
                        Upload
                    </button>
                    <button type="button" onclick="closeUploadModal()"
                        class="flex-1 bg-gray-100 text-gray-800 py-2.5 rounded-xl hover:bg-gray-200 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Image Fullscreen --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black/80 z-[70] flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <img id="imageModalImg" src="" alt="Payment Proof"
            class="max-w-full max-h-[90vh] rounded-xl shadow-2xl object-contain" />
    </div>

    {{-- Data JSON untuk modal (embed sekali, tidak ada JS yang generate tabel) --}}
    <script>
        {{-- ─── Data dari server ──────────────────────────────────────── --}}
        const rentalsData = @json($rentals->items());
        const customersById = @json($customersById);
        const driversById = @json($driversById);
        const movementsByRentalId = @json($movementsByRentalId);

        let currentRentalId = null;

        {{-- ─── Helpers ────────────────────────────────────────────────── --}}
        const fmt = {
            currency: n => 'Rp. ' + new Intl.NumberFormat('id-ID').format(n),
            date: s => s ? new Date(s).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }) : '-',
        };

        const SVG = {
            upload: `<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>`,
            eye: `<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`,
        };

        const BTN =
            `display:inline-flex;align-items:center;gap:6px;background:transparent;border:0.5px solid #ccc;border-radius:8px;padding:5px 12px;font-size:12px;cursor:pointer;`;

        const el = id => document.getElementById(id);

        {{-- ─── Rental Modal ───────────────────────────────────────────── --}}

        function openRentalModal(rentalId) {
            const rental = rentalsData.find(r => r.id === rentalId);
            if (!rental) return;

            currentRentalId = rentalId;

            const customer = customersById[rental.customer_id] ?? null;
            const driver = driversById[rental.driver_id] ?? null;
            const movements = movementsByRentalId[rentalId] ?? [];

            // Header
            el('modal-invoice').textContent = rental.invoice_number;
            el('modal-created').textContent = 'Created: ' + fmt.date(rental.created_at);

            // Customer
            el('modal-customer-name').textContent = customer?.name ?? 'N/A';
            el('modal-customer-email').textContent = customer?.email ?? '';
            el('modal-customer-phone').textContent = customer?.phone ?? '';

            // Driver (jika ada)
            el('modal-driver-name').textContent = driver?.name ?? 'N/A';
            el('modal-driver-email').textContent = driver?.email ?? '';
            el('modal-driver-phone').textContent = driver?.phone ?? '';

            // Rental status badge
            const statusClasses = {
                'Pending': 'bg-yellow-100 text-yellow-800',
                'Delivered': 'bg-green-100 text-green-800',
                'On Track': 'bg-blue-100 text-blue-800',
                'Overdue': 'bg-red-100 text-red-800',
                'Returning': 'bg-purple-100 text-purple-800',
                'On Check': 'bg-gray-100 text-gray-800',
            };
            const badge = el('modal-status-badge');
            badge.textContent = rental.rental_status;
            badge.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium ' +
                (statusClasses[rental.rental_status] ?? 'bg-gray-100 text-gray-800');

            // Rental period
            el('modal-period').textContent =
                fmt.date(rental.rental_start_date) + ' – ' + fmt.date(rental.rental_end_date);

            // Payment badge — only unpaid / paid
            const paymentMeta = {
                unpaid: {
                    label: 'Unpaid',
                    cls: 'bg-red-100 text-red-800'
                },
                paid: {
                    label: 'Paid',
                    cls: 'bg-green-100 text-green-800'
                },
            };
            const pm = paymentMeta[rental.payment_status] ?? {
                label: rental.payment_status,
                cls: 'bg-gray-100 text-gray-800'
            };
            const payBadge = el('modal-payment-badge');
            payBadge.textContent = pm.label;
            payBadge.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium ' + pm.cls;

            // Payment content — always show Upload/Replace + View if proof exists
            const payContent = el('modal-payment-content');
            if (!rental.payment_proof_image) {
                payContent.innerHTML = `
                <div style="margin-top:10px;">
                    <button onclick="openUploadModal()" style="${BTN}">
                        ${SVG.upload} Upload proof
                    </button>
                </div>`;
            } else {
                const imageUrl = '/storage/' + rental.payment_proof_image;
                payContent.innerHTML = `
                <div style="margin-top:10px;display:flex;align-items:center;justify-content:space-between;">
                    <span style="font-size:13px;color:#6b7280;">Payment proof</span>
                    <div style="display:flex;gap:6px;">
                        <button onclick="openImageModal('${imageUrl}')" style="${BTN}">
                            ${SVG.eye} View
                        </button>
                        <button onclick="openUploadModal()" style="${BTN}">
                            ${SVG.upload} Replace
                        </button>
                    </div>
                </div>`;
            }

            // Items
            const startDate = new Date(rental.rental_start_date);
            const endDate = new Date(rental.rental_end_date);
            const days = Math.max(1, Math.ceil((endDate - startDate) / 86400000));

            const exportRecapUrl = '{{ route('rentals.recap.export') }}' + '?rental_id=' + rentalId;
            document.getElementById('btn-export-recap-excel').href = exportRecapUrl;
            const exportStockUrl = '{{ route('rentals.stock.export') }}' + '?rental_id=' + rentalId;
            document.getElementById('btn-export-stock-excel').href = exportStockUrl;
            const exportInvoiceUrl = '{{ route('rentals.invoice.export') }}' + '?rental_id=' + rentalId;
            document.getElementById('btn-export-invoice-excel').href = exportInvoiceUrl;

            el('modal-items-title').textContent = `Rental Items (${movements.length})`;
            el('modal-items-list').innerHTML = movements.length ?
                movements.map(mov => {
                    const toolName = mov.tool?.name ?? mov.tool_id;
                    const dailyRate = mov.tool?.daily_rate ?? 0;
                    const subtotal = dailyRate * mov.quantity * days;
                    return `
                <div class="border-l-4 border-blue-400 bg-blue-50 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-semibold text-gray-900">${toolName}</p>
                            <p class="text-sm text-gray-500">Quantity: ${mov.quantity}</p>
                        </div>
                        <span class="text-lg font-bold text-blue-600">${fmt.currency(subtotal)}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-gray-500">Daily Rate:</p>
                            <p class="font-semibold text-gray-900">${fmt.currency(dailyRate)}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Duration:</p>
                            <p class="font-semibold text-gray-900">${days} days</p>
                        </div>
                    </div>
                </div>`;
                }).join('') :
                '<p class="text-gray-400 text-sm">No items found.</p>';

            // Summary
            el('modal-total-amount').textContent = fmt.currency(rental.total_price);
            el('modal-total-items').textContent = movements.length;

            // Show modal
            el('rentalModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRentalModal() {
            el('rentalModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        {{-- ─── Upload Modal ───────────────────────────────────────────── --}}

        function openUploadModal() {
            el('paymentProofInput').value = '';
            el('uploadPreviewWrapper').classList.add('hidden');
            el('uploadError').classList.add('hidden');
            el('uploadProofModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            el('uploadProofModal').classList.add('hidden');
        }

        function previewUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                el('uploadPreviewImg').src = e.target.result;
                el('uploadPreviewWrapper').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        async function submitPaymentProof() {
            const input = el('paymentProofInput');
            const errorEl = el('uploadError');
            errorEl.classList.add('hidden');

            if (!input.files[0]) {
                errorEl.textContent = 'Please select an image first.';
                errorEl.classList.remove('hidden');
                return;
            }

            const formData = new FormData();
            formData.append('payment_proof', input.files[0]);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const res = await fetch(`/transactions/rentals/${currentRentalId}/upload-proof`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData,
                });

                console.log('[upload] status:', res.status, res.ok);

                const data = await res.json();

                console.log('[upload] response:', data);

                if (data.success) {
                    const rental = rentalsData.find(r => r.id === currentRentalId);
                    console.log('[upload] rental found:', rental);
                    if (rental) {
                        rental.payment_proof_image = data.path.replace('/storage/', '');
                        rental.payment_status = 'paid';
                        console.log('[upload] rental updated:', rental.payment_proof_image, rental.payment_status);
                    }
                    closeUploadModal();
                    openRentalModal(currentRentalId);
                } else {
                    errorEl.textContent = data.message ?? 'Upload failed.';
                    errorEl.classList.remove('hidden');
                }
            } catch (e) {
                console.error('[upload] catch error:', e);
                errorEl.textContent = 'Network error: ' + e.message;
                errorEl.classList.remove('hidden');
            }
        }

        {{-- ─── Image Fullscreen Modal ─────────────────────────────────── --}}

        function openImageModal(src) {
            el('imageModalImg').src = src;
            el('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            el('imageModal').classList.add('hidden');
        }

        {{-- ─── Global Event Listeners ─────────────────────────────────── --}}
        // Backdrop click — rental modal
        el('rentalModal').addEventListener('click', e => {
            if (e.target === el('rentalModal')) closeRentalModal();
        });

        // Escape key
        document.addEventListener('keydown', e => {
            if (e.key !== 'Escape') return;
            closeImageModal();
            closeUploadModal();
            closeRentalModal();
        });
    </script>
@endsection
