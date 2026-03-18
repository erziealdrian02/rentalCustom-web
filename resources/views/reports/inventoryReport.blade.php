{{-- resources/views/inventory-report/index.blade.php --}}
@extends('layout.app')

@section('content')
    <script>
        const statusLabels = @json(array_keys($statusCount));
        const statusValues = @json(array_values($statusCount));
        const statusColors = @json($statusColors);
        const categoryLabels = @json(array_keys($categoryCount));
        const categoryValues = @json(array_values($categoryCount));
    </script>

    {{-- Header + Filter --}}
    <form method="GET" action="{{ route('reports.inventory') }}">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Inventory Report</h2>
            <div class="flex flex-wrap gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Filter
                </button>
                <a href="{{ route('reports.inventory.export') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export CSV
                </a>
            </div>
        </div>
    </form>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Tools</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTools }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Value</p>
            <p class="text-3xl font-bold text-green-600 mt-2">${{ number_format($totalValue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $lowStockCount }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Warehouses</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $totalWarehouses }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tool Status Distribution</h3>
            <canvas id="toolStatusChart" height="300"></canvas>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tools by Category</h3>
            <canvas id="toolCategoryChart" height="300"></canvas>
        </div>
    </div>

    {{-- Tool Inventory Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Tool Inventory Details</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Code</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tools as $tool)
                        @php
                            $statusColor = match ($tool['status']) {
                                'Available' => 'bg-green-100 text-green-800',
                                'Rented' => 'bg-blue-100 text-blue-800',
                                'Damaged' => 'bg-yellow-100 text-yellow-800',
                                'Lost' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $tool['code'] }}</td>
                            <td class="px-6 py-4">{{ $tool['name'] }}</td>
                            <td class="px-6 py-4">{{ $tool['category'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $tool['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">${{ number_format($tool['replacementValue'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No tools found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Warehouse Utilization --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Warehouse Utilization</h3>
        <div class="space-y-4">
            @forelse($warehouses as $wh)
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-semibold text-gray-900">{{ $wh['name'] }}</h4>
                        <span class="text-sm text-gray-600">{{ $wh['currentStock'] }} / {{ $wh['capacity'] }} items</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="{{ $wh['utilizationColor'] }} h-3 rounded-full transition"
                            style="width: {{ $wh['utilization'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">
                        {{ $wh['location'] }} — Utilization: {{ $wh['utilization'] }}%
                    </p>
                </div>
            @empty
                <p class="text-gray-400 text-sm">No warehouses found.</p>
            @endforelse
        </div>
    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        // ── Tool Status Distribution (Doughnut) ───────────────────
        new Chart(document.getElementById('toolStatusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: statusColors,
                    borderWidth: 2,
                }],
            },
            options: {
                responsive: true,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
            },
        });

        // ── Tools by Category (Bar) ───────────────────────────────
        new Chart(document.getElementById('toolCategoryChart'), {
            type: 'bar',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Number of Tools',
                    data: categoryValues,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
            },
        });
    </script>
@endsection
