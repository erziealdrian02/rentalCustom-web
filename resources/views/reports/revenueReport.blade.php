{{-- resources/views/revenue-report/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Kirim data chart ke JavaScript --}}
    <script>
        const monthlyLabels = @json($monthlyLabels);
        const monthlyValues = @json($monthlyValues);
        const toolLabels = @json($toolLabels);
        const toolValues = @json($toolValues);
        const statusLabels = @json($statusLabels);
        const statusValues = @json($statusValues);
    </script>

    {{-- Header + Filter --}}
    <form method="GET" action="{{ route('reports.revenue') }}">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Revenue Report</h2>
            <div class="flex flex-wrap gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Filter
                </button>
                <a href="{{ route('reports.revenue.export') }}"
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
            <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
            <p class="text-3xl font-bold text-green-600 mt-2">${{ number_format($totalRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Average Revenue/Rental</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">${{ number_format($avgRevenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Transactions</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">{{ $totalCount }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Peak Month</p>
            <p class="text-3xl font-bold text-orange-600 mt-2">{{ $peakMonth }}</p>
        </div>
    </div>

    {{-- Row 1: Line Chart + Bar Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue Trend</h3>
            <canvas id="revenueChart" height="300"></canvas>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Tool</h3>
            <canvas id="toolRevenueChart" height="300"></canvas>
        </div>
    </div>

    {{-- Row 2: Customer Revenue List + Doughnut Chart --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

        {{-- Revenue by Customer --}}
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue by Customer</h3>
            <div class="space-y-3">
                @forelse($customerRevenue as $customer => $revenue)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-semibold text-gray-900">{{ $customer }}</span>
                        <span class="text-green-600 font-bold">${{ number_format($revenue, 2) }}</span>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">No data available.</p>
                @endforelse
            </div>
        </div>

        {{-- Status Breakdown Doughnut --}}
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Breakdown</h3>
            <canvas id="statusChart" height="300"></canvas>
        </div>

    </div>

    {{-- Chart.js CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const baseOptions = {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
        };

        // ── Monthly Revenue Trend (Line) ──────────────────────────
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: monthlyValues,
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    tension: 0.4,
                    fill: true,
                }],
            },
            options: {
                ...baseOptions,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            },
        });

        // ── Revenue by Tool (Bar) ─────────────────────────────────
        new Chart(document.getElementById('toolRevenueChart'), {
            type: 'bar',
            data: {
                labels: toolLabels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: toolValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: {
                ...baseOptions,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
            },
        });

        // ── Status Breakdown (Doughnut) ───────────────────────────
        new Chart(document.getElementById('statusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusValues,
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(234, 179, 8, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(168, 85, 247, 0.7)',
                    ],
                    borderWidth: 2,
                }],
            },
            options: {
                ...baseOptions,
                cutout: '60%',
            },
        });
    </script>
@endsection
