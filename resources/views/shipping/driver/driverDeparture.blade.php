@extends('layout.app')

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- Back --}}
        <a href="{{ route('shipping.driver', $shipping->driver_id) }}"
            class="inline-flex items-center gap-2 text-blue-600 font-semibold mb-5 hover:text-blue-700 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali
        </a>

        {{-- Header --}}
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl p-5 mb-6">
            <p class="text-yellow-100 text-sm mb-1">Konfirmasi Keberangkatan</p>
            <h2 class="text-2xl font-bold">{{ $shipping->delivery_number }}</h2>
            <p class="text-yellow-100 text-sm mt-1">Tekan tombol di bawah saat Anda siap berangkat</p>
        </div>

        {{-- Rentals & Tools --}}
        @php
            $rentalIds = is_array($shipping->rental_id)
                ? $shipping->rental_id
                : json_decode($shipping->rental_id, true) ?? [];
        @endphp

        <div class="space-y-4 mb-6">
            @foreach ($rentalIds as $idx => $rid)
                @php
                    $rental = $rentals[$rid] ?? null;
                    $whIds = $fromLocation[$idx] ?? [];
                    $fromName = collect((array) $whIds)
                        ->map(fn($wid) => $warehouses[$wid]?->name ?? "WH#$wid")
                        ->join(' & ');
                    $movIds = $rental ? json_decode($rental->movement_id, true) ?? [] : [];
                @endphp

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Rental header --}}
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $rental?->invoice_number ?? $rid }}</p>
                            <p class="text-xs text-gray-500">{{ $rental?->customer?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            <p>{{ \Carbon\Carbon::parse($rental?->rental_start_date)->format('d M') }}
                                – {{ \Carbon\Carbon::parse($rental?->rental_end_date)->format('d M Y') }}</p>
                        </div>
                    </div>

                    {{-- Route --}}
                    <div class="grid grid-cols-2 gap-3 px-4 py-3 border-b bg-yellow-50">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Dari</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $fromName ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Ke</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $shipping->to_location }}</p>
                        </div>
                    </div>

                    {{-- Tools --}}
                    <div class="p-4 space-y-2">
                        @foreach ($movIds as $mid)
                            @php $mov = $movements[$mid] ?? null; @endphp
                            @if ($mov)
                                <div class="flex justify-between items-center bg-blue-50 rounded-lg px-3 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $mov->tool?->name ?? $mid }}</p>
                                        <p class="text-xs text-gray-500">{{ $mov->tool?->code_tools ?? '' }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-blue-600">Qty: {{ $mov->quantity }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Departure Form --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-1">Siap Berangkat?</h3>
            <p class="text-sm text-gray-500 mb-5">
                Waktu keberangkatan akan dicatat otomatis saat tombol ditekan.
            </p>

            <form method="POST" action="{{ route('shipping.driver.departure.update', $shipping->delivery_number) }}">
                @csrf
                @method('PUT')

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-5">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-yellow-800">Waktu Keberangkatan</p>
                            <p class="text-sm text-yellow-700" id="current-time">--:--</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold text-lg rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                    Mulai Antar Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
        // Update waktu real-time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }
        updateTime();
        setInterval(updateTime, 1000);
    </script>
@endsection
