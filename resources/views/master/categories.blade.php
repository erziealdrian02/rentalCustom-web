{{-- resources/views/categories/index.blade.php --}}
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

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Tool Categories</h2>
        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Category
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Description</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $cat['name'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $cat['description'] }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    {{-- Tombol Edit --}}
                                    <button onclick="openModal({{ json_encode($cat) }})"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Edit
                                    </button>

                                    {{-- Tombol Delete --}}
                                    {{-- <form method="POST" action="{{ route('categories.destroy', $cat['id']) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                            <td colspan="3" class="px-6 py-8 text-center text-gray-400">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== MODAL ===================== --}}
    <div id="category-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">

            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Add Category</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- <form id="category-form" method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf --}}
                <span id="method-field"></span>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" id="f-name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="f-description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save Category
                </button>
            {{-- </form> --}}
        </div>
    </div>

    <script>
        function openModal(cat = null) {
            const modal = document.getElementById('category-modal');
            const form = document.getElementById('category-form');
            const title = document.getElementById('modal-title');
            const methodEl = document.getElementById('method-field');

            // Reset form
            form.reset();
            methodEl.innerHTML = '';

            if (cat) {
                // Mode Edit
                title.textContent = 'Edit Category';
                form.action = `/categories/${cat.id}`;
                methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                document.getElementById('f-name').value = cat.name;
                document.getElementById('f-description').value = cat.description;
            } else {
                // Mode Add
                title.textContent = 'Add Category';
                form.action = "{{ route('categories.store') }}";
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('category-modal').classList.add('hidden');
        }

        // Tutup modal klik luar
        document.getElementById('category-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
@endsection
