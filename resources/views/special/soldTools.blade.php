{{-- resources/views/sold-tools/index.blade.php --}}
@extends('layout.app')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Sold Tools Inventory</h2>
        <p class="text-sm text-gray-600 mt-2">Tools that have been sold off from inventory</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Sold</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalSold }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Revenue from Sales</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">${{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        @if (count($soldTools) === 0)
            <div class="p-8 text-center">
                <p class="text-gray-600">No sold tools</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Code</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Serial Number</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Original Value</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Sold Price</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Sold Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($soldTools as $soldTool)
                            @php
                                $profit = $soldTool->selling_price - $soldTool->tool->replacement_value;
                                $profitPercent =
                                    $soldTool->tool->replacement_value > 0
                                        ? number_format(($profit / $soldTool->tool->replacement_value) * 100, 0)
                                        : 0;
                                $profitColor = $profit >= 0 ? 'text-green-600' : 'text-red-600';
                                $profitPrefix = $profit >= 0 ? '+' : '';
                                $soldDate = \Carbon\Carbon::parse($soldTool->created_at)->format('d M Y');
                            @endphp
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-semibold">{{ $soldTool->tool->code_tools }}</td>
                                <td class="px-6 py-4">{{ $soldTool->tool->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $soldTool->tool->serial_number }}</td>
                                <td class="px-6 py-4">Rp. {{ number_format($soldTool->tool->replacement_value, 2) }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-semibold">Rp. {{ number_format($soldTool->tool->replacement_value, 2) }}</p>
                                    <p class="text-xs {{ $profitColor }}">
                                        {{ $profitPrefix }}{{ number_format($profit, 2) }} ({{ $profitPercent }}%)
                                    </p>
                                </td>
                                <td class="px-6 py-4">{{ $soldDate }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
