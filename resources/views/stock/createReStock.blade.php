{{-- resources/views/rentals/create.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Kirim data PHP ke JavaScript --}}
    <script>
        const pricingMap = @json($pricingMap);
        {{-- { toolId: { dailyRate, weeklyRate, monthlyRate } } --}}
    </script>

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg mb-6">
            @foreach ($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="max-w-full">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">New Rental Transaction</h2>

        {{-- Form utama — items dikirim sebagai JSON string via hidden input --}}
        <form id="rental-form" method="POST" action="{{ route('transactions.rentals.store') }}">
            @csrf
            <input type="hidden" name="customerId" id="hidden-customerId">
            <input type="hidden" name="items" id="hidden-items">

            {{-- Two-Column Layout --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" style="min-height: 600px;">

                {{-- KIRI: Form input --}}
                <div class="flex flex-col gap-6">

                    {{-- Card: Rental Details --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Details</h3>

                        {{-- Customer --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                            <select id="customerId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="syncCustomer(); updateSubmitBtn()">
                                <option value="">Select Customer</option>
                                @foreach ($getCustomers as $customer)
                                    <option value="{{ $customer['id'] }}">{{ $customer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Tools to Rental</h3>

                        {{-- Add Tool Form --}}
                        <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tool *</label>
                                    <select id="toolId"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        onchange="updateDailyRate()">
                                        <option value="">Select Tool</option>
                                        @foreach ($getTools as $tool)
                                            <option value="{{ $tool['id'] }}">{{ $tool['name'] }} ({{ $tool['code'] }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                                    <input type="number" id="quantity" value="1" min="1"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date *</label>
                                    <input type="date" id="startDate"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date *</label>
                                    <input type="date" id="endDate"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration Type *</label>
                                    <select id="durationType"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        onchange="updateDailyRate()">
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rate</label>
                                    <input type="number" id="dailyRate" step="0.01" readonly
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:outline-none">
                                </div>
                            </div>

                            <button type="button" onclick="addRentalItem()"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                + Add Tool to Rental
                            </button>
                        </div>
                    </div>

                    {{-- Card: Summary + Submit --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Summary</h3>
                        <div id="rentalSummary" class="space-y-3 mb-6">
                            <p class="text-gray-600 text-sm">No items added yet</p>
                        </div>
                        <button type="button" onclick="submitRental()" id="submitBtn" disabled
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                            Submit Rental
                        </button>
                    </div>

                </div>

                {{-- KANAN: Daftar item yang ditambahkan --}}
                <div class="flex flex-col bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Tools Added to This Rental</h3>
                    </div>
                    <div id="rentalItemsList" class="flex-1 overflow-y-auto p-6 space-y-4">
                        <p class="text-gray-600 text-center py-8">
                            No items added yet. Add tools above to start building the rental.
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        let rentalItems = [];
        let nextItemId = 1;

        // Set default start date = hari ini
        document.getElementById('startDate').value = new Date().toISOString().split('T')[0];

        // ─── Sync customer ke hidden input ───────────────────────
        function syncCustomer() {
            document.getElementById('hidden-customerId').value = document.getElementById('customerId').value;
        }

        // ─── Update rate berdasarkan tool + duration type ────────
        function updateDailyRate() {
            const toolId = parseInt(document.getElementById('toolId').value);
            const durationType = document.getElementById('durationType').value;
            const pricing = pricingMap[toolId];

            if (!pricing) {
                document.getElementById('dailyRate').value = '';
                return;
            }

            const rateKey = durationType === 'weekly' ? 'weeklyRate' :
                durationType === 'monthly' ? 'monthlyRate' :
                'dailyRate';

            document.getElementById('dailyRate').value = pricing[rateKey];
        }

        // ─── Hitung selisih hari ─────────────────────────────────
        function calculateDays() {
            const start = new Date(document.getElementById('startDate').value);
            const end = new Date(document.getElementById('endDate').value);
            const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
            return diff > 0 ? diff : 1;
        }

        // ─── Tambah item ke rental ───────────────────────────────
        function addRentalItem() {
            const toolId = parseInt(document.getElementById('toolId').value);
            const quantity = parseInt(document.getElementById('quantity').value) || 1;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const durationType = document.getElementById('durationType').value;
            const dailyRate = parseFloat(document.getElementById('dailyRate').value) || 0;
            const customerId = document.getElementById('customerId').value;

            if (!customerId) {
                alert('Please select a customer first');
                return;
            }
            if (!toolId) {
                alert('Please select a tool');
                return;
            }
            if (!startDate || !endDate) {
                alert('Please select start and end dates');
                return;
            }
            if (new Date(startDate) > new Date(endDate)) {
                alert('End date must be after start date');
                return;
            }
            if (dailyRate === 0) {
                alert('No pricing found for the selected tool');
                return;
            }

            // Cari nama tool dari option
            const toolSelect = document.getElementById('toolId');
            const toolName = toolSelect.options[toolSelect.selectedIndex].text;
            const days = calculateDays();
            const subtotal = quantity * dailyRate * days;

            rentalItems.push({
                id: nextItemId++,
                toolId,
                toolName,
                quantity,
                startDate,
                endDate,
                durationType,
                dailyRate,
                days,
                subtotal,
            });

            updateUI();

            // Reset field tool (customer & dates dibiarkan)
            document.getElementById('toolId').value = '';
            document.getElementById('quantity').value = '1';
            document.getElementById('dailyRate').value = '';
        }

        // ─── Hapus item ──────────────────────────────────────────
        function removeRentalItem(itemId) {
            rentalItems = rentalItems.filter(i => i.id !== itemId);
            updateUI();
        }

        // ─── Format currency ─────────────────────────────────────
        function fmt(amount) {
            return '$' + parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // ─── Update semua UI ─────────────────────────────────────
        function updateUI() {
            const summaryDiv = document.getElementById('rentalSummary');
            const itemsListDiv = document.getElementById('rentalItemsList');

            if (rentalItems.length === 0) {
                summaryDiv.innerHTML = '<p class="text-gray-600 text-sm">No items added yet</p>';
                itemsListDiv.innerHTML =
                    '<p class="text-gray-600 text-center py-8">No items added yet. Add tools above to start building the rental.</p>';
                updateSubmitBtn();
                return;
            }

            const totalPrice = rentalItems.reduce((sum, i) => sum + i.subtotal, 0);

            // Summary
            summaryDiv.innerHTML = `
            <div class="border-b border-gray-200 pb-3">
                <p class="text-gray-600 text-sm">Items: <span class="font-semibold text-gray-900">${rentalItems.length}</span></p>
                <p class="text-gray-600 text-sm">Total Price: <span class="font-bold text-lg text-green-600">${fmt(totalPrice)}</span></p>
            </div>
            <div class="text-xs text-gray-500 space-y-1 pt-2">
                ${rentalItems.map(i => `<p>${i.quantity}x ${i.toolName}</p>`).join('')}
            </div>
        `;

            // Items list (kanan)
            itemsListDiv.innerHTML = rentalItems.map(item => `
            <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h4 class="font-semibold text-gray-900">${item.toolName}</h4>
                        <p class="text-sm text-gray-600">${item.quantity}x @ ${fmt(item.dailyRate)}/day</p>
                    </div>
                    <button onclick="removeRentalItem(${item.id})"
                            class="text-red-600 hover:text-red-700 font-medium text-sm">
                        Remove
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Start Date</p>
                        <p class="font-medium text-gray-900">${new Date(item.startDate).toLocaleDateString()}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">End Date</p>
                        <p class="font-medium text-gray-900">${new Date(item.endDate).toLocaleDateString()}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Duration</p>
                        <p class="font-medium text-gray-900">${item.days} days</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase">Type</p>
                        <p class="font-medium text-gray-900 capitalize">${item.durationType}</p>
                    </div>
                </div>
                <div class="bg-blue-50 p-3 rounded border border-blue-100">
                    <p class="text-sm text-blue-900">Subtotal: <span class="font-bold text-lg">${fmt(item.subtotal)}</span></p>
                </div>
            </div>
        `).join('');

            updateSubmitBtn();
        }

        // ─── Update state tombol Submit ──────────────────────────
        function updateSubmitBtn() {
            const btn = document.getElementById('submitBtn');
            const customerId = document.getElementById('customerId').value;
            btn.disabled = !customerId || rentalItems.length === 0;
        }

        // ─── Submit: masukkan items ke hidden input lalu submit ──
        function submitRental() {
            const customerId = document.getElementById('customerId').value;
            if (!customerId || rentalItems.length === 0) {
                alert('Please select a customer and add at least one tool');
                return;
            }

            // Sinkron hidden inputs
            document.getElementById('hidden-customerId').value = customerId;
            document.getElementById('hidden-items').value = JSON.stringify(rentalItems);

            document.getElementById('rental-form').submit();
        }
    </script>
@endsection
