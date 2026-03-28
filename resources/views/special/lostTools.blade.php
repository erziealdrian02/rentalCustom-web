{{-- resources/views/lost-tools/index.blade.php --}}
@extends('layout.app')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Lost Tools Report</h2>
        <p class="text-sm text-gray-600 mt-2">Tools marked as lost during rental period</p>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        @if (count($lostTools) === 0)
            <div class="p-8 text-center">
                <p class="text-gray-600">No lost tools recorded</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Code</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Quantity</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Serial Number</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Replacement Value</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Lost Date</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Invoice</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lostTools as $lostTool)
                            <tr class="border-b hover:bg-red-100 transition bg-red-50">
                                <td class="px-6 py-4 font-semibold text-red-700">{{ $lostTool->tool->code_tools }}</td>
                                <td class="px-6 py-4 font-semibold text-red-700">{{ $lostTool->quantity }}</td>
                                <td class="px-6 py-4">{{ $lostTool->tool->name }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $lostTool->tool->serial_number }}</td>
                                <td class="px-6 py-4 font-semibold text-red-600">
                                    ${{ number_format($lostTool->tool->replacement_value, 2) }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($lostTool->created_at)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4">{{ $lostTool->rent->invoice_number ?? 'N/A' }}</td>
                                <td class="px-6 py-4">{{ $lostTool->rent->customer->name ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
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
                    &mdash; Showing {{ $lostTools->firstItem() }}-{{ $lostTools->lastItem() }} of
                    {{ $lostTools->total() }}
                </span>
            </div>

            {{-- Kanan: Pagination custom (tanpa teks "Showing X to Y") --}}
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if ($lostTools->onFirstPage())
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $lostTools->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">‹</a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($lostTools->getUrlRange(1, $lostTools->lastPage()) as $page => $url)
                    @if ($page == $lostTools->currentPage())
                        <span
                            class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&per_page={{ request('per_page', 10) }}"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($lostTools->hasMorePages())
                    <a href="{{ $lostTools->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">›</a>
                @else
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">›</span>
                @endif
            </div>

        </div>
    </div>

@endsection
