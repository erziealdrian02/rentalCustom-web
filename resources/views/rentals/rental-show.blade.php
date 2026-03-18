{{-- resources/views/rentals/show.blade.php --}}
@extends('layout.app')

@section('content')
    @php
        $statusColor = $rental['status'] === 'Active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800';
        $startDate = \Carbon\Carbon::parse($rental['rentalStartDate'])->format('d M Y');
        $endDate = \Carbon\Carbon::parse($rental['rentalEndDate'])->format('d M Y');
        $createdDate = \Carbon\Carbon::parse($rental['createdDate'] ?? now())->format('d M Y');
        $items = $rental['items'] ?? [];
    @endphp

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('transactions.rentals') }}" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Rentals
        </a>
        {{-- <a href="{{ route('rentals.print', $rental['id']) }}" target="_blank"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2"> --}}
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Details
        </a>
    </div>

    {{-- Invoice Header --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm mb-6">
        <div class="border-b border-gray-200 p-6 flex justify-between items-start">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">{{ $rental['invoiceNumber'] }}</h3>
                <p class="text-gray-600 text-sm mt-1">Created: {{ $createdDate }}</p>
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $statusColor }}">
                {{ $rental['status'] }}
            </span>
        </div>

        <div class="p-6 space-y-6">

            {{-- Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Customer Info --}}
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Customer Information</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $rental['customerName'] }}</p>
                    @if ($customer)
                        <p class="text-sm text-gray-600 mt-2">{{ $customer['email'] }}</p>
                        <p class="text-sm text-gray-600">{{ $customer['phone'] }}</p>
                    @endif
                </div>

                {{-- Rental Period --}}
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase mb-3">Rental Period</h4>
                    <p class="text-sm text-gray-600">From:</p>
                    <p class="text-sm font-medium text-gray-900 mb-2">{{ $startDate }}</p>
                    <p class="text-sm text-gray-600">To:</p>
                    <p class="text-sm font-medium text-gray-900">{{ $endDate }}</p>
                </div>
            </div>

            {{-- Rental Items --}}
            <div>
                <h4 class="text-lg font-semibold text-gray-900 mb-4">Rental Items ({{ count($items) }})</h4>
                @foreach ($items as $item)
                    @php
                        $itemStart = \Carbon\Carbon::parse($item['startDate']);
                        $itemEnd = \Carbon\Carbon::parse($item['endDate']);
                        $days = max($itemStart->diffInDays($itemEnd), 1);
                    @endphp
                    <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded mb-3">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $item['toolName'] }}</h4>
                                <p class="text-sm text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                            </div>
                            <span class="text-lg font-bold text-blue-600">${{ number_format($item['subtotal'], 2) }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-gray-600">Daily Rate:</p>
                                <p class="font-medium">${{ number_format($item['dailyRate'], 2) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Duration:</p>
                                <p class="font-medium">{{ $days }} days</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Total Summary --}}
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border border-blue-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-600 text-sm">Total Amount</p>
                        <p class="text-4xl font-bold text-blue-600 mt-2">${{ number_format($rental['totalPrice'], 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600 text-sm">Total Items</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ count($items) }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
