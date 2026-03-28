{{-- resources/views/stock-movement/index.blade.php --}}
@extends('layout.app')

@section('content')
    @php
        $typeColors = [
            'IN' => 'bg-green-100 text-green-800',
            'OUT' => 'bg-blue-100 text-blue-800',
            'RENT' => 'bg-yellow-100 text-yellow-800',
            'RETURN' => 'bg-purple-100 text-purple-800',
            'LOST' => 'bg-red-100 text-red-800',
            'DAMAGED' => 'bg-orange-100 text-orange-800',
            'Waiting' => 'bg-gray-100 text-gray-800',
            'SHIPPING' => 'bg-blue-100 text-blue-800',
            'Arrived' => 'bg-green-100 text-green-800',
        ];

        $typeIcons = [
            'IN' => '📥',
            'OUT' => '📤',
            'RENT' => '📦',
            'RETURN' => '↩️',
            'LOST' => '❌',
            'DAMAGED' => '⚠️',
            'Waiting' => '⏳',
            'SHIPPING' => '🚚',
            'Arrived' => '✅',
        ];

        // Tipe yang mengurangi stok (ditampilkan merah dengan tanda minus)
        $outTypes = ['OUT', 'RENT', 'LOST', 'DAMAGED'];
    @endphp

    {{-- Header + Filter --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Stock Movement History</h2>

        {{-- Filter dropdown — submit otomatis saat berubah --}}
        <form method="GET" action="{{ route('stock.movement') }}" id="filter-form">
            <select name="type" onchange="document.getElementById('filter-form').submit()"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Movements</option>
                @foreach ($types as $type)
                    <option value="{{ $type }}" {{ $typeFilter === $type ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Warehouse</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Type</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Reference</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filtered as $movement)
                        @php
                            $typeColor = $typeColors[$movement->stock_type] ?? 'bg-gray-100 text-gray-800';
                            $typeMovementColor = $typeColors[$movement->movement_type] ?? 'bg-gray-100 text-gray-800';
                            $icon = $typeIcons[$movement->stock_type] ?? '•';
                            $movementIcon = $typeIcons[$movement->movement_type] ?? '•';
                            $isOut = in_array($movement->stock_type, $outTypes);
                            $qtyColor = $isOut ? 'text-red-600' : 'text-green-600';
                            $qtyPrefix = $isOut ? '-' : '+';
                            $formattedDateCreated = \Carbon\Carbon::parse($movement->created_at)->format('d M Y');
                            $formattedDateLastUpdated = \Carbon\Carbon::parse($movement->updated_at)->format('d M Y');
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $formattedDateCreated }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $movement->tool->name ?? 'Unknown Tool' }}</td>
                            <td class="px-6 py-4">{{ $movement->warehouse->name ?? 'Unknown Warehouse' }}</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded inline-flex items-center gap-1 {{ $typeMovementColor }}">
                                    <span>{{ $movementIcon }}</span>
                                    <span>{{ $movement->movement_type }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 text-xs font-semibold rounded inline-flex items-center gap-1 {{ $typeColor }}">
                                    <span>{{ $icon }}</span>
                                    <span>{{ $movement->stock_type }}</span>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold {{ $qtyColor }}">
                                    {{ $qtyPrefix }}{{ $movement->quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $movement->reference_id }}</td>
                            <td class="px-6 py-4">{{ $formattedDateLastUpdated }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No movement data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div
            class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 border-t border-gray-200 bg-white">

            {{-- Kiri: Per Page Selector + Info --}}
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <form method="GET" action="{{ route('stock.movement') }}" id="per-page-form">
                    <select name="per_page" onchange="document.getElementById('per-page-form').submit()"
                        class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        @foreach ([10, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <span>entries</span>
                <span class="text-gray-400">
                    &mdash; Showing {{ $filtered->firstItem() }}-{{ $filtered->lastItem() }} of
                    {{ $filtered->total() }}
                </span>
            </div>

            {{-- Kanan: Pagination custom (tanpa teks "Showing X to Y") --}}
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if ($filtered->onFirstPage())
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $filtered->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">‹</a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($filtered->getUrlRange(1, $filtered->lastPage()) as $page => $url)
                    @if ($page == $filtered->currentPage())
                        <span
                            class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&per_page={{ request('per_page', 10) }}"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($filtered->hasMorePages())
                    <a href="{{ $filtered->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">›</a>
                @else
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">›</span>
                @endif
            </div>

        </div>
    </div>
@endsection
