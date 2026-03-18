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
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Serial Number</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Replacement Value</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Lost Date</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Invoice</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Customer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lostTools as $tool)
                            <tr class="border-b hover:bg-red-100 transition bg-red-50">
                                <td class="px-6 py-4 font-semibold text-red-700">{{ $tool['toolCode'] }}</td>
                                <td class="px-6 py-4">{{ $tool['toolName'] }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $tool['serialNumber'] }}</td>
                                <td class="px-6 py-4 font-semibold text-red-600">
                                    ${{ number_format($tool['replacementValue'], 2) }}</td>
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($tool['lostDate'])->format('d M Y') }}</td>
                                <td class="px-6 py-4">{{ $tool['invoiceNumber'] }}</td>
                                <td class="px-6 py-4">{{ $tool['customerName'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
