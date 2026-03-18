{{-- resources/views/returns/create.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Kirim data rentals ke JavaScript --}}
    <script>
        const rentalsData = @json($rentals);
    </script>

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg mb-6">
            @foreach ($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form — auditItems dikirim sebagai JSON via hidden input --}}
    <form id="return-form" method="POST" action="{{ route('returns.store') }}">
        @csrf
        <input type="hidden" name="rentalId" id="hidden-rentalId">
        <input type="hidden" name="auditItems" id="hidden-auditItems">

        <div class="max-w-7xl">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Process Tool Return with Audit</h2>

            {{-- Select Rental --}}
            <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6 mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Rental to Return</label>
                <select id="rentalSelect" onchange="selectRental()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium">
                    <option value="">Choose a rental...</option>
                    @foreach ($rentals as $rental)
                        <option value="{{ $rental['id'] }}">
                            {{ $rental['invoiceNumber'] }} - {{ $rental['customerName'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Details Section (hidden sampai rental dipilih) --}}
            <div id="detailsSection" class="hidden">

                {{-- Two Column: Original | Audit --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                    {{-- KIRI: Original Rental (read-only) --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span
                                class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            Original Rental Details
                        </h3>
                        <div id="originalToolsContent"></div>
                    </div>

                    {{-- KANAN: Audit Return (editable) --}}
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span
                                class="w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            Returning Rental - Audit
                        </h3>
                        <div id="returningToolsContent"></div>
                    </div>
                </div>

                {{-- Revenue Summary --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Revenue Summary</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg border border-gray-300 p-4">
                            <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Original Revenue</p>
                            <p class="text-2xl font-bold text-green-700" id="originalRevenue">-</p>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-300 p-4">
                            <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Total Loss</p>
                            <p class="text-2xl font-bold text-red-700" id="totalLoss">-</p>
                        </div>
                        <div class="bg-white rounded-lg border border-gray-300 p-4">
                            <p class="text-xs text-gray-600 uppercase font-semibold mb-1">Net Revenue</p>
                            <p class="text-2xl font-bold text-blue-700" id="netRevenue">-</p>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="button" onclick="submitReturn()"
                    class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-bold text-lg transition shadow-md">
                    Submit Return &amp; Process Audit
                </button>
            </div>
        </div>
    </form>

    <script>
        let selectedRentalId = null;
        let
        auditItems = {}; // { toolId: { toolId, toolName, quantity, originalRate, originalTotal, good, damaged, lost, sold } }

        function fmt(amount) {
            return '$' + parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function fmtDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // ─── Pilih rental → tampilkan detail ─────────────────────
        function selectRental() {
            const rentalId = parseInt(document.getElementById('rentalSelect').value);
            if (!rentalId) {
                selectedRentalId = null;
                document.getElementById('detailsSection').classList.add('hidden');
                return;
            }

            const rental = rentalsData.find(r => r.id === rentalId);
            if (!rental) return;

            selectedRentalId = rentalId;
            document.getElementById('hidden-rentalId').value = rentalId;

            // Init auditItems
            auditItems = {};
            rental.items.forEach(item => {
                auditItems[item.toolId] = {
                    toolId: item.toolId,
                    toolName: item.toolName,
                    quantity: item.quantity,
                    originalRate: item.dailyRate,
                    originalTotal: item.subtotal,
                    good: item.quantity,
                    damaged: 0,
                    lost: 0,
                    sold: 0,
                };
            });

            renderDetails(rental);
            updateRevenueSummary(rental);
            document.getElementById('detailsSection').classList.remove('hidden');
        }

        // ─── Render kolom kiri (read-only) ───────────────────────
        function renderOriginal(rental) {
            let html = `
            <div class="mb-6 pb-6 border-b">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Invoice</p><p class="font-semibold text-gray-900">${rental.invoiceNumber}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Customer</p><p class="font-semibold text-gray-900">${rental.customerName}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Rental Start</p><p class="font-semibold text-gray-900">${fmtDate(rental.rentalStartDate)}</p></div>
                    <div><p class="text-xs text-gray-500 uppercase font-semibold">Rental End</p><p class="font-semibold text-gray-900">${fmtDate(rental.rentalEndDate)}</p></div>
                </div>
                <p class="text-sm font-semibold text-gray-700 mb-3">Items Rented:</p>
            </div>
            <div class="space-y-4">
        `;

            rental.items.forEach(item => {
                html += `
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">${item.toolName}</h4>
                            <p class="text-xs text-gray-600">Tool ID: ${item.toolId}</p>
                        </div>
                        <span class="bg-gray-200 text-gray-800 px-3 py-1 rounded text-sm font-semibold">Qty: ${item.quantity}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div><p class="text-gray-600">Daily Rate</p><p class="font-semibold text-gray-900">${fmt(item.dailyRate)}</p></div>
                        <div><p class="text-gray-600">Subtotal</p><p class="font-bold text-green-700">${fmt(item.subtotal)}</p></div>
                    </div>
                </div>
            `;
            });

            html += '</div>';
            document.getElementById('originalToolsContent').innerHTML = html;
        }

        // ─── Render kolom kanan (audit / editable) ───────────────
        function renderAudit(rental) {
            let html = `
            <div class="mb-6 pb-6 border-b">
                <p class="text-sm font-semibold text-gray-700 mb-3">Audit Return Items:</p>
            </div>
            <div class="space-y-4">
        `;

            rental.items.forEach(item => {
                const audit = auditItems[item.toolId];
                const damageValueLoss = audit.damaged * audit.originalRate;
                const lostValueLoss = audit.lost * audit.originalRate;
                const soldRevenue = audit.sold * (audit.originalRate * 0.5);
                const recoveryValue = audit.good * audit.originalRate + soldRevenue;

                html += `
                <div class="bg-white border border-gray-300 rounded-lg p-4">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h4 class="font-semibold text-gray-900">${item.toolName}</h4>
                            <p class="text-xs text-gray-600">Qty: ${item.quantity} unit(s)</p>
                        </div>
                    </div>

                    <div class="mb-4 pb-4 border-b">
                        <p class="text-xs font-semibold text-gray-700 uppercase mb-3">Condition Count</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">Good</label>
                                <input type="number" min="0" max="${item.quantity}" value="${audit.good}"
                                       onchange="updateAuditCount(${item.toolId}, 'good', this.value)"
                                       class="w-full px-3 py-2 border border-green-300 rounded bg-green-50 text-center font-semibold text-green-800">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">Damaged</label>
                                <input type="number" min="0" max="${item.quantity}" value="${audit.damaged}"
                                       onchange="updateAuditCount(${item.toolId}, 'damaged', this.value)"
                                       class="w-full px-3 py-2 border border-red-300 rounded bg-red-50 text-center font-semibold text-red-800">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">Lost</label>
                                <input type="number" min="0" max="${item.quantity}" value="${audit.lost}"
                                       onchange="updateAuditCount(${item.toolId}, 'lost', this.value)"
                                       class="w-full px-3 py-2 border border-yellow-300 rounded bg-yellow-50 text-center font-semibold text-yellow-800">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">Sold</label>
                                <input type="number" min="0" max="${item.quantity}" value="${audit.sold}"
                                       onchange="updateAuditCount(${item.toolId}, 'sold', this.value)"
                                       class="w-full px-3 py-2 border border-blue-300 rounded bg-blue-50 text-center font-semibold text-blue-800">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Original Value:</span>
                            <span class="font-semibold text-gray-900">${fmt(audit.originalTotal)}</span>
                        </div>
                        ${audit.good    > 0 ? `<div class="flex justify-between text-green-700"><span>Good (${audit.good}x):</span><span class="font-semibold">${fmt(audit.good * audit.originalRate)}</span></div>` : ''}
                        ${audit.damaged > 0 ? `<div class="flex justify-between text-red-700"><span>Damage Loss (${audit.damaged}x):</span><span class="font-semibold">-${fmt(damageValueLoss)}</span></div>` : ''}
                        ${audit.lost    > 0 ? `<div class="flex justify-between text-red-700"><span>Lost (${audit.lost}x):</span><span class="font-semibold">-${fmt(lostValueLoss)}</span></div>` : ''}
                        ${audit.sold    > 0 ? `<div class="flex justify-between text-blue-700"><span>Sold (${audit.sold}x @ 50%):</span><span class="font-semibold">+${fmt(soldRevenue)}</span></div>` : ''}
                        <div class="flex justify-between pt-2 border-t font-bold text-lg">
                            <span>Recovery Value:</span>
                            <span class="text-blue-600">${fmt(recoveryValue)}</span>
                        </div>
                    </div>
                </div>
            `;
            });

            html += '</div>';
            document.getElementById('returningToolsContent').innerHTML = html;
        }

        function renderDetails(rental) {
            renderOriginal(rental);
            renderAudit(rental);
        }

        // ─── Update satu field audit ──────────────────────────────
        function updateAuditCount(toolId, field, value) {
            auditItems[toolId][field] = parseInt(value) || 0;

            // Jaga agar total tidak melebihi quantity
            const item = auditItems[toolId];
            const total = item.good + item.damaged + item.lost + item.sold;
            if (total > item.quantity) {
                auditItems[toolId][field] = Math.max(0, auditItems[toolId][field] - (total - item.quantity));
            }

            const rental = rentalsData.find(r => r.id === selectedRentalId);
            if (rental) {
                renderAudit(rental);
                updateRevenueSummary(rental);
            }
        }

        // ─── Hitung & tampilkan revenue summary ──────────────────
        function updateRevenueSummary(rental) {
            let totalGood = 0;
            let totalDamageLoss = 0;
            let totalLostLoss = 0;
            let totalSoldRevenue = 0;

            Object.values(auditItems).forEach(item => {
                totalGood += item.good * item.originalRate;
                totalDamageLoss += item.damaged * item.originalRate;
                totalLostLoss += item.lost * item.originalRate;
                totalSoldRevenue += item.sold * (item.originalRate * 0.5);
            });

            const totalLoss = totalDamageLoss + totalLostLoss;
            const netRevenue = totalGood + totalSoldRevenue;

            document.getElementById('originalRevenue').textContent = fmt(rental.totalPrice);
            document.getElementById('totalLoss').textContent = fmt(totalLoss);
            document.getElementById('netRevenue').textContent = fmt(netRevenue);
        }

        // ─── Submit: masukkan auditItems ke hidden input → submit ─
        function submitReturn() {
            if (!selectedRentalId) {
                alert('Please select a rental');
                return;
            }
            if (Object.keys(auditItems).length === 0) {
                alert('No audit data found');
                return;
            }

            document.getElementById('hidden-rentalId').value = selectedRentalId;
            document.getElementById('hidden-auditItems').value = JSON.stringify(Object.values(auditItems));
            document.getElementById('return-form').submit();
        }
    </script>
@endsection
