{{-- ===================== RENTAL DETAIL MODAL ===================== --}}
<div id="rentalModal" class="modal fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto modal-enter">
        <div class="sticky top-0 bg-white border-b p-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Rental Details</h2>
            <button onclick="closeRentalModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="rentalModalContent" class="p-6"></div>
        <div class="border-t p-6 flex gap-3 justify-end bg-gray-50">
            <button onclick="closeRentalModal()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Close</button>
            <button onclick="printRentalModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H9a2 2 0 002 2v2a2 2 0 002 2h6a2 2 0 002-2v-2a2 2 0 00-2-2zm-6-4a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                Print Invoice
            </button>
        </div>
    </div>
</div>

{{-- ===================== RETURN DETAIL MODAL ===================== --}}
<div id="returnModal" class="modal fixed inset-0 bg-black/50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto modal-enter">
        <div class="sticky top-0 bg-white border-b p-6 flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900">Return Details</h2>
            <button onclick="closeReturnModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="returnModalContent" class="p-6"></div>
        <div class="border-t p-6 flex gap-3 justify-end bg-gray-50">
            <button onclick="closeReturnModal()"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Close</button>
            <button onclick="printReturnModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4H9a2 2 0 002 2v2a2 2 0 002 2h6a2 2 0 002-2v-2a2 2 0 00-2-2zm-6-4a2 2 0 100-4 2 2 0 000 4z" />
                </svg>
                Print Receipt
            </button>
        </div>
    </div>
</div>
