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
        {{-- <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Pricing
        </button> --}}
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Code Tools</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Tool Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Daily Rate</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Weekly Rate</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Monthly Rate</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tools as $tool)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $tool['code_tools'] }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $tool['name'] }}</td>
                            <td class="px-6 py-4">Rp.{{ number_format($tool['daily_rate']) }}</td>
                            <td class="px-6 py-4">Rp.{{ number_format($tool['weekly_rate']) }}</td>
                            <td class="px-6 py-4">Rp.{{ number_format($tool['monthly_rate']) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    {{-- Tombol Edit --}}
                                    <button onclick="openModal({{ json_encode($tool) }})"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Edit
                                    </button>
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
                    <select name="toolId" id="f-toolId" disabled
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Tool</option>
                        @foreach ($tools as $tool)
                            {{-- gunakan id_tools bukan id --}}
                            <option value="{{ $tool->id_tools }}">{{ $tool->name }}</option>
                        @endforeach
                    </select>
                </div>

                @foreach ([['label' => 'Daily Rate', 'name' => 'daily_rate'], ['label' => 'Weekly Rate', 'name' => 'weekly_rate'], ['label' => 'Monthly Rate', 'name' => 'monthly_rate']] as $rate)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $rate['label'] }} (Rp.)</label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm pointer-events-none">Rp.</span>
                            <input type="text" id="f-{{ $rate['name'] }}_display" placeholder="0"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <input type="hidden" name="{{ $rate['name'] }}" id="f-{{ $rate['name'] }}">
                    </div>
                @endforeach

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save Pricing
                </button>
            </form>
        </div>
    </div> {{-- ← tutup modal di sini --}}

    {{-- Footer Tabel: Per Page + Pagination (di luar modal) --}}
    <div class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 border-t border-gray-200 bg-white">

        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Show</span>
            <form method="GET" action="{{ route('master.pricing') }}" id="per-page-form">
                <select name="per_page" onchange="document.getElementById('per-page-form').submit()"
                    class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    @foreach ([10, 50, 100] as $size)
                        <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                            {{ $size }}
                        </option>
                    @endforeach
                </select>
            </form>
            <span>entries</span>
            <span class="text-gray-400">
                &mdash; Showing {{ $tools->firstItem() }}-{{ $tools->lastItem() }} of {{ $tools->total() }}
            </span>
        </div>

        <div class="flex items-center gap-1">
            @if ($tools->onFirstPage())
                <span
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">‹</span>
            @else
                <a href="{{ $tools->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">‹</a>
            @endif

            @foreach ($tools->getUrlRange(1, $tools->lastPage()) as $page => $url)
                @if ($page == $tools->currentPage())
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">{{ $page }}</a>
                @endif
            @endforeach

            @if ($tools->hasMorePages())
                <a href="{{ $tools->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">›</a>
            @else
                <span
                    class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">›</span>
            @endif
        </div>
    </div>

    <script>
        // Format input rate
        ['daily_rate', 'weekly_rate', 'monthly_rate'].forEach(function(field) {
            document.getElementById('f-' + field + '_display').addEventListener('input', function() {
                let raw = this.value.replace(/\D/g, '');
                this.value = raw.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                document.getElementById('f-' + field).value = raw;
            });
        });

        function openModal(price = null) {
            const modal = document.getElementById('pricing-modal');
            const form = document.getElementById('pricing-form');
            const title = document.getElementById('modal-title');
            const methodEl = document.getElementById('method-field');

            form.reset();
            methodEl.innerHTML = '';

            if (price) {
                title.textContent = `Edit Pricing ${price.code_tools}`;
                form.action = `/master/pricing/update/${price.id_tools}`;
                methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                // Set select tool
                const sel = document.getElementById('f-toolId');
                for (let opt of sel.options) {
                    opt.selected = opt.value === price.id_tools
                }

                ['daily_rate', 'weekly_rate', 'monthly_rate'].forEach(function(field) {
                    document.getElementById('f-' + field + '_display').value = '';
                    document.getElementById('f-' + field).value = '';
                });

                ['daily_rate', 'weekly_rate', 'monthly_rate'].forEach(function(field) {
                    if (price[field] !== undefined) {
                        const formatted = Number(price[field]).toLocaleString('id-ID');
                        document.getElementById('f-' + field + '_display').value = formatted;
                        document.getElementById('f-' + field).value = price[field];
                    }
                });
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
