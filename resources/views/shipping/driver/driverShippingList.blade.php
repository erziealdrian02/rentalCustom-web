@extends('layout.app')

@section('content')
    <div class="max-w-3xl mx-auto">

        {{-- Driver Header --}}
        <div class="bg-blue-600 text-white rounded-xl p-5 mb-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                {{ strtoupper(substr($driver->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-blue-100 text-sm">Selamat datang,</p>
                <h2 class="text-xl font-bold">{{ $driver->name }}</h2>
                <p class="text-blue-100 text-sm">{{ $driver->vehicle_type }} · {{ $driver->license_plate }}</p>
            </div>
        </div>

        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Tab Navigation --}}
        <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
            <button onclick="switchTab('active')" id="tab-active"
                class="tab-btn flex-1 py-2.5 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2 bg-white text-blue-600 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
                Pengiriman Aktif
                @if ($activeShippings->count() > 0)
                    <span class="bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                        {{ $activeShippings->count() }}
                    </span>
                @endif
            </button>
            <button onclick="switchTab('history')" id="tab-history"
                class="tab-btn flex-1 py-2.5 rounded-lg text-sm font-semibold transition flex items-center justify-center gap-2 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Riwayat
                @if ($historyShippings->count() > 0)
                    <span class="bg-gray-400 text-white text-xs px-2 py-0.5 rounded-full">
                        {{ $historyShippings->count() }}
                    </span>
                @endif
            </button>
        </div>

        {{-- ===== ACTIVE TAB ===== --}}
        <div id="panel-active">
            @forelse($activeShippings as $shipping)
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
                    $fromName = collect((array) $whIds)
                        ->map(fn($wid) => $warehouses[$wid]?->name ?? "WH#$wid")
                        ->join(', ');
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
                @endphp

                <a href="{{ $route }}"
                    class="block bg-white rounded-xl border-2 {{ $isDeparted ? 'border-blue-200' : 'border-yellow-200' }} shadow-sm hover:shadow-md transition mb-4 overflow-hidden">

                    {{-- Progress bar --}}
                    <div class="h-1 {{ $isDeparted ? 'bg-blue-500' : 'bg-yellow-400' }}"></div>

                    <div class="p-5">
                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="font-bold text-gray-900">{{ $shipping->delivery_number }}</p>
                                <p class="text-sm text-gray-500">{{ $firstRental?->customer?->name ?? 'N/A' }}</p>
                            </div>
                            <span
                                class="text-xs font-semibold px-3 py-1 rounded-full {{ $isDeparted ? 'bg-blue-100 text-blue-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $isDeparted ? '🚚 In Transit' : '⏳ Pending' }}
                            </span>
                        </div>

                        {{-- Route --}}
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex-1 bg-gray-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500 mb-0.5">Dari</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $fromName ?: '-' }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                            <div class="flex-1 bg-gray-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500 mb-0.5">Ke</p>
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ $shipping->to_location }}</p>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <div class="flex gap-3 text-xs text-gray-500">
                                <span>🧰 {{ $toolCount }} tools</span>
                                <span>📦 {{ count($rentalIds) }} rental</span>
                                @if ($isDeparted)
                                    <span>🕐 {{ \Carbon\Carbon::parse($shipping->departure_time)->format('H:i') }}</span>
                                @endif
                            </div>
                            <span
                                class="text-sm font-bold {{ $isDeparted ? 'text-blue-600' : 'text-yellow-600' }} flex items-center gap-1">
                                {{ $isDeparted ? 'Konfirmasi Tiba' : 'Mulai Antar' }}
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
                    <svg class="w-14 h-14 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-600 font-semibold">Tidak ada pengiriman aktif</p>
                    <p class="text-gray-400 text-sm mt-1">Semua pengiriman sudah selesai!</p>
                </div>
            @endforelse
        </div>

        {{-- ===== HISTORY TAB ===== --}}
        <div id="panel-history" class="hidden">
            @forelse($historyShippings as $shipping)
                @php
                    $rentalIds = is_array($shipping->rental_id)
                        ? $shipping->rental_id
                        : json_decode($shipping->rental_id, true) ?? [];
                    $firstRental = $rentals[$rentalIds[0] ?? ''] ?? null;
                    $toolCount = 0;
                    foreach ($rentalIds as $rid) {
                        $r = $rentals[$rid] ?? null;
                        if ($r) {
                            $toolCount += count(json_decode($r->movement_id, true) ?? []);
                        }
                    }
                    // dd($shipping->delivery_status);
                    $statusMap = [
                        'Delivered' => [
                            'label' => '✓ Delivered',
                            'class' => 'bg-green-100 text-green-700',
                            'border' => 'border-green-200',
                        ],
                        'Failed' => [
                            'label' => '✗ Failed',
                            'class' => 'bg-red-100 text-red-700',
                            'border' => 'border-red-200',
                        ],
                        'Cancelled' => [
                            'label' => '⊘ Cancelled',
                            'class' => 'bg-gray-100 text-gray-600',
                            'border' => 'border-gray-200',
                        ],
                    ];
                    $st = $statusMap[$shipping->delivery_status] ?? $statusMap['cancelled'];
                @endphp

                <a href="{{ route('shipping.driver.history', ['id' => $driver->id, 'delivery_number' => $shipping->delivery_number]) }}"
                    class="block bg-white rounded-xl border {{ $st['border'] }} shadow-sm hover:shadow-md transition mb-4 overflow-hidden">

                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="font-bold text-gray-900">{{ $shipping->delivery_number }}</p>
                                <p class="text-sm text-gray-500">{{ $firstRental?->customer?->name ?? 'N/A' }}</p>
                            </div>
                            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mb-3">
                            <div class="flex-1 bg-gray-50 rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500 mb-0.5">Tujuan</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $shipping->to_location }}</p>
                            </div>
                            @if ($shipping->proof_image_url)
                                <div class="w-12 h-12 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                                    <img src="{{ Storage::url($shipping->proof_image_url) }}" alt="Proof"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                            <div class="flex gap-3 text-xs text-gray-400">
                                <span>🧰 {{ $toolCount }} tools</span>
                                @if ($shipping->actual_arrival_time)
                                    <span>🕐
                                        {{ \Carbon\Carbon::parse($shipping->actual_arrival_time)->format('d M Y, H:i') }}</span>
                                @endif
                            </div>
                            <span class="text-sm font-semibold text-gray-500 flex items-center gap-1">
                                Lihat Detail
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 p-10 text-center">
                    <svg class="w-14 h-14 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-600 font-semibold">Belum ada riwayat</p>
                    <p class="text-gray-400 text-sm mt-1">Riwayat pengiriman akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

    </div>

    <script>
        function switchTab(tab) {
            const panels = {
                active: 'panel-active',
                history: 'panel-history'
            };
            const tabs = {
                active: 'tab-active',
                history: 'tab-history'
            };

            Object.keys(panels).forEach(key => {
                const isActive = key === tab;
                document.getElementById(panels[key]).classList.toggle('hidden', !isActive);
                const btn = document.getElementById(tabs[key]);
                if (isActive) {
                    btn.classList.add('bg-white', 'text-blue-600', 'shadow-sm');
                    btn.classList.remove('text-gray-500');
                } else {
                    btn.classList.remove('bg-white', 'text-blue-600', 'shadow-sm');
                    btn.classList.add('text-gray-500');
                }
            });
        }

        // Auto-switch ke history kalau dari redirect success re-upload
        @if (session('success') && str_contains(session('success'), 'diperbarui'))
            switchTab('history');
        @endif
    </script>
@endsection
