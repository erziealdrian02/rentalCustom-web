{{-- resources/views/users/index.blade.php --}}
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
        <h2 class="text-xl font-semibold text-gray-900">System Users</h2>
        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add User
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Email</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Role</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        @php
                            $statusColor =
                                $user['status'] === 'Active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700';

                            $roleColor =
                                $user['role'] === 'Administrator'
                                    ? 'bg-purple-100 text-purple-800'
                                    : 'bg-blue-100 text-blue-800';
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $user['name'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $user['email'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $roleColor }}">
                                    {{ $user['role'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $user['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    {{-- Tombol Edit --}}
                                    <button onclick="openModal({{ json_encode($user) }})"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Edit
                                    </button>

                                    {{-- Tombol Delete --}}
                                    {{-- <form method="POST" action="{{ route('users.destroy', $user['id']) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== MODAL ADD / EDIT ===================== --}}
    <div id="user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">

            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Add User</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="user-form" method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf
                <span id="method-field"></span>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="name" id="f-name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="f-email" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="f-role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Administrator">Administrator</option>
                        <option value="Warehouse Manager">Warehouse Manager</option>
                        <option value="Staff">Staff</option>
                        <option value="Clerk">Clerk</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="f-status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save User
                </button>
            </form>
        </div>
    </div>

    <script>
        function openModal(user = null) {
            const modal = document.getElementById('user-modal');
            const form = document.getElementById('user-form');
            const title = document.getElementById('modal-title');
            const methodEl = document.getElementById('method-field');

            form.reset();
            methodEl.innerHTML = '';

            if (user) {
                title.textContent = 'Edit User';
                form.action = `/users/${user.id}`;
                methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                document.getElementById('f-name').value = user.name;
                document.getElementById('f-email').value = user.email;

                setSelect('f-role', user.role);
                setSelect('f-status', user.status);
            } else {
                title.textContent = 'Add User';
                form.action = "{{ route('users.store') }}";
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('user-modal').classList.add('hidden');
        }

        function setSelect(id, value) {
            const sel = document.getElementById(id);
            for (let opt of sel.options) {
                opt.selected = opt.value === value;
            }
        }

        // Tutup modal klik luar
        document.getElementById('user-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
@endsection
