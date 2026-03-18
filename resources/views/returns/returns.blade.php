{{-- resources/views/returns/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tool Returns</h2>
        <a href="{{ route('returns.form') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            New Return
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Returns</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Completed</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $completed }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $pending }}</p>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Return ID</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Invoice</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Customer</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Return Date</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Condition</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $ret)
                        @php
                            $conditionColor = match ($ret['condition']) {
                                'Good' => 'bg-green-100 text-green-800',
                                'Damaged' => 'bg-red-100 text-red-800',
                                'Lost' => 'bg-gray-100 text-gray-800',
                                default => 'bg-yellow-100 text-yellow-800',
                            };
                            $statusColor = match ($ret['status']) {
                                'Completed' => 'bg-green-100 text-green-800',
                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $formattedDate = \Carbon\Carbon::parse($ret['returnDate'])->format('d M Y');
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $ret['returnId'] }}</td>
                            <td class="px-6 py-4">{{ $ret['invoiceNumber'] }}</td>
                            <td class="px-6 py-4">{{ $ret['toolName'] }}</td>
                            <td class="px-6 py-4">{{ $ret['customerName'] }}</td>
                            <td class="px-6 py-4">{{ $formattedDate }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $conditionColor }}">
                                    {{ $ret['condition'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $ret['status'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No returns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
