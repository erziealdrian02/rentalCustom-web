@extends('layout.app')

@section('content')
    <div class="max-w-2xl mx-auto">

        {{-- Back --}}
        <a href="{{ route('shipping.driver', $driver->id) }}"
            class="inline-flex items-center gap-2 text-blue-600 font-semibold mb-5 hover:text-blue-700 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Riwayat
        </a>

        @if (session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-5 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="bg-gradient-to-r from-gray-700 to-gray-900 text-white rounded-xl p-5 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-400 text-sm mb-1">Riwayat Pengiriman</p>
                    <h2 class="text-2xl font-bold">{{ $shipping->delivery_number }}</h2>
                </div>
                @php
                    $statusMap = [
                        'Delivered' => ['label' => '✓ Delivered', 'class' => 'bg-green-500 text-white'],
                        'Failed' => ['label' => '✗ Failed', 'class' => 'bg-red-500 text-white'],
                        'Cancelled' => ['label' => '⊘ Cancelled', 'class' => 'bg-gray-500 text-white'],
                    ];
                    $st = $statusMap[$shipping->delivery_status] ?? $statusMap['cancelled'];
                @endphp
                <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $st['class'] }}">
                    {{ $st['label'] }}
                </span>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-4">Timeline</p>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Dibuat</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($shipping->created_at)->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="w-8 h-8 rounded-full {{ $shipping->departure_time ? 'bg-yellow-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 {{ $shipping->departure_time ? 'text-yellow-600' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Keberangkatan</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $shipping->departure_time ? \Carbon\Carbon::parse($shipping->departure_time)->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div
                        class="w-8 h-8 rounded-full {{ $shipping->actual_arrival_time ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 {{ $shipping->actual_arrival_time ? 'text-green-600' : 'text-gray-400' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tiba</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $shipping->actual_arrival_time ? \Carbon\Carbon::parse($shipping->actual_arrival_time)->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tools per Rental --}}
        @php
            $rentalIds = is_array($shipping->rental_id)
                ? $shipping->rental_id
                : json_decode($shipping->rental_id, true) ?? [];
        @endphp

        <div class="space-y-3 mb-5">
            <p class="text-xs font-semibold text-gray-500 uppercase px-1">Rental Items</p>
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
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $rental?->invoice_number ?? $rid }}</p>
                            <p class="text-xs text-gray-500">{{ $rental?->customer?->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ count($movIds) }} tools</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 px-4 py-3 border-b bg-gray-50/50 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Dari</p>
                            <p class="font-semibold text-gray-800 text-xs">{{ $fromName ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Ke</p>
                            <p class="font-semibold text-gray-800 text-xs">{{ $shipping->to_location }}</p>
                        </div>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach ($movIds as $mid)
                            @php $mov = $movements[$mid] ?? null; @endphp
                            @if ($mov)
                                <div class="flex justify-between items-center bg-gray-50 rounded-lg px-3 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $mov->tool?->name ?? $mid }}</p>
                                        <p class="text-xs text-gray-400">{{ $mov->tool?->code_tools ?? '' }}</p>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600 bg-gray-200 px-2 py-1 rounded">Qty:
                                        {{ $mov->quantity }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Proof Image + Re-upload --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase mb-4">Bukti Pengiriman</p>

            {{-- Current proof --}}
            @if ($shipping->proof_image_url)
                <div class="mb-5">
                    <p class="text-sm text-gray-500 mb-2">Foto saat ini:</p>
                    <div class="rounded-xl overflow-hidden border border-gray-200">
                        <img src="{{ Storage::url($shipping->proof_image_url) }}" alt="Bukti Pengiriman"
                            class="w-full object-cover max-h-72 cursor-pointer" onclick="openImageModal(this.src)">
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5 text-center">Klik foto untuk perbesar</p>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-5 text-center">
                    <p class="text-yellow-700 text-sm font-medium">Belum ada foto bukti pengiriman</p>
                </div>
            @endif

            @if ($shipping->notes)
                <div class="bg-gray-50 rounded-lg p-3 mb-5">
                    <p class="text-xs text-gray-500 mb-1">Catatan:</p>
                    <p class="text-sm text-gray-700">{{ $shipping->notes }}</p>
                </div>
            @endif

            {{-- Re-upload form --}}
            <div class="border-t pt-5">
                <p class="text-sm font-semibold text-gray-900 mb-1">Upload Ulang Bukti</p>
                <p class="text-xs text-gray-500 mb-4">Ganti foto bukti pengiriman jika diperlukan.</p>

                <form method="POST"
                    action="{{ route('shipping.driver.reupload', ['id' => $driver->id, 'delivery_number' => $shipping->delivery_number]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                            @foreach ($errors->all() as $e)
                                <p>{{ $e }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-blue-400 transition cursor-pointer mb-4"
                        onclick="document.getElementById('new_proof').click()">
                        <input type="file" id="new_proof" name="proof_image" accept="image/*" class="hidden"
                            onchange="previewNew(this)">
                        <div id="new-placeholder">
                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm text-gray-500">Klik untuk pilih foto baru</p>
                        </div>
                        <div id="new-preview" class="hidden">
                            <img id="new-preview-img" src="" alt="New Preview"
                                class="max-h-48 mx-auto rounded-lg object-cover">
                            <p class="text-green-600 text-xs font-medium mt-2">✓ Foto baru siap diupload</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <textarea name="notes" rows="2" placeholder="Catatan (opsional)..."
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ $shipping->notes }}</textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition text-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Upload Bukti Baru
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Image Modal --}}
    <div id="imageModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4"
        onclick="closeImageModal()">
        <div class="max-w-2xl w-full">
            <img id="modal-img" src="" alt="Bukti" class="w-full rounded-xl object-contain max-h-[80vh]">
            <p class="text-white text-center text-sm mt-3 opacity-60">Klik di mana saja untuk tutup</p>
        </div>
    </div>

    <script>
        function previewNew(input) {
            if (!input.files?.[0]) return;
            const file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('File terlalu besar. Maksimal 5MB.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('new-preview-img').src = e.target.result;
                document.getElementById('new-placeholder').classList.add('hidden');
                document.getElementById('new-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function openImageModal(src) {
            document.getElementById('modal-img').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
            document.body.style.overflow = '';
        }
    </script>
@endsection
