@extends('layout.app')

@section('content')
    <script>
        const rentalsData = JSON.parse(atob('{{ base64_encode(json_encode($rentals)) }}'));
        const movementsByRental = JSON.parse(atob('{{ base64_encode(json_encode($movementsByRentalId)) }}'));
    </script>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg mb-6">
            @foreach ($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

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
                        <option value="{{ $rental->id }}">
                            {{ $rental->invoice_number }} - {{ $rental->customer->name }}
                            ({{ \Carbon\Carbon::parse($rental->rental_start_date)->format('d M Y') }}
                            to {{ \Carbon\Carbon::parse($rental->rental_end_date)->format('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Details Section --}}
            <div id="detailsSection" class="hidden">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                    {{-- KIRI: Original --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <span
                                class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            Original Rental Details
                        </h3>
                        <div id="originalToolsContent"></div>
                    </div>

                    {{-- KANAN: Audit --}}
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

                <button type="button" onclick="submitReturn()"
                    class="w-full px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 font-bold text-lg transition shadow-md">
                    Submit Return &amp; Process Audit
                </button>
            </div>
        </div>
    </form>

    <script>
        let selectedRentalId = null;
        let auditItems = {};

        function fmt(amount) {
            return 'Rp. ' + new Intl.NumberFormat('id-ID').format(Math.round(amount));
        }

        function fmtDate(dateStr) {
            if (!dateStr) return '-';
            return new Date(dateStr).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        function selectRental() {
            const rentalId = document.getElementById('rentalSelect').value;
            if (!rentalId) {
                selectedRentalId = null;
                document.getElementById('detailsSection').classList.add('hidden');
                return;
            }

            const rental = rentalsData.find(r => r.id === rentalId);
            const movements = movementsByRental[rentalId] ?? [];

            if (!rental) return;

            selectedRentalId = rentalId;
            document.getElementById('hidden-rentalId').value = rentalId;

            // Hitung durasi
            const start = new Date(rental.rental_start_date);
            const end = new Date(rental.rental_end_date);
            const days = Math.max(1, Math.ceil((end - start) / 86400000));

            // Init auditItems dari movements
            auditItems = {};
            movements.forEach(mov => {
                if (!mov?.tool) return;
                const dailyRate = mov.tool.daily_rate ?? 0;
                const subtotal = dailyRate * mov.quantity * days;

                auditItems[mov.id] = {
                    movementId: mov.id,
                    toolId: mov.tool_id,
                    toolName: mov.tool.name,
                    toolCode: mov.tool.code_tools,
                    quantity: mov.quantity,
                    dailyRate: dailyRate,
                    days: days,
                    originalTotal: subtotal,
                    good: mov.quantity,
                    damaged: 0,
                    lost: 0,
                    sold: 0,
                };
            });

            renderDetails(rental, movements, days);
            updateRevenueSummary(rental);
            document.getElementById('detailsSection').classList.remove('hidden');
        }

        function renderOriginal(rental, movements, days) {
            let html = `
            <div class="mb-4 pb-4 border-b border-blue-200">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Invoice</p>
                        <p class="font-semibold text-gray-900">${rental.invoice_number}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Customer</p>
                        <p class="font-semibold text-gray-900">${rental.customer?.name ?? 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">Start</p>
                        <p class="font-semibold text-gray-900">${fmtDate(rental.rental_start_date)}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold">End</p>
                        <p class="font-semibold text-gray-900">${fmtDate(rental.rental_end_date)}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-semibold">Duration</p>
                        <p class="font-semibold text-gray-900">${days} days</p>
                    </div>
                </div>
            </div>
            <div class="space-y-3">
        `;

            movements.forEach(mov => {
                if (!mov?.tool) return;
                const dailyRate = mov.tool.daily_rate ?? 0;
                const subtotal = dailyRate * mov.quantity * days;

                html += `
                <div class="bg-white border border-blue-200 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="font-semibold text-gray-900">${mov.tool.name}</p>
                            <p class="text-xs text-gray-500">${mov.tool.code_tools ?? ''}</p>
                        </div>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-semibold">
                            Qty: ${mov.quantity}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Daily Rate</p>
                            <p class="font-semibold">${fmt(dailyRate)}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Subtotal (${days}d)</p>
                            <p class="font-bold text-green-700">${fmt(subtotal)}</p>
                        </div>
                    </div>
                </div>
            `;
            });

            html += '</div>';
            document.getElementById('originalToolsContent').innerHTML = html;
        }

        function renderAudit(rental, movements, days) {
            let html = `<div class="space-y-4">`;

            movements.forEach(mov => {
                if (!mov?.tool) return;
                const audit = auditItems[mov.id];
                if (!audit) return;

                const damageValueLoss = audit.damaged * audit.dailyRate * days;
                const lostValueLoss = audit.lost * audit.dailyRate * days;
                const soldRevenue = audit.sold * (audit.dailyRate * days * 0.5);
                const goodRevenue = audit.good * audit.dailyRate * days;
                const recoveryValue = goodRevenue + soldRevenue;

                html += `
                <div class="bg-white border border-gray-300 rounded-lg p-4">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <p class="font-semibold text-gray-900">${mov.tool.name}</p>
                            <p class="text-xs text-gray-500">Total: ${mov.quantity} unit(s)</p>
                        </div>
                    </div>

                    <div class="mb-4 pb-4 border-b">
                        <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Condition Count</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">✅ Good</label>
                                <input type="number" min="0" max="${mov.quantity}" value="${audit.good}"
                                    onchange="updateAuditCount('${mov.id}', 'good', this.value)"
                                    class="w-full px-3 py-2 border border-green-300 rounded bg-green-50 text-center font-semibold text-green-800 focus:outline-none focus:ring-2 focus:ring-green-400">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">⚠️ Damaged</label>
                                <input type="number" min="0" max="${mov.quantity}" value="${audit.damaged}"
                                    onchange="updateAuditCount('${mov.id}', 'damaged', this.value)"
                                    class="w-full px-3 py-2 border border-orange-300 rounded bg-orange-50 text-center font-semibold text-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-400">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">❌ Lost</label>
                                <input type="number" min="0" max="${mov.quantity}" value="${audit.lost}"
                                    onchange="updateAuditCount('${mov.id}', 'lost', this.value)"
                                    class="w-full px-3 py-2 border border-red-300 rounded bg-red-50 text-center font-semibold text-red-800 focus:outline-none focus:ring-2 focus:ring-red-400">
                            </div>
                            <div>
                                <label class="text-xs text-gray-600 block mb-1">💰 Sold</label>
                                <input type="number" min="0" max="${mov.quantity}" value="${audit.sold}"
                                    onchange="updateAuditCount('${mov.id}', 'sold', this.value)"
                                    class="w-full px-3 py-2 border border-blue-300 rounded bg-blue-50 text-center font-semibold text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-2 text-center">
                            Total entered: ${audit.good + audit.damaged + audit.lost + audit.sold} / ${mov.quantity}
                        </p>
                    </div>

                    <div class="space-y-1.5 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Original Value:</span>
                            <span class="font-semibold text-gray-900">${fmt(audit.originalTotal)}</span>
                        </div>
                        ${audit.good > 0 ? `
                            <div class="flex justify-between text-green-700">
                                <span>Good (${audit.good}× ${days}d):</span>
                                <span class="font-semibold">${fmt(goodRevenue)}</span>
                            </div>` : ''}
                        ${audit.damaged > 0 ? `
                            <div class="flex justify-between text-orange-700">
                                <span>Damage Loss (${audit.damaged}×):</span>
                                <span class="font-semibold">-${fmt(damageValueLoss)}</span>
                            </div>` : ''}
                        ${audit.lost > 0 ? `
                            <div class="flex justify-between text-red-700">
                                <span>Lost (${audit.lost}×):</span>
                                <span class="font-semibold">-${fmt(lostValueLoss)}</span>
                            </div>` : ''}
                        ${audit.sold > 0 ? `
                            <div class="flex justify-between text-blue-700">
                                <span>Sold (${audit.sold}× @ 50%):</span>
                                <span class="font-semibold">+${fmt(soldRevenue)}</span>
                            </div>` : ''}
                        <div class="flex justify-between pt-2 border-t font-bold">
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

        function renderDetails(rental, movements, days) {
            renderOriginal(rental, movements, days);
            renderAudit(rental, movements, days);
        }

        function updateAuditCount(movId, field, value) {
            if (!auditItems[movId]) return;

            auditItems[movId][field] = parseInt(value) || 0;

            // Jaga total tidak melebihi quantity
            const item = auditItems[movId];
            const total = item.good + item.damaged + item.lost + item.sold;
            if (total > item.quantity) {
                const excess = total - item.quantity;
                auditItems[movId][field] = Math.max(0, auditItems[movId][field] - excess);
            }

            const rental = rentalsData.find(r => r.id === selectedRentalId);
            const movements = movementsByRental[selectedRentalId] ?? [];
            const days = auditItems[movId]?.days ?? 1;

            if (rental) {
                renderAudit(rental, movements, days);
                updateRevenueSummary(rental);
            }
        }

        function updateRevenueSummary(rental) {
            let totalGood = 0;
            let totalDamageLoss = 0;
            let totalLostLoss = 0;
            let totalSoldRevenue = 0;

            Object.values(auditItems).forEach(item => {
                const d = item.days;
                totalGood += item.good * item.dailyRate * d;
                totalDamageLoss += item.damaged * item.dailyRate * d;
                totalLostLoss += item.lost * item.dailyRate * d;
                totalSoldRevenue += item.sold * (item.dailyRate * d * 0.5);
            });

            const totalLoss = totalDamageLoss + totalLostLoss;
            const netRevenue = totalGood + totalSoldRevenue;

            document.getElementById('originalRevenue').textContent = fmt(rental.total_price);
            document.getElementById('totalLoss').textContent = fmt(totalLoss);
            document.getElementById('netRevenue').textContent = fmt(netRevenue);
        }

        function submitReturn() {
            if (!selectedRentalId) {
                alert('Please select a rental');
                return;
            }
            if (Object.keys(auditItems).length === 0) {
                alert('No audit data found');
                return;
            }

            // Validasi total per movement
            let valid = true;
            Object.values(auditItems).forEach(item => {
                const total = item.good + item.damaged + item.lost + item.sold;
                if (total !== item.quantity) {
                    alert(`Total count for "${item.toolName}" must equal ${item.quantity}. Currently: ${total}`);
                    valid = false;
                }
            });
            if (!valid) return;

            document.getElementById('hidden-rentalId').value = selectedRentalId;
            document.getElementById('hidden-auditItems').value = JSON.stringify(Object.values(auditItems));
            document.getElementById('return-form').submit();
        }
    </script>
@endsection
