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
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl p-5 mb-6">
            <p class="text-green-100 text-sm mb-1">Konfirmasi Kedatangan</p>
            <h2 class="text-2xl font-bold">{{ $shipping->delivery_number }}</h2>
            <p class="text-green-100 text-sm mt-1">
                Berangkat: {{ \Carbon\Carbon::parse($shipping->departure_time)->format('d M Y, H:i') }}
            </p>
        </div>

        {{-- Summary Tools --}}
        @php
            $rentalIds = is_array($shipping->rental_id)
                ? $shipping->rental_id
                : json_decode($shipping->rental_id, true) ?? [];
        @endphp

        <div class="space-y-3 mb-6">
            @foreach ($rentalIds as $rid)
                @php
                    $rental = $rentals[$rid] ?? null;
                    $movIds = $rental ? json_decode($rental->movement_id, true) ?? [] : [];
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $rental?->invoice_number ?? $rid }}</p>
                            <p class="text-xs text-gray-500">{{ $rental?->customer?->name ?? 'N/A' }}</p>
                        </div>
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-medium">
                            {{ count($movIds) }} tools
                        </span>
                    </div>
                    <div class="p-4 space-y-2">
                        @foreach ($movIds as $mid)
                            @php $mov = $movements[$mid] ?? null; @endphp
                            @if ($mov)
                                <div class="flex justify-between items-center bg-green-50 rounded-lg px-3 py-2">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $mov->tool?->name ?? $mid }}</p>
                                        <p class="text-xs text-gray-500">{{ $mov->tool?->code_tools ?? '' }}</p>
                                    </div>
                                    <span class="text-sm font-bold text-green-600">Qty: {{ $mov->quantity }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Arrival Form --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="font-semibold text-gray-900 mb-1">Konfirmasi Kedatangan</h3>
            <p class="text-sm text-gray-500 mb-5">Upload foto bukti pengiriman untuk menyelesaikan.</p>

            <form method="POST" action="{{ route('shipping.driver.arrival.update', $shipping->delivery_number) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Photo Upload --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Foto Bukti Pengiriman <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-green-400 transition cursor-pointer"
                        onclick="document.getElementById('proof_image').click()">
                        <input type="file" id="proof_image" name="proof_image" accept="image/*" class="hidden"
                            onchange="previewProof(this)">
                        <div id="upload-placeholder">
                            <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-600 font-medium text-sm">Klik untuk upload foto</p>
                            <p class="text-gray-400 text-xs mt-1">JPG, PNG (Max 5MB)</p>
                        </div>
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" src="" alt="Preview"
                                class="max-h-64 mx-auto rounded-lg object-cover">
                            <p class="text-green-600 text-sm font-medium mt-2">✓ Foto siap diupload</p>
                            <p class="text-xs text-gray-400 mt-1">Klik untuk ganti foto</p>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-5">
                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">
                        Catatan (Opsional)
                    </label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Tambahkan catatan pengiriman..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none">{{ old('notes') }}</textarea>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full py-4 bg-green-600 hover:bg-green-700 text-white font-bold text-lg rounded-xl transition flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Selesaikan Pengiriman
                </button>
            </form>
        </div>
    </div>

    <script>
        function previewProof(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('File terlalu besar. Maksimal 5MB.');
                input.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = e => {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('upload-placeholder').classList.add('hidden');
                document.getElementById('image-preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    </script>
@endsection
