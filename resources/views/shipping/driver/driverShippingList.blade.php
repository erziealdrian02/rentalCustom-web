@extends('layout.app')

@section('content')
    <script>
        const shippingsData = JSON.parse(atob('{{ base64_encode(json_encode($shippings)) }}'));
        const rentalsData = JSON.parse(atob('{{ base64_encode(json_encode($rentals)) }}'));
        const warehousesData = JSON.parse(atob('{{ base64_encode(json_encode($warehouses)) }}'));
        const movementsData = JSON.parse(atob('{{ base64_encode(json_encode($movements)) }}'));
    </script>

    <div class="max-w-3xl mx-auto">

        {{-- Driver Header --}}
        <div class="bg-blue-600 text-white rounded-xl p-5 mb-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-xl font-bold">
                {{ strtoupper(substr($driver->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-blue-100 text-sm">Selamat datang,</p>
                <h2 class="text-xl font-bold">{{ $driver->name }}</h2>
                <p class="text-blue-100 text-sm">{{ $driver->vehicle_type }} · {{ $driver->license_plate }}</p>
            </div>
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Pengiriman Saya</h3>
            <span class="text-sm text-blue-600 font-semibold bg-blue-50 px-3 py-1 rounded-full">
                {{ $shippings->count() }} aktif
            </span>
        </div>

        @forelse($shippings as $shipping)
            @php
                $rentalIds = is_array($shipping->rental_id)
                    ? $shipping->rental_id
                    : json_decode($shipping->rental_id, true) ?? [];
                $fromLocs = is_array($shipping->from_location)
                    ? $shipping->from_location
                    : json_decode($shipping->from_location, true) ?? [];
                $isDeparted = !is_null($shipping->departure_time);
                $firstRental = $rentals[$rentalIds[0] ?? ''] ?? null;
                $whIds = $fromLocs[0] ?? [];
                $fromName = collect((array) $whIds)->map(fn($wid) => $warehouses[$wid]?->name ?? "WH#$wid")->join(', ');

                // Hitung total tools
                $toolCount = 0;
                foreach ($rentalIds as $rid) {
                    $r = $rentals[$rid] ?? null;
                    if ($r) {
                        $toolCount += count(json_decode($r->movement_id, true) ?? []);
                    }
                }

                $route = $isDeparted
                    ? route('shipping.driver.arrival', $shipping->delivery_number)
                    : route('shipping.driver.departure', $shipping->delivery_number);

                $deliveryStatus = $shipping->delivery_status ?? ($isDeparted ? 'On Track' : 'Pending');

                $statusClass = match ($deliveryStatus) {
                    'Delivered' => 'bg-green-100 text-green-800',
                    'On Track' => 'bg-blue-100 text-blue-800',
                    'Pending' => 'bg-yellow-100 text-yellow-800',
                    default => 'bg-gray-100 text-gray-700',
                };

                $statusLabel = $deliveryStatus;
            @endphp

            <a href="{{ $route }}"
                class="block bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition mb-4 overflow-hidden">

                {{-- Card Header --}}
                <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100">
                    <div>
                        <p class="font-bold text-gray-900">{{ $shipping->delivery_number }}</p>
                        <p class="text-sm text-gray-500">{{ $firstRental?->customer?->name ?? 'N/A' }}</p>
                    </div>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                {{-- Route --}}
                <div class="grid grid-cols-2 gap-4 px-5 py-4 border-b border-gray-100">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Dari</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $fromName ?: '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Ke</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $shipping->to_location }}</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex justify-between items-center px-5 py-3">
                    <div class="flex gap-4 text-sm text-gray-500">
                        <span>🧰 {{ $toolCount }} tools</span>
                        <span>📦 {{ count($rentalIds) }} rental</span>
                    </div>
                    <span class="text-blue-600 font-semibold text-sm flex items-center gap-1">
                        {{ $isDeparted ? 'Konfirmasi Tiba →' : 'Mulai Antar →' }}
                    </span>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
                <svg class="w-14 h-14 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 font-medium">Tidak ada pengiriman aktif.</p>
                <p class="text-gray-400 text-sm mt-1">Semua pengiriman sudah selesai!</p>
            </div>
        @endforelse

    </div>
@endsection
