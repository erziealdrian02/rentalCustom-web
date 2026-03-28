@extends('layout.app')

@section('content')
    <script>
        const availableRentals = JSON.parse(atob('{{ base64_encode(json_encode($rentals)) }}'));
        const movementsByRentalId = JSON.parse(atob('{{ base64_encode(json_encode($movementsByRentalId)) }}'));
        const warehousesData = JSON.parse(atob('{{ base64_encode(json_encode($warehouses)) }}'));
    </script>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg mb-6">
            @foreach ($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <h2 class="text-xl font-semibold text-gray-900 mb-6">Create Delivery Shipment</h2>

    <form id="shipment-form" method="POST" action="{{ route('shipping.store') }}">
        @csrf
        <input type="hidden" name="driverId" id="hidden-driverId">
        <input type="hidden" name="rentalItems" id="hidden-rentalItems">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ===== KIRI ===== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Add Rental Card --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Rentals to Shipment</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Rental</label>
                        <select id="rentalSelect"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Choose a rental to add</option>
                            @foreach ($rentals as $rental)
                                <option value="{{ $rental->id }}">
                                    {{ $rental->invoice_number }} - {{ $rental->customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Dynamic Rental Forms --}}
                <div id="rental-forms-container" class="space-y-4"></div>

                {{-- Driver + Submit --}}
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Driver</label>
                        <select id="driverSelect" onchange="syncDriver()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a driver</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver['id'] }}">
                                    {{ $driver['name'] }} - {{ $driver['vehicle_type'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="button" onclick="submitShipment()"
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-lg transition">
                        Create Shipment
                    </button>
                </div>
            </div>

            {{-- ===== KANAN: Summary ===== --}}
            <div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipment Summary</h3>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-gray-600 text-sm">Rentals Added</p>
                        <p class="text-3xl font-bold text-blue-600" id="rental-count">0</p>
                    </div>

                    <div id="summary-list" class="text-gray-500 italic text-sm">
                        No rentals added yet.
                    </div>
                </div>
            </div>

        </div>
    </form>

    <script>
        // addedRentals: { rentalId, invoiceNumber, customerName, locations: [{warehouseId, warehouseName, toLocation}] }
        let addedRentals = [];

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

        function syncDriver() {
            document.getElementById('hidden-driverId').value = document.getElementById('driverSelect').value;
        }

        // Saat pilih rental → render form card baru (jika belum ada)
        document.getElementById('rentalSelect').addEventListener('change', function() {
            const rentalId = this.value;
            if (!rentalId) return;

            // Cek duplikat
            if (addedRentals.find(r => r.rentalId === rentalId)) {
                alert('This rental is already added.');
                this.value = '';
                return;
            }

            const rental = availableRentals.find(r => r.id === rentalId);
            if (!rental) return;

            const movements = movementsByRentalId[rentalId] ?? [];
            const warehouseIds = JSON.parse(rental.warehouse_id ?? '[]');

            // Hitung durasi & tools preview
            const startDate = new Date(rental.rental_start_date);
            const endDate = new Date(rental.rental_end_date);
            const days = Math.max(1, Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)));

            const toolsHtml = movements.map(mov => {
                const toolName = mov.tool?.name ?? mov.tool_id;
                const dailyRate = mov.tool?.daily_rate ?? 0;
                const subtotal = dailyRate * mov.quantity * days;
                return `
                <div class="border-l-4 border-blue-400 bg-blue-50 rounded-lg p-3">
                    <div class="flex justify-between items-start mb-1">
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">${toolName}</p>
                            <p class="text-xs text-gray-500">Qty: ${mov.quantity}</p>
                        </div>
                        <span class="text-sm font-bold text-blue-600">${formatCurrency(subtotal)}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs mt-1">
                        <div><p class="text-gray-500">Daily Rate:</p><p class="font-semibold">${formatCurrency(dailyRate)}</p></div>
                        <div><p class="text-gray-500">Duration:</p><p class="font-semibold">${days} days</p></div>
                    </div>
                </div>
            `;
            }).join('');

            // Render From Location per warehouse
            const fromLocationFields = warehouseIds.map((whId, idx) => {
                const wh = warehousesData[whId];
                const whName = wh ? wh.name : `Warehouse #${whId}`;
                return `
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        From Location ${warehouseIds.length > 1 ? `(${idx + 1})` : ''} 
                        <span class="text-blue-600 font-semibold">${whName}</span>
                    </label>
                    <input type="text"
                        data-rental="${rentalId}"
                        data-warehouse="${whId}"
                        data-field="from"
                        placeholder="e.g., Main Warehouse - Rack A"
                        value="${whName}"
                        class="from-location-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            `;
            }).join('');

            // Buat card form
            const cardHtml = `
            <div id="rental-form-${rentalId}" class="bg-white rounded-lg border-2 border-blue-200 shadow-sm overflow-hidden">
                
                {{-- Header --}}
                <div class="bg-blue-600 px-5 py-3 flex justify-between items-center">
                    <div>
                        <p class="text-white font-semibold">${rental.invoice_number}</p>
                        <p class="text-blue-100 text-sm">${rental.customer?.name ?? 'N/A'} · ${movements.length} tools · ${days} days</p>
                    </div>
                    <button type="button" onclick="removeRentalForm('${rentalId}')"
                        class="text-white hover:text-red-200 transition p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Tools preview --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Tools (${movements.length})</p>
                        <div class="space-y-2">${toolsHtml}</div>
                    </div>

                    {{-- Divider --}}
                    <hr class="border-gray-200">

                    {{-- Location inputs --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase mb-3">Delivery Route</p>
                        <div class="space-y-3">
                            ${fromLocationFields}
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">To Location (Destination)</label>
                                <input type="text"
                                    data-rental="${rentalId}"
                                    data-field="to"
                                    placeholder="e.g., Customer Site"
                                    class="to-location-input w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    {{-- Add button --}}
                    <button type="button" onclick="confirmAddRental('${rentalId}')"
                        class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm transition">
                        ✓ Confirm Add to Shipment
                    </button>
                </div>
            </div>
        `;

            document.getElementById('rental-forms-container').insertAdjacentHTML('beforeend', cardHtml);

            // Reset dropdown
            this.value = '';
        });

        function confirmAddRental(rentalId) {
            const rental = availableRentals.find(r => r.id === rentalId);
            if (!rental) return;

            const movements = movementsByRentalId[rentalId] ?? [];
            const warehouseIds = JSON.parse(rental.warehouse_id ?? '[]');

            // Ambil from locations
            const fromInputs = document.querySelectorAll(`.from-location-input[data-rental="${rentalId}"]`);
            const locations = [];

            let valid = true;
            fromInputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    return;
                }
                locations.push({
                    warehouseId: input.dataset.warehouse,
                    warehouseName: input.value.trim(),
                });
            });

            const toInput = document.querySelector(`.to-location-input[data-rental="${rentalId}"]`);
            if (!toInput || !toInput.value.trim()) {
                valid = false;
            }

            if (!valid) {
                alert('Please fill in all location fields.');
                return;
            }

            addedRentals.push({
                rentalId,
                invoiceNumber: rental.invoice_number,
                customerName: rental.customer?.name ?? 'N/A',
                itemCount: movements.length,
                locations,
                toLocation: toInput.value.trim(),
            });

            // Ubah card jadi "confirmed" state
            const card = document.getElementById(`rental-form-${rentalId}`);
            const confirmBtn = card.querySelector('button[onclick*="confirmAddRental"]');
            confirmBtn.outerHTML = `
            <div class="flex items-center gap-2 py-2 px-3 bg-green-50 border border-green-200 rounded-lg">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-green-700 font-semibold text-sm">Added to Shipment</span>
            </div>
        `;

            // Disable inputs
            card.querySelectorAll('input').forEach(i => i.disabled = true);
            card.classList.replace('border-blue-200', 'border-green-300');

            renderSummary();
        }

        function removeRentalForm(rentalId) {
            // Hapus dari array jika sudah di-confirm
            addedRentals = addedRentals.filter(r => r.rentalId !== rentalId);
            // Hapus card
            const card = document.getElementById(`rental-form-${rentalId}`);
            if (card) card.remove();
            renderSummary();
        }

        function renderSummary() {
            const counter = document.getElementById('rental-count');
            const summaryList = document.getElementById('summary-list');

            counter.textContent = addedRentals.length;

            if (addedRentals.length === 0) {
                summaryList.innerHTML = '<p class="text-gray-500 italic text-sm">No rentals added yet.</p>';
                return;
            }

            summaryList.innerHTML = '<div class="space-y-3">' + addedRentals.map(item => `
            <div class="border rounded-lg p-3 bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">${item.invoiceNumber}</p>
                        <p class="text-xs text-gray-500">${item.customerName} · ${item.itemCount} tools</p>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Added</span>
                </div>
                <div class="mt-2 space-y-1">
                    ${item.locations.map(loc => `
                            <p class="text-xs text-gray-500">📦 ${loc.warehouseName} → ${item.toLocation}</p>
                        `).join('')}
                </div>
            </div>
        `).join('') + '</div>';
        }

        function submitShipment() {
            const driverId = document.getElementById('driverSelect').value;

            if (!driverId) {
                alert('Please select a driver');
                return;
            }
            if (addedRentals.length === 0) {
                alert('Please add at least one rental');
                return;
            }

            document.getElementById('hidden-driverId').value = driverId;
            document.getElementById('hidden-rentalItems').value = JSON.stringify(addedRentals);
            document.getElementById('shipment-form').submit();
        }
    </script>
@endsection
