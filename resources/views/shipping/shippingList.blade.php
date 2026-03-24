@extends('layout.app')

@section('content')
    <script>
        const shippingsData = JSON.parse(atob('{{ base64_encode(json_encode($shippings)) }}'));
        const rentalsData = JSON.parse(atob('{{ base64_encode(json_encode($rentals)) }}'));
        const driversData = JSON.parse(atob('{{ base64_encode(json_encode($drivers)) }}'));
        const warehousesData = JSON.parse(atob('{{ base64_encode(json_encode($warehouses)) }}'));
        const movementsData = JSON.parse(atob('{{ base64_encode(json_encode($movements)) }}'));
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

    {{-- Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Delivery #</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Driver</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Rentals</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Destination</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Created</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shippings as $shipping)
                        @php
                            $driver = $drivers[$shipping->driver_id] ?? null;
                            $rentalIds = is_array($shipping->rental_id)
                                ? $shipping->rental_id
                                : json_decode($shipping->rental_id, true) ?? [];
                            $rentalCount = count($rentalIds);

                            $statusColor = match ($shipping->delivery_status) {
                                'delivered' => 'bg-green-100 text-green-800',
                                'in_transit' => 'bg-blue-100 text-blue-800',
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'cancelled' => 'bg-gray-100 text-gray-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition cursor-pointer"
                            onclick="openDeliveryModal('{{ $shipping->id }}')">
                            <td class="px-5 py-4 font-semibold text-gray-900">{{ $shipping->delivery_number }}</td>
                            <td class="px-5 py-4 text-gray-700">{{ $driver?->name ?? 'N/A' }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-medium">
                                    {{ $rentalCount }} rental{{ $rentalCount > 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-700">{{ $shipping->to_location }}</td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-block px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $shipping->delivery_status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 text-xs">
                                {{ \Carbon\Carbon::parse($shipping->created_at)->format('d M Y') }}
                            </td>
                            <td class="px-5 py-4" onclick="event.stopPropagation()">
                                <button onclick="openDeliveryModal('{{ $shipping->id }}')"
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400">No shipments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
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
            return 'Rp. ' + new Intl.NumberFormat('id-ID').format(amount);
        }

        function statusBadge(status) {
            const map = {
                'delivered': 'bg-green-100 text-green-800',
                'in_transit': 'bg-blue-100 text-blue-800',
                'pending': 'bg-yellow-100 text-yellow-800',
                'failed': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-700',
            };
            const label = status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
            const cls = map[status] ?? 'bg-gray-100 text-gray-700';
            return `<span class="inline-block px-3 py-1 rounded-full text-xs font-semibold ${cls}">${label}</span>`;
        }

        function openDeliveryModal(shippingId) {
            const shipping = shippingsData.find(s => s.id === shippingId);
            if (!shipping) return;

            const driver = driversData[shipping.driver_id] ?? null;
            const rentalIds = Array.isArray(shipping.rental_id) ?
                shipping.rental_id :
                JSON.parse(shipping.rental_id ?? '[]');
            const fromLocs = Array.isArray(shipping.from_location) ?
                shipping.from_location :
                JSON.parse(shipping.from_location ?? '[]');

            // Build rentals detail
            const rentalsHtml = rentalIds.map((rid, idx) => {
                const rental = rentalsData[rid] ?? null;
                const customer = rental?.customer ?? null;

                // from_location per rental = fromLocs[idx] (array of warehouse ids)
                const whIds = fromLocs[idx] ?? [];
                const fromNames = Array.isArray(whIds) ?
                    whIds.map(wid => warehousesData[wid]?.name ?? `Warehouse #${wid}`).join(', ') :
                    `Warehouse #${whIds}`;

                // Tools dari movements
                const movIds = rental ? JSON.parse(rental.movement_id ?? '[]') : [];
                const toolsHtml = movIds.map(mid => {
                    const mov = movementsData[mid] ?? null;
                    if (!mov) return '';
                    const name = mov.tool?.name ?? mid;
                    const rate = mov.tool?.daily_rate ?? 0;

                    // Durasi
                    const start = new Date(rental.rental_start_date);
                    const end = new Date(rental.rental_end_date);
                    const days = Math.max(1, Math.ceil((end - start) / 86400000));
                    const subtotal = rate * mov.quantity * days;

                    return `
                    <div class="border-l-4 border-blue-400 bg-blue-50 rounded-lg p-3">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">${name}</p>
                                <p class="text-xs text-gray-500">Qty: ${mov.quantity}</p>
                            </div>
                            <span class="text-sm font-bold text-blue-600">${formatCurrency(subtotal)}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs mt-1">
                            <div><p class="text-gray-500">Daily Rate:</p><p class="font-semibold">${formatCurrency(rate)}</p></div>
                            <div><p class="text-gray-500">Duration:</p><p class="font-semibold">${days} days</p></div>
                        </div>
                    </div>
                `;
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
                                <p class="font-semibold text-gray-900">${shipping.to_location}</p>
                            </div>
                        </div>
                        <div class="space-y-2">${toolsHtml || '<p class="text-gray-400 text-xs">No tools data.</p>'}</div>
                    </div>
                </div>
            `;
            }).join('');

            document.getElementById('deliveryModalContent').innerHTML = `
            {{-- Status + Delivery Number --}}
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Delivery Number</p>
                    <p class="text-xl font-bold text-gray-900">${shipping.delivery_number}</p>
                    <p class="text-xs text-gray-400 mt-1">Created: ${formatDate(shipping.created_at)}</p>
                </div>
                ${statusBadge(shipping.delivery_status)}
            </div>

            {{-- Driver Info --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Driver Information</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div><p class="text-gray-500 text-xs">Name</p><p class="font-semibold">${driver?.name ?? 'N/A'}</p></div>
                    <div><p class="text-gray-500 text-xs">Vehicle</p><p class="font-semibold">${driver?.vehicle_type ?? '-'}</p></div>
                    <div><p class="text-gray-500 text-xs">License Plate</p><p class="font-semibold">${driver?.license_plate ?? '-'}</p></div>
                    <div><p class="text-gray-500 text-xs">Phone</p><p class="font-semibold">${driver?.phone ?? '-'}</p></div>
                </div>
            </div>

            {{-- Timeline --}}
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

            {{-- Rentals --}}
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Rental Items (${rentalIds.length})</p>
                <div class="space-y-3">${rentalsHtml}</div>
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
    </script>
@endsection
