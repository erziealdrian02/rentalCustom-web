@extends('layout.app')

@section('content')
    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div id="notif"
            class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('notif')?.remove(), 3000);
        </script>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tool Inventory</h2>
        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Tool
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Code</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Category</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Serial Number</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Value</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tools as $tool)
                    {{-- {{ dd($tools->first()) }} --}}
                        @php
                            $status = ucfirst($tool->status);

                            $statusColor = match ($status) {
                                'Available' => 'bg-green-100 text-green-700',
                                'Rented' => 'bg-blue-100 text-blue-700',
                                'Damaged' => 'bg-yellow-100 text-yellow-700',
                                'Lost' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ $tool->code_tools }}</td>
                            <td class="px-6 py-4 font-semibold">{{ $tool->name }}</td>
                            <td class="px-6 py-4">{{ $tool->category->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $tool->serial_number }}</td>
                            <td class="px-6 py-4">${{ number_format($tool->replacement_value) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $status = ucfirst($tool->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    {{-- Tombol Edit --}}
                                    <button onclick="openModal({{ json_encode($tool) }})"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Edit
                                    </button>

                                    {{-- Tombol Delete --}}
                                    <form method="POST" action="{{ route('tools.destroy', $tool['id_tools']) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus tool ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-400">No tools found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Footer Tabel: Per Page + Pagination --}}
        <div
            class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 border-t border-gray-200 bg-white">

            {{-- Kiri: Per Page Selector + Info --}}
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <form method="GET" action="{{ route('master.tools') }}" id="per-page-form">
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

            {{-- Kanan: Pagination custom (tanpa teks "Showing X to Y") --}}
            <div class="flex items-center gap-1">
                {{-- Prev --}}
                @if ($tools->onFirstPage())
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $tools->previousPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">‹</a>
                @endif

                {{-- Page Numbers --}}
                @foreach ($tools->getUrlRange(1, $tools->lastPage()) as $page => $url)
                    @if ($page == $tools->currentPage())
                        <span
                            class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&per_page={{ request('per_page', 10) }}"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">{{ $page }}</a>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($tools->hasMorePages())
                    <a href="{{ $tools->nextPageUrl() }}&per_page={{ request('per_page', 10) }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">›</a>
                @else
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">›</span>
                @endif
            </div>

        </div>
    </div>

    {{-- ===================== MODAL ===================== --}}
    <div id="tool-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">

            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Add Tool</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Form dinamis: POST untuk add, PUT untuk edit --}}
            <form id="tool-form" method="POST" action="{{ route('tools.store') }}" class="space-y-4">
                @csrf
                <span id="method-field"></span>

                <input type="" id="tool-id" name="tool_id" value="">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tool Name</label>
                    <input type="text" name="name" id="f-name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" id="f-category" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @forelse($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @empty
                            <option>No categories found</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Replacement Value ($)</label>
                    <input type="number" name="replacementValue" id="f-replacementValue" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="f-status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Available</option>
                        <option>Rented</option>
                        <option>Damaged</option>
                        <option>Lost</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save Tool
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal(tool = null) {
            const modal = document.getElementById('tool-modal');
            const form = document.getElementById('tool-form');
            const title = document.getElementById('modal-title');
            const methodEl = document.getElementById('method-field');

            // Reset form
            form.reset();
            document.getElementById('tool-id').value = '';
            methodEl.innerHTML = '';

            if (tool) {
                // Mode Edit
                title.textContent = `Edit Tool ${tool.code_tools}`;
                form.action = `/master/tools/update/${tool.id_tools}`;
                methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                document.getElementById('tool-id').value = tool.id_tools;
                document.getElementById('f-name').value = tool.name;
                document.getElementById('f-replacementValue').value = tool.replacement_value;

                // Set select values
                setSelect('f-category', tool.category_id);
                setSelect('f-status', tool.status);
            } else {
                // Mode Add
                title.textContent = 'Add Tool';
                form.action = "{{ route('tools.store') }}";
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('tool-modal').classList.add('hidden');
        }

        function setSelect(id, value) {
            const sel = document.getElementById(id);
            for (let opt of sel.options) {
                opt.selected = opt.value === value || opt.text === value;
            }
        }

        // Tutup modal klik luar
        document.getElementById('tool-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
@endsection
