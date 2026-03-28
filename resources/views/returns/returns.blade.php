{{-- resources/views/rentals/index.blade.php --}}
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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Returning</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalReturn }}</p>
        </div>
        {{-- <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Sold Damaged On Rental</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Damaged</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $completedRentals }}</p>
        </div> --}}
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
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $rental->customer->name ?? 'N/A' }}</td>
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
        <div class="bg-white rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto shadow-xl">

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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Customer Information --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Customer Information</p>
                        <p id="modal-customer-name" class="text-base font-bold text-gray-900"></p>
                        <p id="modal-customer-email" class="text-sm text-gray-500 mt-1"></p>
                        <p id="modal-customer-phone" class="text-sm text-gray-500"></p>
                    </div>

                    {{-- Rental Status --}}
                    <div class="border border-gray-200 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Rental Status</p>
                        <span id="modal-status-badge"
                            class="inline-block px-3 py-1 rounded-full text-sm font-medium"></span>
                        <p class="text-sm text-gray-500 mt-3">Rental Period:</p>
                        <p id="modal-period" class="text-sm font-medium text-gray-900 mt-0.5"></p>
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

                {{-- Action Buttons --}}
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

    {{-- Data JSON untuk modal (embed sekali, tidak ada JS yang generate tabel) --}}
    <script>
        const rentalsData = @json($rentals->items());
        const customersById = @json($customersById);
        const movementsByRentalId = @json($movementsByRentalId);

        console.log(JSON.parse(atob('{{ base64_encode(json_encode($movementsByRentalId)) }}')));

        function formatCurrency(amount) {
            return 'Rp. ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function formatDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function openRentalModal(rentalId) {
            const rental = rentalsData.find(r => r.id === rentalId);
            if (!rental) return;

            const customer = customersById[rental.customer_id] ?? null;
            const movements = movementsByRentalId[rentalId] ?? [];

            // Header
            document.getElementById('modal-invoice').textContent = rental.invoice_number;
            document.getElementById('modal-created').textContent =
                'Created: ' + formatDate(rental.created_at);

            // Customer
            document.getElementById('modal-customer-name').textContent = rental.customer?.name ?? 'N/A';
            document.getElementById('modal-customer-email').textContent = customer?.email ?? '';
            document.getElementById('modal-customer-phone').textContent = customer?.phone ?? '';

            // Status badge
            const badge = document.getElementById('modal-status-badge');
            badge.textContent = rental.rental_status;
            const statusClasses = {
                'Pending': 'bg-yellow-100 text-yellow-800',
                'Delivered': 'bg-green-100 text-green-800',
                'On Track': 'bg-blue-100 text-blue-800',
                'Overdue': 'bg-red-100 text-red-800',
                'Returning': 'bg-purple-100 text-purple-800',
                'On Check': 'bg-gray-100 text-gray-800',
            };
            badge.className = 'inline-block px-3 py-1 rounded-full text-sm font-medium ' +
                (statusClasses[rental.rental_status] ?? 'bg-gray-100 text-gray-800');

            // Rental period
            document.getElementById('modal-period').textContent =
                formatDate(rental.rental_start_date) + ' – ' + formatDate(rental.rental_end_date);

            // Items dari stock_movements
            document.getElementById('modal-items-title').textContent = `Rental Items (${movements.length})`;
            document.getElementById('modal-items-list').innerHTML = movements.map(mov => {
                const toolName = mov.tool?.name ?? mov.tool_id;
                const toolCode = mov.tool?.code_tools ?? '';
                const dailyRate = mov.tool?.daily_rate ?? 0;

                // Hitung durasi dari rental period
                const startDate = new Date(rental.rental_start_date);
                const endDate = new Date(rental.rental_end_date);
                const days = Math.max(1, Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)));

                const subtotal = dailyRate * mov.quantity * days;

                return `
        <div class="border-l-4 border-blue-400 bg-blue-50 rounded-lg p-4">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <p class="font-semibold text-gray-900">${toolName}</p>
                    <p class="text-sm text-gray-500">Quantity: ${mov.quantity}</p>
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
        </div>
    `;
            }).join('') || '<p class="text-gray-400 text-sm">No items found.</p>';

            // Summary
            document.getElementById('modal-total-amount').textContent = formatCurrency(rental.total_price);
            document.getElementById('modal-total-items').textContent = movements.length;

            // Tampilkan modal
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
    </script>
@endsection
