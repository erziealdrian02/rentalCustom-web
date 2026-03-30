@extends('layout.app')

@php
    $statusColors = [
        'Active' => 'bg-blue-100 text-blue-800',
        'Completed' => 'bg-green-100 text-green-800',
        'Pending' => 'bg-yellow-100 text-yellow-800',
        'Overdue' => 'bg-red-100 text-red-800',
        'Cancelled' => 'bg-gray-100 text-gray-800',
        'Good' => 'bg-green-100 text-green-800',
        'Damaged' => 'bg-red-100 text-red-800',
        'Lost' => 'bg-red-200 text-red-900',
        'Maintenance' => 'bg-orange-100 text-orange-800',
        'Available' => 'bg-green-100 text-green-800',
        'Rented' => 'bg-blue-100 text-blue-800',
    ];

    $cards = [
        ['title' => 'Total Tools', 'value' => $stats['totalTools'], 'icon' => '🔧'],
        ['title' => 'Tools Available', 'value' => $stats['toolsAvailable'], 'icon' => '✅'],
        ['title' => 'Tools Rented', 'value' => $stats['toolsRented'], 'icon' => '📦'],
        ['title' => 'Active Rentals', 'value' => $stats['activeRentals'], 'icon' => '📋'],
        [
            'title' => 'Monthly Revenue',
            'value' => 'Rp ' . number_format($stats['monthlyRevenue'], 0, ',', '.'),
            'icon' => '💰',
        ],
    ];
@endphp

@section('content')
    <main class="flex-1 p-8">

        {{-- ===================== STATS CARDS ===================== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            @foreach ($cards as $card)
                <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-gray-600 text-sm font-medium">{{ $card['title'] }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $card['value'] }}</p>
                        </div>
                        <div class="text-2xl">{{ $card['icon'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===================== CHARTS ROW 1 ===================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue</h3>
                <div style="height:260px"><canvas id="revenueChart"></canvas></div>
            </div>
            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Activity</h3>
                <div style="height:260px"><canvas id="rentalChart"></canvas></div>
            </div>
        </div>

        {{-- ===================== CHARTS ROW 2 + RECENT RENTALS ===================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Tool Status</h3>
                <div style="height:260px"><canvas id="toolStatusChart"></canvas></div>
            </div>

            <div class="lg:col-span-2 bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Rentals</h3>
                <div class="space-y-3">
                    @foreach (array_slice($rentals, 0, 5) as $rental)
                        @php
                            $toolNames = implode(', ', array_column($rental['items'], 'toolName'));
                            $badge = $statusColors[$rental['rentalStatus']] ?? 'bg-gray-100 text-gray-800';
                            $start = \Carbon\Carbon::parse($rental['rentalStartDate'])->translatedFormat('d M Y');
                            $end = \Carbon\Carbon::parse($rental['rentalEndDate'])->translatedFormat('d M Y');
                        @endphp
                        <button onclick="openRentalModal({{ $rental['id'] }})"
                            class="w-full text-left flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $rental['invoiceNumber'] }}</p>
                                <p class="text-sm text-gray-600">{{ $toolNames }} — {{ $rental['customerName'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $start }} s/d {{ $end }}</p>
                            </div>
                            <div class="text-right ml-4">
                                <p class="font-semibold text-gray-900">Rp
                                    {{ number_format($rental['totalPrice'], 0, ',', '.') }}</p>
                                <span
                                    class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded {{ $badge }}">
                                    {{ $rental['rentalStatus'] }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===================== RECENT RETURNS ===================== --}}
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Returns</h3>
            <div class="space-y-3">
                @foreach (array_slice($returns, 0, 5) as $ret)
                    @php
                        $toolNames = implode(', ', array_column($ret['items'], 'toolName'));
                        $badge = $statusColors[$ret['status']] ?? 'bg-gray-100 text-gray-800';
                        $displayDate = $ret['actualReturnDate'] ?? $ret['requestedReturnDate'];
                        $dateStr = \Carbon\Carbon::parse($displayDate)->translatedFormat('d M Y');
                    @endphp
                    <button onclick="openReturnModal({{ $ret['id'] }})"
                        class="w-full text-left flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition">
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">{{ $ret['returnId'] }}</p>
                            <p class="text-sm text-gray-600">{{ $toolNames }} — {{ $ret['customerName'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">Return: {{ $dateStr }}</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="font-semibold text-gray-900">Rp
                                {{ number_format($ret['originalRevenue'], 0, ',', '.') }}</p>
                            <span class="inline-block mt-1 px-2 py-1 text-xs font-semibold rounded {{ $badge }}">
                                {{ $ret['status'] }}
                            </span>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

    </main>


    {{-- ===================== MODAL: RENTAL DETAIL ===================== --}}
    <div id="rentalModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50"
        onclick="if(event.target===this) closeRentalModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-xl font-bold text-gray-900">Rental Detail</h2>
                <button onclick="closeRentalModal()"
                    class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
            <div class="overflow-y-auto p-6 flex-1">
                @foreach ($rentals as $rental)
                    @php $badge = $statusColors[$rental['rentalStatus']] ?? 'bg-gray-100 text-gray-800'; @endphp
                    <div id="rental-detail-{{ $rental['id'] }}" class="rental-detail hidden space-y-6">

                        <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                            <div>
                                <p class="text-gray-600 text-sm">Invoice Number</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $rental['invoiceNumber'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Created Date</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($rental['createdDate'])->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Customer</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $rental['customerName'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Status</p>
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">
                                    {{ $rental['rentalStatus'] }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Rental Period</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Start Date</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($rental['rentalStartDate'])->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">End Date</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($rental['rentalEndDate'])->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if ($rental['driverName'])
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-3">Delivery Information</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-600 text-sm">Driver</p>
                                        <p class="font-semibold text-gray-900">{{ $rental['driverName'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 text-sm">Delivery Location</p>
                                        <p class="font-semibold text-gray-900">{{ $rental['deliveryLocation'] }}</p>
                                    </div>
                                    @if ($rental['estimatedDeliveryTime'])
                                        <div class="col-span-2">
                                            <p class="text-gray-600 text-sm">Estimated Delivery Time</p>
                                            <p class="font-semibold text-gray-900">{{ $rental['estimatedDeliveryTime'] }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-gray-900">Delivery not yet assigned</p>
                            </div>
                        @endif

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Rented Items</h4>
                            <div class="border rounded-lg overflow-hidden divide-y">
                                @foreach ($rental['items'] as $i => $item)
                                    <div
                                        class="flex items-center justify-between p-4 {{ $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' }}">
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $item['toolName'] }}</p>
                                            <p class="text-sm text-gray-600">
                                                Qty: {{ $item['quantity'] }} × Rp
                                                {{ number_format($item['dailyRate'], 0, ',', '.') }}/day
                                            </p>
                                        </div>
                                        <p class="font-semibold text-gray-900">
                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t-2">
                            <p class="text-lg font-bold text-gray-900">Total:</p>
                            <p class="text-2xl font-bold text-blue-600">
                                Rp {{ number_format($rental['totalPrice'], 0, ',', '.') }}
                            </p>
                        </div>

                    </div>
                @endforeach
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <button onclick="window.print()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    🖨 Print
                </button>
                <button onclick="closeRentalModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>


    {{-- ===================== MODAL: RETURN DETAIL ===================== --}}
    <div id="returnModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50"
        onclick="if(event.target===this) closeReturnModal()">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h2 class="text-xl font-bold text-gray-900">Return Detail</h2>
                <button onclick="closeReturnModal()"
                    class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
            </div>
            <div class="overflow-y-auto p-6 flex-1">
                @foreach ($returns as $ret)
                    @php $badge = $statusColors[$ret['status']] ?? 'bg-gray-100 text-gray-800'; @endphp
                    <div id="return-detail-{{ $ret['id'] }}" class="return-detail hidden space-y-6">

                        <div class="grid grid-cols-2 gap-4 pb-4 border-b">
                            <div>
                                <p class="text-gray-600 text-sm">Return ID</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $ret['returnId'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Invoice Number</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $ret['invoiceNumber'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Customer</p>
                                <p class="text-lg font-semibold text-gray-900">{{ $ret['customerName'] }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm">Status</p>
                                <span
                                    class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $badge }}">
                                    {{ $ret['status'] }}
                                </span>
                            </div>
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">Return Timeline</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <p class="text-gray-600 text-sm">Rental Date</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($ret['originalRentalDate'])->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Expected Return</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($ret['requestedReturnDate'])->translatedFormat('d M Y') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 text-sm">Actual Return</p>
                                    <p class="font-semibold text-gray-900">
                                        {{ $ret['actualReturnDate']
                                            ? \Carbon\Carbon::parse($ret['actualReturnDate'])->translatedFormat('d M Y')
                                            : 'Pending' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-900 mb-3">Returned Items</h4>
                            <div class="space-y-3">
                                @foreach ($ret['items'] as $item)
                                    @php $condBadge = $statusColors[$item['condition']] ?? 'bg-gray-100 text-gray-800'; @endphp
                                    <div class="border rounded-lg p-4 bg-gray-50">
                                        <div class="flex items-center justify-between mb-3">
                                            <div>
                                                <p class="font-semibold text-gray-900">{{ $item['toolName'] }}</p>
                                                <p class="text-sm text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                                            </div>
                                            <span
                                                class="px-3 py-1 rounded-full text-sm font-semibold {{ $condBadge }}">
                                                {{ $item['condition'] }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-4 gap-2 text-sm">
                                            @foreach (['good' => 'Good', 'damaged' => 'Damaged', 'lost' => 'Lost', 'sold' => 'Sold'] as $key => $label)
                                                <div class="bg-white p-2 rounded border text-center">
                                                    <p class="text-gray-600">{{ $label }}</p>
                                                    <p class="font-semibold">{{ $item['auditDetails'][$key] }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                <p class="text-gray-600 text-sm">Original Rental Revenue</p>
                                <p class="text-2xl font-bold text-green-700">
                                    Rp {{ number_format($ret['originalRevenue'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <p class="text-gray-600 text-sm">Revenue Loss (Damage/Lost)</p>
                                <p class="text-2xl font-bold text-red-700">
                                    Rp {{ number_format($ret['totalAuditRevenueLoss'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t">
                <button onclick="window.print()"
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                    🖨 Print
                </button>
                <button onclick="closeReturnModal()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    Close
                </button>
            </div>
        </div>
    </div>


    {{-- ===================== SCRIPTS ===================== --}}
    <script>
        const chartData = {
            monthlyRevenue: @json($monthlyRevenue),
            monthlyRentals: @json($monthlyRentals),
            toolStatusDistribution: @json($toolStatusDistribution),
        };
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // ── Modal helpers ────────────────────────────────────────
        function openRentalModal(id) {
            document.querySelectorAll('.rental-detail').forEach(el => el.classList.add('hidden'));
            document.getElementById('rental-detail-' + id)?.classList.remove('hidden');
            const m = document.getElementById('rentalModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeRentalModal() {
            const m = document.getElementById('rentalModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        function openReturnModal(id) {
            document.querySelectorAll('.return-detail').forEach(el => el.classList.add('hidden'));
            document.getElementById('return-detail-' + id)?.classList.remove('hidden');
            const m = document.getElementById('returnModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }

        function closeReturnModal() {
            const m = document.getElementById('returnModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }

        // ── Charts ───────────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            const {
                monthlyRevenue,
                monthlyRentals,
                toolStatusDistribution
            } = chartData;

            const scaleOpts = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    },
                },
            };

            new Chart(document.getElementById('revenueChart'), {
                type: 'line',
                data: {
                    labels: monthlyRevenue.map(d => d.month),
                    datasets: [{
                        data: monthlyRevenue.map(d => d.revenue),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 5,
                    }],
                },
                options: scaleOpts,
            });

            new Chart(document.getElementById('rentalChart'), {
                type: 'bar',
                data: {
                    labels: monthlyRentals.map(d => d.month),
                    datasets: [{
                        data: monthlyRentals.map(d => d.rentals),
                        backgroundColor: 'rgba(59,130,246,0.7)',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        borderRadius: 4,
                    }],
                },
                options: scaleOpts,
            });

            new Chart(document.getElementById('toolStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: toolStatusDistribution.map(d => d.label),
                    datasets: [{
                        data: toolStatusDistribution.map(d => d.value),
                        backgroundColor: toolStatusDistribution.map(d => d.color),
                        borderWidth: 2,
                        borderColor: '#fff',
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 16,
                                boxWidth: 12
                            }
                        },
                    },
                },
            });
        });
    </script>
@endsection
