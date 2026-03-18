{{-- resources/views/shippings/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Kirim data shippings ke JavaScript --}}
    <script>
        const shippings = @json($shippings);
    </script>

    {{-- ===================== MODAL DELIVERY DETAIL ===================== --}}
    <div id="deliveryModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">

            <div class="sticky top-0 bg-white border-b p-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Delivery Details</h2>
                <button onclick="closeDeliveryModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="deliveryModalContent" class="p-6 space-y-6"></div>

            <div class="border-t p-6 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeDeliveryModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Shipping Deliveries</h2>
        <a href="{{ route('shipping.form') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Delivery
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Shipments</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalShipments }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Delivered</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $delivered }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">In Transit</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $inTransit }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pending }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Delivery #</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Driver</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Customer</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Destination</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Departure</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($shippings as $shipping)
                        @php
                            $statusColor = match ($shipping['status']) {
                                'Delivered' => 'bg-green-100 text-green-800',
                                'In Transit' => 'bg-blue-100 text-blue-800',
                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $firstRental = $shipping['rentals'][0] ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $shipping['deliveryNumber'] }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $shipping['driverName'] }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $firstRental['customerName'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $firstRental['toLocation'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-900">{{ $shipping['departureTime'] }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                    {{ $shipping['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="openDeliveryModal({{ $shipping['id'] }})"
                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No shipments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function getStatusColor(status) {
            const map = {
                'Delivered': 'bg-green-100 text-green-800',
                'In Transit': 'bg-blue-100 text-blue-800',
                'Pending': 'bg-yellow-100 text-yellow-800',
            };
            return map[status] ?? 'bg-gray-100 text-gray-700';
        }

        function openDeliveryModal(deliveryId) {
            const delivery = shippings.find(d => d.id === deliveryId);
            if (!delivery) return;

            const rentalsHtml = delivery.rentals.map(rental => `
            <div class="border rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-900">${rental.customerName}</p>
                        <p class="text-sm text-gray-600">Invoice: ${rental.invoiceNumber}</p>
                    </div>
                    <span class="text-sm font-semibold px-2 py-1 rounded bg-blue-200 text-blue-800">
                        ${rental.items} items
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div class="bg-white p-2 rounded border">
                        <p class="text-gray-600">From</p>
                        <p class="font-semibold">${rental.fromLocation}</p>
                    </div>
                    <div class="bg-white p-2 rounded border">
                        <p class="text-gray-600">To</p>
                        <p class="font-semibold">${rental.toLocation}</p>
                    </div>
                </div>
            </div>
        `).join('');

            const arrivalHtml = delivery.arrivalTime ?
                `<div class="flex items-center gap-3">
                   <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                   <div>
                       <p class="text-gray-600 text-sm">Arrival Time</p>
                       <p class="font-semibold text-gray-900">${delivery.arrivalTime}</p>
                   </div>
               </div>` :
                `<div class="flex items-center gap-3">
                   <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                   <div>
                       <p class="text-gray-600 text-sm">Arrival Time</p>
                       <p class="font-semibold text-gray-900">Not Yet Arrived</p>
                   </div>
               </div>`;

            const proofHtml = delivery.status === 'Delivered' ?
                `<div class="bg-green-50 border border-green-200 rounded-lg p-4">
                   <h4 class="font-semibold text-gray-900 mb-3">Delivery Proof</h4>
                   ${delivery.proofImage
                       ? `<img src="${delivery.proofImage}" alt="Proof" class="w-full rounded-lg border mb-2">
                              <p class="text-sm text-gray-600">Proof image uploaded and verified</p>`
                       : `<p class="text-gray-900">Proof verified</p>`}
                   <p class="text-sm text-gray-600 mt-2">${delivery.notes}</p>
               </div>` :
                `<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                   <h4 class="font-semibold text-gray-900 mb-3">Notes</h4>
                   <p class="text-gray-900">${delivery.notes}</p>
               </div>`;

            document.getElementById('deliveryModalContent').innerHTML = `
            <div class="space-y-6">
                {{-- Header --}}
                <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                    <div>
                        <p class="text-gray-600 text-sm">Delivery Number</p>
                        <p class="text-lg font-semibold text-gray-900">${delivery.deliveryNumber}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${getStatusColor(delivery.status)}">
                            ${delivery.status}
                        </span>
                    </div>
                </div>

                {{-- Driver Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Driver Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600 text-sm">Driver Name</p>
                            <p class="font-semibold text-gray-900">${delivery.driverName}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Vehicle Type</p>
                            <p class="font-semibold text-gray-900">${delivery.vehicleType}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">License Plate</p>
                            <p class="font-semibold text-gray-900">${delivery.licensePlate}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Phone</p>
                            <p class="font-semibold text-gray-900">${delivery.phone}</p>
                        </div>
                    </div>
                </div>

                {{-- Timeline --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-3">Delivery Timeline</h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 bg-green-600 rounded-full"></div>
                            <div>
                                <p class="text-gray-600 text-sm">Departure Time</p>
                                <p class="font-semibold text-gray-900">${delivery.departureTime}</p>
                            </div>
                        </div>
                        ${arrivalHtml}
                    </div>
                </div>

                {{-- Items --}}
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Items Being Delivered</h4>
                    <div class="space-y-3">${rentalsHtml}</div>
                </div>

                ${proofHtml}
            </div>
        `;

            document.getElementById('deliveryModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDeliveryModal() {
            document.getElementById('deliveryModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Tutup modal klik luar
        document.getElementById('deliveryModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeliveryModal();
        });
    </script>
@endsection
