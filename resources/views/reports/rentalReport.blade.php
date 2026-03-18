{{-- resources/views/rental-report/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Kirim data chart ke JavaScript --}}
    <script>
        const monthlyLabels = @json($monthlyLabels);
        const monthlyValues = @json($monthlyValues);
        const toolLabels = @json($toolLabels);
        const toolValues = @json($toolValues);
    </script>

    {{-- Header + Filter --}}
    <form method="GET" action="{{ route('reports.rental') }}" id="filter-form">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Rental Report</h2>
            <div class="flex flex-wrap gap-2">
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    Filter
                </button>
                <a href="{{ route('reports.rental.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
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
            <p class="text-gray-600 text-sm font-medium">Total Rentals</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Active Rentals</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $active }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Completed Rentals</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $completed }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Avg Rental Value</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">${{ number_format($avgValue, 2) }}</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rental Trends</h3>
            <canvas id="rentalTrendsChart" height="300"></canvas>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tool Popularity</h3>
            <canvas id="toolPopularityChart" height="300"></canvas>
        </div>
    </div>

    {{-- Rental Details Table --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Rental Details
            @if ($startDate && $endDate)
                <span class="text-sm font-normal text-gray-500 ml-2">
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} –
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
            @endif
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Invoice</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Customer</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Period</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Price</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filtered as $rental)
                        @php
                            $statusColor = match ($rental['status']) {
                                'Active' => 'bg-green-100 text-green-800',
                                'Completed' => 'bg-blue-100 text-blue-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $start = \Carbon\Carbon::parse(
                                $rental['startDate'] ?? ($rental['rentalStartDate'] ?? null),
                            );
                            $end = \Carbon\Carbon::parse($rental['endDate'] ?? ($rental['rentalEndDate'] ?? null));
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $rental['invoiceNumber'] }}</td>
                            <td class="px-6 py-4">{{ $rental['customerName'] }}</td>
                            <td class="px-6 py-4">{{ $rental['toolName'] ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm">
                                {{ $start->format('d M Y') }} – {{ $end->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 font-semibold">${{ number_format($rental['totalPrice'], 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $rental['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                No rentals found for the selected date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js dari CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const chartDefaults = {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
            },
        };

        // Rental Trends Chart
        new Chart(document.getElementById('rentalTrendsChart'), {
            type: 'bar',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Number of Rentals',
                    data: monthlyValues,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: chartDefaults,
        });

        // Tool Popularity Chart
        new Chart(document.getElementById('toolPopularityChart'), {
            type: 'bar',
            data: {
                labels: toolLabels,
                datasets: [{
                    label: 'Rental Count',
                    data: toolValues,
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }],
            },
            options: chartDefaults,
        });
    </script>
@endsection
