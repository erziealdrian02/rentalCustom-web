@extends('layout.app')

@section('content')
    @if (session('success'))
        <div id="notif"
            class="fixed top-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
        <script>
            setTimeout(() => document.getElementById('notif')?.remove(), 3000)
        </script>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900">Driver List</h2>
        <button onclick="openModal()"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Driver
        </button>
    </div>

    {{-- Search --}}
    <div class="mb-4">
        <form method="GET" action="{{ route('master.drivers') }}" id="search-form">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, phone, or license..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"
                    oninput="debounceSearch(this.form)">
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        @php
                            $sortBy = request('sort_by', 'name');
                            $sortDir = request('sort_dir', 'asc');
                            $columns = [
                                'name' => 'Name',
                                'phone' => 'Phone',
                                'license_plate' => 'License Number',
                                'vehicle_type' => 'Vehicle Type',
                                'status' => 'Status',
                            ];
                        @endphp

                        @foreach ($columns as $col => $label)
                            @php
                                $isSorted = $sortBy === $col;
                                $nextDir = $isSorted && $sortDir === 'asc' ? 'desc' : 'asc';
                                $sortUrl = route(
                                    'master.drivers',
                                    array_merge(request()->except('page'), ['sort_by' => $col, 'sort_dir' => $nextDir]),
                                );
                            @endphp
                            <th class="px-6 py-3 text-left">
                                <a href="{{ $sortUrl }}"
                                    class="inline-flex items-center gap-1 font-semibold text-gray-700 hover:text-blue-600 transition select-none group">
                                    {{ $label }}
                                    <span class="flex flex-col leading-none text-gray-400 group-hover:text-blue-500">
                                        <svg class="w-3 h-3 -mb-0.5 {{ $isSorted && $sortDir === 'asc' ? 'text-blue-600' : '' }}"
                                            viewBox="0 0 10 6" fill="currentColor">
                                            <path d="M5 0L10 6H0z" />
                                        </svg>
                                        <svg class="w-3 h-3 {{ $isSorted && $sortDir === 'desc' ? 'text-blue-600' : '' }}"
                                            viewBox="0 0 10 6" fill="currentColor">
                                            <path d="M5 6L0 0H10z" />
                                        </svg>
                                    </span>
                                </a>
                            </th>
                        @endforeach

                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $driver)
                        @php
                            $status = ucfirst($driver->status);
                            $statusColor = match ($status) {
                                'Active' => 'bg-green-100 text-green-700',
                                'Inactive' => 'bg-gray-100 text-gray-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold">{{ $driver->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $driver->phone }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $driver->license_plate }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $driver->vehicle_type }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded {{ $statusColor }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('shipping.driver', $driver->id) }}"
                                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Detail
                                    </a>
                                    <button onclick="openModal({{ json_encode($driver) }})"
                                        class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">
                                        Edit
                                    </button>
                                    <form method="POST" action="{{ route('drivers.destroy', $driver->id) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus driver ini?')">
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
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">No drivers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer: Per Page + Pagination --}}
        <div
            class="px-6 py-4 flex flex-col sm:flex-row justify-between items-center gap-3 border-t border-gray-200 bg-white">
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Show</span>
                <form method="GET" action="{{ route('master.drivers') }}" id="per-page-form">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="sort_by" value="{{ request('sort_by', 'name') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">
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
                    &mdash; Showing {{ $drivers->firstItem() }}-{{ $drivers->lastItem() }} of {{ $drivers->total() }}
                </span>
            </div>

            <div class="flex items-center gap-1">
                @if ($drivers->onFirstPage())
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $drivers->previousPageUrl() }}&per_page={{ request('per_page', 10) }}&search={{ request('search') }}&sort_by={{ request('sort_by', 'name') }}&sort_dir={{ request('sort_dir', 'asc') }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">‹</a>
                @endif

                @foreach ($drivers->getUrlRange(1, $drivers->lastPage()) as $page => $url)
                    @if ($page == $drivers->currentPage())
                        <span
                            class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&per_page={{ request('per_page', 10) }}&search={{ request('search') }}&sort_by={{ request('sort_by', 'name') }}&sort_dir={{ request('sort_dir', 'asc') }}"
                            class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($drivers->hasMorePages())
                    <a href="{{ $drivers->nextPageUrl() }}&per_page={{ request('per_page', 10) }}&search={{ request('search') }}&sort_by={{ request('sort_by', 'name') }}&sort_dir={{ request('sort_dir', 'asc') }}"
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-100 transition">›</a>
                @else
                    <span
                        class="px-3 py-1.5 text-sm rounded-lg border border-gray-200 text-gray-300 cursor-not-allowed">›</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="driver-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 id="modal-title" class="text-xl font-semibold text-gray-900">Add Driver</h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="driver-form" method="POST" action="{{ route('drivers.store') }}" class="space-y-4">
                @csrf
                <span id="method-field"></span>
                <input type="hidden" id="driver-id" name="driver_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="f-name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="phone" id="f-phone" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">License Number</label>
                    <input type="text" name="license_plate" id="f-license" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Type</label>
                    <input type="text" name="vehicle_type" id="f-vehicle" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="f-status" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-semibold">
                    Save Driver
                </button>
            </form>
        </div>
    </div>

    <script>
        let searchTimeout;

        function debounceSearch(form) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => form.submit(), 400);
        }

        function openModal(driver = null) {
            const modal = document.getElementById('driver-modal');
            const form = document.getElementById('driver-form');
            const title = document.getElementById('modal-title');
            const methodEl = document.getElementById('method-field');

            form.reset();
            methodEl.innerHTML = '';
            document.getElementById('driver-id').value = '';

            if (driver) {
                title.textContent = `Edit Driver: ${driver.name}`;
                form.action = `/master/drivers/update/${driver.id}`;
                methodEl.innerHTML = `<input type="hidden" name="_method" value="PUT">`;

                document.getElementById('driver-id').value = driver.id;
                document.getElementById('f-name').value = driver.name;
                document.getElementById('f-phone').value = driver.phone;
                document.getElementById('f-license').value = driver.license_plate;
                document.getElementById('f-vehicle').value = driver.vehicle_type;
                setSelect('f-status', driver.status);
            } else {
                title.textContent = 'Add Driver';
                form.action = "{{ route('drivers.store') }}";
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('driver-modal').classList.add('hidden');
        }

        function setSelect(id, value) {
            const sel = document.getElementById(id);
            for (let opt of sel.options) {
                opt.selected = opt.value === value || opt.text === value;
            }
        }

        document.getElementById('driver-modal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
    </script>
@endsection
