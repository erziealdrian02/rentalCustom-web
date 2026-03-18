{{-- resources/views/shippings/create.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Kirim data rentals ke JavaScript --}}
    <script>
        const availableRentals = @json($rentals);
    </script>

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg mb-6">
            @foreach ($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <h2 class="text-xl font-semibold text-gray-900 mb-6">Create Delivery Shipment</h2>

    {{-- Form utama — rentalItems dikirim sebagai JSON via hidden input --}}
    <form id="shipment-form" method="POST" action="{{ route('shipping.store') }}">
        @csrf
        <input type="hidden" name="driverId" id="hidden-driverId">
        <input type="hidden" name="rentalItems" id="hidden-rentalItems">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- ===== KIRI: Form Input (2/3) ===== --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 space-y-6">

                    {{-- Add Rental Section --}}
                    <div class="border-b pb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Rentals to Shipment</h3>
                        <div class="space-y-4">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Rental</label>
                                <select id="rentalSelect"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Choose a rental to add</option>
                                    @foreach ($rentals as $rental)
                                        <option value="{{ $rental['id'] }}">
                                            {{ $rental['invoiceNumber'] }} - {{ $rental['customerName'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">From Location</label>
                                    <input type="text" id="fromLocation" placeholder="e.g., Main Warehouse"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">To Location</label>
                                    <input type="text" id="toLocation" placeholder="e.g., Customer Site"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <button type="button" onclick="addRentalToShipment()"
                                class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition">
                                + Add Rental
                            </button>
                        </div>
                    </div>

                    {{-- Driver Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Driver</label>
                        <select id="driverSelect" onchange="syncDriver()"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a driver</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver['id'] }}">
                                    {{ $driver['name'] }} - {{ $driver['vehicleType'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Submit --}}
                    <button type="button" onclick="submitShipment()"
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold text-lg transition">
                        Create Shipment
                    </button>

                </div>
            </div>

            {{-- ===== KANAN: Summary (1/3) ===== --}}
            <div>
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 sticky top-24">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipment Summary</h3>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-gray-600 text-sm">Rentals Added</p>
                        <p class="text-3xl font-bold text-blue-600" id="rental-count">0</p>
                    </div>

                    <div class="border-t pt-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Added Rentals</h4>
                        <div id="added-rentals" class="text-gray-500 italic text-sm">
                            No rentals added yet.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>

    <script>
        let addedRentals = [];

        function syncDriver() {
            document.getElementById('hidden-driverId').value = document.getElementById('driverSelect').value;
        }

        function addRentalToShipment() {
            const rentalSelect = document.getElementById('rentalSelect');
            const fromLocation = document.getElementById('fromLocation').value.trim();
            const toLocation = document.getElementById('toLocation').value.trim();
            const driverSelect = document.getElementById('driverSelect');

            if (!rentalSelect.value || !fromLocation || !toLocation || !driverSelect.value) {
                alert('Please fill in all fields (Rental, From, To, and Driver)');
                return;
            }

            const rentalId = parseInt(rentalSelect.value);

            // Cegah duplikat
            if (addedRentals.find(r => r.rentalId === rentalId)) {
                alert('This rental is already added to the shipment');
                return;
            }

            const rental = availableRentals.find(r => r.id === rentalId);
            if (!rental) return;

            addedRentals.push({
                rentalId: rentalId,
                invoiceNumber: rental.invoiceNumber,
                customerName: rental.customerName,
                customerId: rental.customerId ?? null,
                itemCount: rental.items ? rental.items.length : 1,
                fromLocation: fromLocation,
                toLocation: toLocation,
            });

            renderRentals();

            // Reset field
            rentalSelect.value = '';
            document.getElementById('fromLocation').value = '';
            document.getElementById('toLocation').value = '';
        }

        function removeRental(rentalId) {
            addedRentals = addedRentals.filter(r => r.rentalId !== rentalId);
            renderRentals();
        }

        function renderRentals() {
            const container = document.getElementById('added-rentals');
            const counter = document.getElementById('rental-count');
            counter.textContent = addedRentals.length;

            if (addedRentals.length === 0) {
                container.innerHTML = '<p class="text-gray-500 italic text-sm">No rentals added yet.</p>';
                return;
            }

            container.innerHTML = '<div class="space-y-3">' + addedRentals.map(item => `
            <div class="border rounded-lg p-4 bg-gray-50 flex items-center justify-between">
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-sm">${item.invoiceNumber}</p>
                    <p class="text-sm text-gray-600">${item.customerName} - ${item.itemCount} items</p>
                    <p class="text-xs text-gray-500 mt-1">${item.fromLocation} → ${item.toLocation}</p>
                </div>
                <button type="button" onclick="removeRental(${item.rentalId})"
                        class="px-2 py-2 text-red-600 hover:bg-red-50 rounded ml-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
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
                alert('Please add at least one rental to the shipment');
                return;
            }

            // Sync hidden inputs lalu submit form
            document.getElementById('hidden-driverId').value = driverId;
            document.getElementById('hidden-rentalItems').value = JSON.stringify(addedRentals);
            document.getElementById('shipment-form').submit();
        }
    </script>
@endsection
