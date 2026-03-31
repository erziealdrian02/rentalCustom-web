@extends('layout.app')

@section('content')
    {{-- Pass data to JS --}}
    <script>
        const rentalsData = @json($rentals);
    </script>

    {{-- ===================== MODAL ===================== --}}
    <div id="rentalDetailsModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">

            <div class="sticky top-0 bg-white border-b p-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Rental Details</h2>
                <button onclick="closeRentalModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div id="rentalModalContent" class="p-6 space-y-6"></div>

            <div class="border-t p-6 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeRentalModal()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Close
                </button>
            </div>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Active Rental Monitoring</h2>
        <div class="text-sm text-gray-600">
            Total Active: <span class="font-semibold text-gray-900">{{ count($rentals) }}</span>
        </div>
    </div>

    {{-- Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rentals as $rental)
            @php
                $days = $rental['daysRemaining'];
                $gradientColor =
                    $days < 0
                        ? 'from-red-500 to-red-600'
                        : ($days <= 3
                            ? 'from-yellow-500 to-yellow-600'
                            : 'from-green-500 to-green-600');
                $badgeColor =
                    $days < 0
                        ? 'bg-red-100 text-red-800'
                        : ($days <= 3
                            ? 'bg-yellow-100 text-yellow-800'
                            : 'bg-green-100 text-green-800');
                $daysColor = $days < 0 ? 'text-red-600' : ($days <= 3 ? 'text-yellow-600' : 'text-green-600');
                $toolNames = implode(', ', array_column($rental['items'], 'toolName'));
            @endphp

            <button onclick="openRentalModal('{{ $rental['id'] }}')"
                class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition text-left w-full">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r {{ $gradientColor }} text-white p-4">
                    <h3 class="font-bold text-lg">{{ $rental['invoiceNumber'] }}</h3>
                    <p class="text-sm opacity-90">{{ $rental['customerName'] }}</p>
                </div>

                {{-- Card Body --}}
                <div class="p-4 space-y-3">
                    <div>
                        <p class="text-xs text-gray-600 font-semibold uppercase">Items</p>
                        <p class="font-semibold text-gray-900 truncate">{{ $toolNames ?: '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded p-3">
                        <p class="text-xs text-gray-600 uppercase font-semibold">Days Remaining</p>
                        <p class="text-2xl font-bold {{ $daysColor }}">{{ $days }} days</p>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="px-2 py-1 rounded text-xs font-semibold {{ $badgeColor }}">
                            {{ $rental['rentalStatus'] }}
                        </span>
                        <span class="text-xs text-gray-600">
                            {{ $rental['driverName'] ? 'Driver: ' . $rental['driverName'] : 'No driver assigned' }}
                        </span>
                    </div>

                    <div class="pt-2 border-t">
                        <p class="text-xs text-gray-600">Total Revenue</p>
                        <p class="text-lg font-bold text-blue-600">
                            Rp {{ number_format($rental['totalRevenue'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </button>
        @empty
            <div class="col-span-3 text-center text-gray-400 py-16">No active rentals found.</div>
        @endforelse
    </div>

    {{-- ===================== JAVASCRIPT ===================== --}}
    <script>
        const statusList = ['On Track', 'Delivered', 'Pending', 'Overdue', 'Returning', 'On Check'];

        function fmt(amount) {
            return 'Rp ' + parseFloat(amount).toLocaleString('id-ID');
        }

        function fmtDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function getDaysColor(days) {
            return days < 0 ? 'text-red-600' : days <= 3 ? 'text-yellow-600' : 'text-green-600';
        }

        function getStatusBadgeColor(status) {
            const map = {
                'On Track': 'bg-green-100 text-green-800',
                'Delivered': 'bg-blue-100 text-blue-800',
                'Pending': 'bg-yellow-100 text-yellow-800',
                'Overdue': 'bg-red-100 text-red-800',
                'Returning': 'bg-purple-100 text-purple-800',
                'On Check': 'bg-gray-100 text-gray-800',
            };
            return map[status] ?? 'bg-gray-100 text-gray-700';
        }

        function openRentalModal(rentalId) {
            const rental = rentalsData.find(r => r.id === rentalId);
            if (!rental) return;

            const days = rental.daysRemaining;
            const daysColor = getDaysColor(days);

            // --- Delivery section ---
            const deliveryHtml = rental.driverName ?
                `<div class="bg-green-50 border border-green-200 rounded-lg p-4">
               <h4 class="font-semibold text-gray-900 mb-3">Delivery Information</h4>
               <div class="grid grid-cols-2 gap-4">
                   <div>
                       <p class="text-gray-600 text-sm">Delivery No.</p>
                       <p class="font-semibold text-gray-900">${rental.deliveryNumber ?? '-'}</p>
                   </div>
                   <div>
                       <p class="text-gray-600 text-sm">Status</p>
                       <p class="font-semibold text-gray-900">${rental.deliveryStatus ?? '-'}</p>
                   </div>
                   <div>
                       <p class="text-gray-600 text-sm">Driver</p>
                       <p class="font-semibold text-gray-900">${rental.driverName}</p>
                   </div>
                   <div>
                       <p class="text-gray-600 text-sm">Destination</p>
                       <p class="font-semibold text-gray-900">${rental.deliveryLocation ?? '-'}</p>
                   </div>
                   ${rental.departureTime
                       ? `<div><p class="text-gray-600 text-sm">Departure</p><p class="font-semibold text-gray-900">${rental.departureTime}</p></div>`
                       : ''}
                   ${rental.estimatedDeliveryTime
                       ? `<div><p class="text-gray-600 text-sm">Est. Arrival</p><p class="font-semibold text-gray-900">${rental.estimatedDeliveryTime}</p></div>`
                       : ''}
                   ${rental.actualDeliveryTime
                       ? `<div class="col-span-2"><p class="text-gray-600 text-sm">Actual Arrival</p><p class="font-semibold text-gray-900">${rental.actualDeliveryTime}</p></div>`
                       : ''}
               </div>
           </div>` :
                `<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
               <p class="text-gray-700">⚠️ Delivery not yet assigned</p>
           </div>`;

            // --- Items list ---
            const itemsHtml = rental.items.length ?
                rental.items.map((item, idx) => `
            <div class="flex items-center justify-between p-4 ${idx % 2 === 0 ? 'bg-gray-50' : 'bg-white'} border-b last:border-b-0">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">${item.toolName}</p>
                    <p class="text-sm text-gray-600">Qty: ${item.quantity} × ${fmt(item.dailyRate)}/day</p>
                </div>
                <div class="text-right">
                    <p class="font-semibold text-gray-900">${fmt(item.subtotal)}</p>
                </div>
            </div>`).join('') :
                `<div class="p-4 text-gray-400 text-center">No items found</div>`;

            // --- Status buttons ---
            const statusButtons = statusList.map(status => `
        <button onclick="updateRentalStatus('${rental.id}', '${status}')"
                class="px-3 py-2 rounded-lg text-sm font-semibold border transition
                       ${rental.rentalStatus === status
                           ? 'bg-blue-600 text-white border-blue-600'
                           : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'}">
            ${status}
        </button>`).join('');

            document.getElementById('rentalModalContent').innerHTML = `
        <div class="space-y-6">

            {{-- Basic Info --}}
            <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                <div>
                    <p class="text-gray-600 text-sm">Invoice Number</p>
                    <p class="text-lg font-semibold text-gray-900">${rental.invoiceNumber}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Status</p>
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${getStatusBadgeColor(rental.rentalStatus)}">
                        ${rental.rentalStatus}
                    </span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Customer</p>
                    <p class="text-lg font-semibold text-gray-900">${rental.customerName}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Created</p>
                    <p class="text-lg font-semibold text-gray-900">${fmtDate(rental.createdDate)}</p>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-3">Rental Timeline</h4>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-gray-600 text-sm">Start Date</p>
                        <p class="font-semibold text-gray-900">${fmtDate(rental.rentalStartDate)}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">End Date</p>
                        <p class="font-semibold text-gray-900">${fmtDate(rental.rentalEndDate)}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Days Remaining</p>
                        <p class="font-bold text-lg ${daysColor}">${days} days</p>
                    </div>
                </div>
            </div>

            {{-- Delivery --}}
            ${deliveryHtml}

            {{-- Items --}}
            <div>
                <h4 class="font-semibold text-gray-900 mb-3">Rented Items</h4>
                <div class="border rounded-lg overflow-hidden">${itemsHtml}</div>
            </div>

            {{-- Revenue --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold text-green-700">${fmt(rental.totalRevenue)}</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-gray-600 text-sm">Daily Average</p>
                    <p class="text-2xl font-bold text-blue-700">${fmt(rental.dailyAverage)}</p>
                </div>
            </div>

            {{-- Update Status --}}
            <div class="border-t pt-4">
                <h4 class="font-semibold text-gray-900 mb-3">Update Status</h4>
                <div class="grid grid-cols-2 gap-2" id="status-buttons">${statusButtons}</div>
            </div>

        </div>`;

            document.getElementById('rentalDetailsModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRentalModal() {
            document.getElementById('rentalDetailsModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function updateRentalStatus(rentalId, newStatus) {
            fetch(`/monitoring-active/${rentalId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        status: newStatus
                    }),
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const rental = rentalsData.find(r => r.id === rentalId);
                        if (rental) rental.rentalStatus = newStatus;

                        // Re-render status buttons
                        const buttons = statusList.map(status => `
                <button onclick="updateRentalStatus('${rentalId}', '${status}')"
                        class="px-3 py-2 rounded-lg text-sm font-semibold border transition
                               ${status === newStatus
                                   ? 'bg-blue-600 text-white border-blue-600'
                                   : 'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'}">
                    ${status}
                </button>`).join('');
                        document.getElementById('status-buttons').innerHTML = buttons;

                        // Update badge di modal
                        const badge = document.querySelector('#rentalModalContent .rounded-full');
                        if (badge) {
                            badge.className =
                                `inline-block px-3 py-1 rounded-full text-sm font-semibold ${getStatusBadgeColor(newStatus)}`;
                            badge.textContent = newStatus;
                        }

                        setTimeout(() => window.location.reload(), 800);
                    }
                })
                .catch(() => alert('Failed to update status. Please try again.'));
        }

        // Tutup modal saat klik luar
        document.getElementById('rentalDetailsModal').addEventListener('click', function(e) {
            if (e.target === this) closeRentalModal();
        });
    </script>
@endsection
