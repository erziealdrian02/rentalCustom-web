<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Rentals;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ReturnsController extends Controller
{
    private function getReturns()
    {
        return session('returns', [['id' => 1, 'returnId' => 'RET-2025-001', 'invoiceNumber' => 'INV-2025-001', 'toolName' => 'Angle Grinder', 'customerName' => 'John Doe', 'returnDate' => '2025-01-17', 'condition' => 'Good', 'status' => 'Completed'], ['id' => 2, 'returnId' => 'RET-2025-002', 'invoiceNumber' => 'INV-2025-002', 'toolName' => 'Drill Machine', 'customerName' => 'Jane Smith', 'returnDate' => '2025-01-12', 'condition' => 'Damaged', 'status' => 'Completed'], ['id' => 3, 'returnId' => 'RET-2025-003', 'invoiceNumber' => 'INV-2025-003', 'toolName' => 'Safety Helmet', 'customerName' => 'Bob Johnson', 'returnDate' => '2025-01-20', 'condition' => 'Good', 'status' => 'Pending'], ['id' => 4, 'returnId' => 'RET-2025-004', 'invoiceNumber' => 'INV-2025-002', 'toolName' => 'Safety Helmet', 'customerName' => 'Jane Smith', 'returnDate' => '2025-01-13', 'condition' => 'Good', 'status' => 'Completed']]);
    }

    private function getRentals()
    {
        return session('rentals', [
            [
                'id' => 1,
                'invoiceNumber' => 'INV-2025-001',
                'customerId' => 1,
                'customerName' => 'John Doe',
                'status' => 'Active',
                'rentalStartDate' => '2025-01-10',
                'rentalEndDate' => '2025-01-17',
                'totalPrice' => 175.0,
                'items' => [['toolId' => 1, 'toolName' => 'Angle Grinder', 'quantity' => 2, 'dailyRate' => 25.0, 'subtotal' => 175.0]],
            ],
            [
                'id' => 2,
                'invoiceNumber' => 'INV-2025-002',
                'customerId' => 2,
                'customerName' => 'Jane Smith',
                'status' => 'Active',
                'rentalStartDate' => '2025-01-05',
                'rentalEndDate' => '2025-01-12',
                'totalPrice' => 210.0,
                'items' => [['toolId' => 2, 'toolName' => 'Drill Machine', 'quantity' => 1, 'dailyRate' => 20.0, 'subtotal' => 140.0], ['toolId' => 3, 'toolName' => 'Safety Helmet', 'quantity' => 2, 'dailyRate' => 5.0, 'subtotal' => 70.0]],
            ],
            [
                'id' => 3,
                'invoiceNumber' => 'INV-2025-003',
                'customerId' => 3,
                'customerName' => 'Bob Johnson',
                'status' => 'Active',
                'rentalStartDate' => '2025-01-15',
                'rentalEndDate' => '2025-01-22',
                'totalPrice' => 105.0,
                'items' => [['toolId' => 4, 'toolName' => 'Hammer', 'quantity' => 3, 'dailyRate' => 5.0, 'subtotal' => 105.0]],
            ],
        ]);
    }

    public function returnsTools(Request $request)
    {
        $customers = Customers::all();
        $allReturns = Rentals::whereNotNull('return_invoice_number')->get();
        $rentals = Rentals::with('customer')->whereNotNull('return_invoice_number')->where('rental_status', 'Returning')->get();

        $allMovementIds = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovementIds = array_merge($allMovementIds, $ids);
        }

        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovementIds))->get()->keyBy('id');

        $totalReturn = $allReturns->count();
        $activeRentals = $allReturns->where('rental_status', 'Pending')->count();
        $completedRentals = $allReturns->where('payment_status', 'paid')->count();
        $totalRevenue = $allReturns->sum('total_price');

        $customersById = [];
        foreach ($customers as $c) {
            $customersById[$c->id] = $c;
        }

        $movementsByRentalId = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $movementsByRentalId[$rental->id] = collect($ids)->map(fn($id) => $movements->get($id))->filter()->values();
        }

        return view('returns.returns', compact('rentals', 'customersById', 'movementsByRentalId', 'totalReturn', 'activeRentals', 'completedRentals', 'totalRevenue'));
    }

    public function returnsFrom()
    {
        $rentals = Rentals::with('customer')
            ->whereNull('return_invoice_number')
            ->whereNotIn('rental_status', ['Returning'])
            ->get();

        // Resolve movements & tools
        $allMovIds = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovIds = array_merge($allMovIds, $ids);
        }

        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovIds))->get()->keyBy('id');

        // Buat movementsByRentalId
        $movementsByRentalId = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $movementsByRentalId[$rental->id] = collect($ids)->map(fn($id) => $movements->get($id))->filter()->values();
        }

        return view('returns.returnForm', compact('rentals', 'movementsByRentalId'));
    }

    // ─── Store ───────────────────────────────────────────────
    public function returnStore(Request $request)
    {
        $rental = Rentals::findOrFail($request->rentalId);
        $auditItems = json_decode($request->auditItems, true);

        if (empty($auditItems)) {
            return back()->withErrors(['auditItems' => 'No audit data found.']);
        }

        // Generate return invoice number
        $returnInvoice = 'RET-' . strtoupper(substr($rental->invoice_number, 4)) . '-' . now()->format('Ymd');
        while (Rentals::where('return_invoice_number', $returnInvoice)->exists()) {
            $returnInvoice = 'RET-' . strtoupper(substr($rental->invoice_number, 4)) . '-' . now()->format('YmdHis');
        }

        // Ambil warehouse_id dari rental (ambil pertama)
        $warehouseIds = json_decode($rental->warehouse_id, true) ?? [];
        $warehouseId = $warehouseIds[0] ?? 1;

        // Map stock_type per kondisi
        $stockTypeMap = [
            'good' => 'RETURN',
            'damaged' => 'DAMAGED',
            'lost' => 'LOST',
            'sold' => 'SOLD',
        ];

        $returnMovementIds = [];
        foreach ($auditItems as $item) {
            $toolId = $item['toolId'];
            foreach ($stockTypeMap as $condition => $stockType) {
                $qty = intval($item[$condition] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $movId = (string) Str::uuid();

                $stockMOvement = new StockMovement();
                $stockMOvement->id = $movId;
                $stockMOvement->reference_id = $returnInvoice;
                $stockMOvement->warehouse_id = $warehouseId;
                $stockMOvement->tool_id = $toolId;
                $stockMOvement->movement_type = 'Waiting';
                $stockMOvement->stock_type = $stockType;
                $stockMOvement->quantity = $qty;
                $stockMOvement->notes = "Return from {$rental->invoice_number} — condition: {$condition}";
                $stockMOvement->created_by = auth()->id();

                $stockMOvement->save();

                $returnMovementIds[] = $movId;
            }
        }

        $rental->return_invoice_number = $returnInvoice;
        $rental->return_movement_id = json_encode($returnMovementIds);
        $rental->rental_status = 'Returning';

        $rental->save();

        return redirect()
            ->route('returns.tools')
            ->with('success', "Return {$returnInvoice} berhasil diproses.");
    }
}
