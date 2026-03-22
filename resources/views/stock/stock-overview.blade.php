{{-- resources/views/stock/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Header + Filter --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Warehouse Stock Overview</h2>

        <a href="{{ route('stock.restock.form') }}"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
            Restock Tools
        </a>

        {{-- Filter dropdown — submit otomatis saat berubah --}}
        <form method="GET" action="{{ route('stock.overview') }}" id="filter-form">
            <select name="warehouse" onchange="document.getElementById('filter-form').submit()"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Warehouses</option>
                @foreach ($warehouses as $wh)
                    <option value="{{ $wh['name'] }}" {{ $warehouseFilter === $wh['name'] ? 'selected' : '' }}>
                        {{ $wh['name'] }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Items in Stock</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalItems }}</p>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Tools</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTools }}</p>
        </div>

        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Low Stock Items</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $lowStockCount }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Warehouse</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Utilization</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filteredStock as $stock)
                        @php
                            $isLowStock = $stock['quantity'] < 3;
                            $statusColor =
                                $stock['status'] === 'Good' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                            $utilization = min($stock['quantity'] * 10, 100);
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $stock['toolName'] }}</td>
                            <td class="px-6 py-4">{{ $stock['warehouseName'] }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold">{{ $stock['quantity'] }}</span>
                                    @if ($isLowStock)
                                        <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">
                                            Low Stock
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $stock['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $utilization }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No stock data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
