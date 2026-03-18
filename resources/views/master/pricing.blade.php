{{-- resources/views/pricing/index.blade.php --}}
@extends('layout.app')

@section('content')
    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div id="notif"
            class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('notif')?.remove(), 3000);
        </script>
    @endif

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Rental Pricing Setup</h2>
        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Pricing
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Daily Rate</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Weekly Rate</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Monthly Rate</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pricing as $price)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $price['toolName'] }}</td>
                            <td class="px-6 py-4">${{ number_format($price['dailyRate'], 2) }}</td>
                            <td class="px-6 py-4">${{ number_format($price['weeklyRate'], 2) }}</td>
                            <td class="px-6 py-4">${{ number_format($price['monthlyRate'], 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    {{-- Tombol Edit --}}
                                    <button onclick="openModal({{ json_encode($price) }})"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Edit
                                    </button>

                                    {{-- Tombol Delete --}}
                                    {{-- <form method="POST" action="{{ route('pricing.destroy', $price['id']) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus pricing ini?')">
                                        @csrf
                                        @method('DELETE') --}}
                                        <button type="submit"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                            Delete
                                        </button>
                                    {{-- </form> --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No pricing found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== MODAL ADD / EDIT ===================== --}}
    <div id="pricing-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Add Pricing</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="pricing-form" method="POST" action="{{ route('pricing.store') }}" class="space-y-4">
                @csrf
                <span id="method-field"></span>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tool</label>
                    <select name="toolId" id="f-toolId" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Tool</option>
                        @foreach ($tools as $tool)
                            <option value="{{ $tool['id'] }}">{{ $tool['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Daily Rate ($)</label>
                    <input type="number" name="dailyRate" id="f-dailyRate" step="0.01" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Weekly Rate ($)</label>
                    <input type="number" name="weeklyRate" id="f-weeklyRate" step="0.01" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rate ($)</label>
                    <input type="number" name="monthlyRate" id="f-monthlyRate" step="0.01" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save Pricing
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal(price = null) {
            const modal = document.getElementById('pricing-modal');
            const form = document.getElementById('pricing-form');
            const title = document.getElementById('modal-title');
            const methodEl = document.getElementById('method-field');

            form.reset();
            methodEl.innerHTML = '';

            if (price) {
                title.textContent = 'Edit Pricing';
                form.action = `/pricing/${price.id}`;
                methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                // Set select tool
                const sel = document.getElementById('f-toolId');
                for (let opt of sel.options) {
                    opt.selected = parseInt(opt.value) === price.toolId;
                }

                document.getElementById('f-dailyRate').value = price.dailyRate;
                document.getElementById('f-weeklyRate').value = price.weeklyRate;
                document.getElementById('f-monthlyRate').value = price.monthlyRate;
            } else {
                title.textContent = 'Add Pricing';
                form.action = "{{ route('pricing.store') }}";
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('pricing-modal').classList.add('hidden');
        }

        // Tutup modal klik luar
        document.getElementById('pricing-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
@endsection
