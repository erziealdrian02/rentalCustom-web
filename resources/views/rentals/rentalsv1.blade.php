{{-- resources/views/rentals/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h2 class="text-2xl font-semibold text-gray-900">Rental List</h2>
        <a href="{{ route('transactions.rentals.form') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create Rental
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Rentals</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Active Rentals</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $activeRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Completed</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $completedRentals }}</p>
        </div>
        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
            <p class="text-gray-600 text-sm font-medium">Total Revenue</p>
            <p class="text-3xl font-bold text-purple-600 mt-2">${{ number_format($totalRevenue, 2) }}</p>
        </div>
    </div>

    {{-- Tabel Rentals --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Invoice</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Tools</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Rental Period</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Total Price</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($rentals as $rental)
                        @php
                            $statusColor =
                                $rental['status'] === 'Active'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-blue-100 text-blue-800';
                            $toolsCount = count($rental['items'] ?? []);
                            $startDate = \Carbon\Carbon::parse($rental['rentalStartDate'])->format('d M Y');
                            $endDate = \Carbon\Carbon::parse($rental['rentalEndDate'])->format('d M Y');
                        @endphp
                        <tr class="hover:bg-gray-50 transition cursor-pointer"
                            onclick="window.location='{{ route('transactions.rentals.show', $rental['id']) }}'">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $rental['invoiceNumber'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $rental['customerName'] }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                    {{ $toolsCount }} tool{{ $toolsCount > 1 ? 's' : '' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $startDate }} - {{ $endDate }}</td>
                            <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                ${{ number_format($rental['totalPrice'], 2) }}</td>
                            <td class="px-4 py-3 text-sm">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ $rental['status'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <a href="{{ route('transactions.rentals.show', $rental['id']) }}" onclick="event.stopPropagation()"
                                    class="text-blue-600 hover:text-blue-700 font-medium">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No rentals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
