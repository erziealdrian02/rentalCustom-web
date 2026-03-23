{{-- resources/views/stock/createReStock.blade.php --}}
@extends('layout.app')

@section('content')

    {{-- Error validasi --}}
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-5 py-3 rounded-lg mb-6">
            @foreach ($errors->all() as $error)
                <p class="text-sm">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="max-w-full">
        <h2 class="text-2xl font-semibold text-gray-900 mb-6">Restock Tools</h2>

        <form id="restock-form" method="POST" action="{{ route('stock.restock.store') }}">
            @csrf
            <input type="hidden" name="warehouse_id" id="hidden-warehouseId">
            <input type="hidden" name="items" id="hidden-items">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" style="min-height: 600px;">

                {{-- KIRI --}}
                <div class="flex flex-col gap-6">

                    {{-- Card: Pilih Warehouse --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Restock Details</h3>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Warehouse *</label>
                            <select id="warehouseId"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                onchange="updateSubmitBtn()">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} — {{ $wh->location }}</option>
                                @endforeach
                            </select>

                            <textarea name="notes" placeholder="Additional notes..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>

                    {{-- Card: Tambah Tool --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Add Tools</h3>

                        <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">

                            {{-- Tool select --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tool *</label>
                                <select id="toolId" onchange="onToolChange()"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Tool</option>
                                    @foreach ($tools as $tool)
                                        <option value="{{ $tool->id_tools }}" data-name="{{ $tool->name }}"
                                            data-code="{{ $tool->code_tools }}"
                                            data-replacement="{{ $tool->replacement_value }}">
                                            {{ $tool->name }} ({{ $tool->code_tools }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quantity + Replacement Value (2 kolom) --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                                    <input type="number" id="quantity" value="1" min="1"
                                        oninput="onQtyChange()"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Replacement Value /
                                        unit</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm pointer-events-none">Rp.</span>
                                        <input type="text" id="replacement_value_display" placeholder="0" readonly
                                            class="w-full pl-10 pr-4 py-2 border border-gray-200 bg-gray-100 rounded-lg text-gray-600 cursor-not-allowed">
                                    </div>
                                </div>
                            </div>

                            {{-- Total replacement preview --}}
                            <div
                                class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 flex justify-between items-center">
                                <span class="text-sm text-blue-700">Total Replacement Value</span>
                                <span id="total_preview" class="font-semibold text-blue-800">Rp. 0</span>
                            </div>

                            <button type="button" onclick="addItem()"
                                class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                + Add Tool
                            </button>
                        </div>
                    </div>

                    {{-- Card: Summary + Submit --}}
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
                        <div id="summary" class="space-y-2 mb-6">
                            <p class="text-gray-500 text-sm">No items added yet.</p>
                        </div>
                        <button type="button" onclick="submitRestock()" id="submitBtn" disabled
                            class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition font-medium disabled:bg-gray-400 disabled:cursor-not-allowed">
                            Submit Restock
                        </button>
                    </div>

                </div>

                {{-- KANAN: Daftar item --}}
                <div class="flex flex-col bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Tools to Restock</h3>
                    </div>
                    <div id="itemsList" class="flex-1 overflow-y-auto p-6 space-y-4">
                        <p class="text-gray-500 text-center py-8 text-sm">
                            No tools added yet. Select a tool and quantity above.
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        let items = [];
        let nextId = 1;

        function fmtRp(val) {
            return 'Rp. ' + Number(val).toLocaleString('id-ID');
        }

        function getCurrentReplacement() {
            const toolSelect = document.getElementById('toolId');
            return parseInt(toolSelect.options[toolSelect.selectedIndex]?.dataset.replacement) || 0;
        }

        function updatePreview() {
            const qty = parseInt(document.getElementById('quantity').value) || 0;
            const rv = getCurrentReplacement();
            document.getElementById('total_preview').textContent = fmtRp(qty * rv);
        }

        // Auto-isi replacement value & preview saat tool dipilih
        function onToolChange() {
            const toolSelect = document.getElementById('toolId');
            const rv = parseInt(toolSelect.options[toolSelect.selectedIndex]?.dataset.replacement) || 0;
            document.getElementById('replacement_value_display').value =
                rv > 0 ? Number(rv).toLocaleString('id-ID') : '0';
            updatePreview();
        }

        // Update preview saat quantity berubah
        function onQtyChange() {
            updatePreview();
        }

        function addItem() {
            const toolSelect = document.getElementById('toolId');
            const toolId = toolSelect.value;
            const toolName = toolSelect.options[toolSelect.selectedIndex]?.dataset.name || '';
            const toolCode = toolSelect.options[toolSelect.selectedIndex]?.dataset.code || '';
            const replacementValue = getCurrentReplacement();
            const quantity = parseInt(document.getElementById('quantity').value) || 1;

            if (!document.getElementById('warehouseId').value) {
                alert('Please select a warehouse first.');
                return;
            }
            if (!toolId) {
                alert('Please select a tool.');
                return;
            }
            if (quantity < 1) {
                alert('Quantity must be at least 1.');
                return;
            }

            // Jika tool sudah ada, tambah quantity saja
            const existing = items.find(i => i.toolId === toolId);
            if (existing) {
                existing.quantity += quantity;
                existing.totalReplacement = existing.quantity * existing.replacementValue;
                renderUI();
                resetToolInput();
                return;
            }

            items.push({
                id: nextId++,
                toolId,
                toolName,
                toolCode,
                quantity,
                replacementValue,
                totalReplacement: quantity * replacementValue,
            });

            renderUI();
            resetToolInput();
        }

        function removeItem(id) {
            items = items.filter(i => i.id !== id);
            renderUI();
        }

        function resetToolInput() {
            document.getElementById('toolId').value = '';
            document.getElementById('quantity').value = '1';
            document.getElementById('replacement_value_display').value = '';
            document.getElementById('total_preview').textContent = 'Rp. 0';
        }

        function renderUI() {
            const summary = document.getElementById('summary');
            const itemsList = document.getElementById('itemsList');

            if (items.length === 0) {
                summary.innerHTML = '<p class="text-gray-500 text-sm">No items added yet.</p>';
                itemsList.innerHTML =
                    '<p class="text-gray-500 text-center py-8 text-sm">No tools added yet. Select a tool and quantity above.</p>';
                updateSubmitBtn();
                return;
            }

            const totalQty = items.reduce((s, i) => s + i.quantity, 0);
            const totalReplacement = items.reduce((s, i) => s + i.totalReplacement, 0);

            // Summary kiri
            summary.innerHTML = `
                <div class="text-sm text-gray-600 space-y-1.5 border-b border-gray-100 pb-3">
                    <div class="flex justify-between">
                        <span>Total Tools</span>
                        <span class="font-semibold text-gray-900">${items.length} item</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Qty</span>
                        <span class="font-semibold text-gray-900">${totalQty} unit</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Replacement Value</span>
                        <span class="font-semibold text-blue-700">${fmtRp(totalReplacement)}</span>
                    </div>
                </div>
                <div class="text-xs text-gray-500 pt-2 space-y-1.5">
                    ${items.map(i => `
                                                            <div class="flex justify-between">
                                                                <span>${i.quantity}x ${i.toolName}</span>
                                                                <span class="text-gray-700 font-medium">${fmtRp(i.totalReplacement)}</span>
                                                            </div>
                                                        `).join('')}
                </div>
            `;

            // Daftar item kanan
            itemsList.innerHTML = items.map(item => `
                <div class="bg-white rounded-lg border border-gray-200 p-4 shadow-sm">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">${item.toolName}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">${item.toolCode}</p>
                        </div>
                        <button onclick="removeItem(${item.id})"
                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                            Remove
                        </button>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="bg-gray-50 rounded p-2">
                            <p class="text-xs text-gray-500 mb-1">Qty</p>
                            <p class="font-semibold text-gray-900 text-sm">${item.quantity} unit</p>
                        </div>
                        <div class="bg-gray-50 rounded p-2">
                            <p class="text-xs text-gray-500 mb-1">Per Unit</p>
                            <p class="font-semibold text-gray-900 text-sm">${fmtRp(item.replacementValue)}</p>
                        </div>
                        <div class="bg-blue-50 rounded p-2">
                            <p class="text-xs text-blue-600 mb-1">Total Value</p>
                            <p class="font-semibold text-blue-700 text-sm">${fmtRp(item.totalReplacement)}</p>
                        </div>
                    </div>
                </div>
            `).join('');

            updateSubmitBtn();
        }

        function updateSubmitBtn() {
            const btn = document.getElementById('submitBtn');
            btn.disabled = !document.getElementById('warehouseId').value || items.length === 0;
        }

        function submitRestock() {
            const warehouseId = document.getElementById('warehouseId').value;
            if (!warehouseId || items.length === 0) {
                alert('Please select a warehouse and add at least one tool.');
                return;
            }

            document.getElementById('hidden-warehouseId').value = warehouseId;
            document.getElementById('hidden-items').value = JSON.stringify(items);

            document.getElementById('restock-form').submit();
        }

        let warehouseChoices = null;
        let toolsChoices = null;

        function initWarehouseChoices() {
            const el = document.getElementById('warehouseId');
            if (warehouseChoices) {
                warehouseChoices.destroy();
            }
            warehouseChoices = new Choices(el, {
                searchEnabled: true,
                searchPlaceholderValue: 'Cari warehouse...',
                itemSelectText: '',
                noResultsText: 'Warehouse tidak ditemukan',
                shouldSort: false,
            });

            // Gunakan event dari Choices, bukan onchange native
            el.addEventListener('change', function() {
                updateSubmitBtn();
            });
        }

        function initToolsChoices() {
            const el = document.getElementById('toolId');
            if (toolsChoices) {
                toolsChoices.destroy();
            }
            toolsChoices = new Choices(el, {
                searchEnabled: true,
                searchPlaceholderValue: 'Cari tools...',
                itemSelectText: '',
                noResultsText: 'Tools tidak ditemukan',
                shouldSort: false,
            });

            // Gunakan event dari Choices, bukan onchange native
            el.addEventListener('change', function() {
                onToolChange();
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initWarehouseChoices();
            initToolsChoices();
        });
    </script>

@endsection
