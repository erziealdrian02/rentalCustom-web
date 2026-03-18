{{-- resources/views/warehouses/index.blade.php --}}
@extends('layout.app')

@section('content')

{{-- Notifikasi sukses --}}
@if(session('success'))
<div id="notif"
     class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
<script>setTimeout(() => document.getElementById('notif')?.remove(), 3000);</script>
@endif

{{-- Header --}}
<div class="flex justify-between items-center mb-8">
    <h2 class="text-xl font-semibold text-gray-900">Warehouse Management</h2>
    <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add Warehouse
    </button>
</div>

{{-- Warehouse Cards grouped by location --}}
@forelse($grouped as $region => $warehouses)
<div class="mb-12">
    <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b-2 border-blue-500">
        {{ $region }}
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($warehouses as $warehouse)
        @php
            $utilization      = $warehouse['capacity'] > 0
                                ? round(($warehouse['currentStock'] / $warehouse['capacity']) * 100)
                                : 0;
            $utilizationColor = $utilization > 80 ? 'text-red-600'
                              : ($utilization > 60  ? 'text-yellow-600' : 'text-green-600');
            $available        = $warehouse['capacity'] - $warehouse['currentStock'];
        @endphp

        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-lg transition">

            {{-- Card Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4">
                <h4 class="font-bold text-lg">{{ $warehouse['name'] }}</h4>
                <p class="text-blue-100 text-sm">ID: {{ $warehouse['id'] }}</p>
            </div>

            {{-- Card Body --}}
            <div class="p-4 space-y-3">

                {{-- Location --}}
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-600">Location</p>
                        <p class="font-semibold text-gray-900">{{ $warehouse['location'] }}</p>
                    </div>
                </div>

                {{-- Capacity Bar --}}
                <div class="bg-gray-50 rounded p-3">
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm font-semibold text-gray-700">Capacity</p>
                        <span class="text-sm font-bold text-gray-900">
                            {{ $warehouse['currentStock'] }}/{{ $warehouse['capacity'] }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-300 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition"
                             style="width: {{ $utilization }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-2">
                        <span class="{{ $utilizationColor }} font-semibold">{{ $utilization }}%</span> Utilized
                    </p>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 gap-2 pt-2">
                    <div class="bg-green-50 rounded p-2">
                        <p class="text-xs text-gray-600">Available</p>
                        <p class="font-bold text-green-700">{{ $available }}</p>
                    </div>
                    <div class="bg-blue-50 rounded p-2">
                        <p class="text-xs text-gray-600">Using</p>
                        <p class="font-bold text-blue-700">{{ $warehouse['currentStock'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Card Footer --}}
            <div class="border-t bg-gray-50 p-4 flex gap-2">
                <button onclick="openModal({{ json_encode($warehouse) }})"
                        class="flex-1 px-3 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Edit
                </button>

                {{-- <form method="POST" action="{{ route('warehouses.destroy', $warehouse['id']) }}"
                      onsubmit="return confirm('Yakin ingin menghapus warehouse ini?')"
                      class="flex-1">
                    @csrf
                    @method('DELETE') --}}
                    <button type="submit"
                            class="w-full px-3 py-2 text-sm bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Delete
                    </button>
                {{-- </form> --}}
            </div>
        </div>
        @endforeach
    </div>
</div>
@empty
<div class="text-center text-gray-400 py-16">No warehouses found.</div>
@endforelse


{{-- ===================== MODAL ADD / EDIT ===================== --}}
<div id="warehouse-modal"
     class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">

        <div class="flex justify-between items-center mb-4">
            <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Add Warehouse</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- <form id="warehouse-form" method="POST" action="{{ route('warehouses.store') }}" class="space-y-4">
            @csrf --}}
            <span id="method-field"></span>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse Name</label>
                <input type="text" name="name" id="f-name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                <input type="text" name="location" id="f-location" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Capacity</label>
                <input type="number" name="capacity" id="f-capacity" min="1" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                <input type="number" name="currentStock" id="f-currentStock" min="0" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Save
                </button>
            </div>
        {{-- </form> --}}
    </div>
</div>

<script>
    function openModal(warehouse = null) {
        const modal    = document.getElementById('warehouse-modal');
        const form     = document.getElementById('warehouse-form');
        const title    = document.getElementById('modal-title');
        const methodEl = document.getElementById('method-field');

        form.reset();
        methodEl.innerHTML = '';

        if (warehouse) {
            title.textContent  = 'Edit Warehouse';
            form.action        = `/warehouses/${warehouse.id}`;
            methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

            document.getElementById('f-name').value         = warehouse.name;
            document.getElementById('f-location').value     = warehouse.location;
            document.getElementById('f-capacity').value     = warehouse.capacity;
            document.getElementById('f-currentStock').value = warehouse.currentStock;
        } else {
            title.textContent = 'Add Warehouse';
            form.action       = "{{ route('warehouses.store') }}";
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('warehouse-modal').classList.add('hidden');
    }

    // Tutup modal klik luar
    document.getElementById('warehouse-modal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>

@endsection