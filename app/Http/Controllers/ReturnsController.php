<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Rentals;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    // public function returnsTools()
    // {
    //     $returns = $this->getReturns();
    //     $total = count($returns);
    //     $completed = count(array_filter($returns, fn($r) => $r['status'] === 'Completed'));
    //     $pending = count(array_filter($returns, fn($r) => $r['status'] === 'Pending'));

    //     return view('returns.returns', compact('returns', 'total', 'completed', 'pending'));
    // }

    public function returnsTools(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $customers = Customers::all();
        $allRentals = Rentals::all();
        $rentals = Rentals::with('customer')->whereNull('return_invoice_number')->where('rental_status', 'Returning')->paginate($perPage);

        $allMovementIds = [];
        foreach ($rentals->items() as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovementIds = array_merge($allMovementIds, $ids);
        }

        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovementIds))->get()->keyBy('id');

        $totalRentals = $allRentals->count();
        $activeRentals = $allRentals->where('rental_status', 'Pending')->count();
        $completedRentals = $allRentals->where('payment_status', 'paid')->count();
        $totalRevenue = $allRentals->sum('total_price');

        // Buat lookup customers by id untuk modal
        $customersById = [];
        foreach ($customers as $c) {
            $customersById[$c->id] = $c;
        }

        // Buat lookup movements by rental id untuk modal
        $movementsByRentalId = [];
        foreach ($rentals->items() as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $movementsByRentalId[$rental->id] = collect($ids)->map(fn($id) => $movements->get($id))->filter()->values();
        }

        return view('returns.returns', compact('rentals', 'customersById', 'movementsByRentalId', 'totalRentals', 'activeRentals', 'completedRentals', 'totalRevenue'));
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
    public function store(Request $request)
    {
        $request->validate([
            'rentalId' => 'required|integer',
            'auditItems' => 'required|string',
        ]);

        $rentals = $this->getRentals();
        $rental = collect($rentals)->firstWhere('id', (int) $request->rentalId);

        if (!$rental) {
            return back()
                ->withErrors(['rentalId' => 'Rental not found.'])
                ->withInput();
        }

        $auditItems = json_decode($request->auditItems, true);
        if (empty($auditItems)) {
            return back()
                ->withErrors(['auditItems' => 'Audit data is missing.'])
                ->withInput();
        }

        // Hitung summary finansial
        $totalGood = 0;
        $totalDamageLoss = 0;
        $totalLostLoss = 0;
        $totalSoldRevenue = 0;

        foreach ($auditItems as $item) {
            $totalGood += $item['good'] * $item['originalRate'];
            $totalDamageLoss += $item['damaged'] * $item['originalRate'];
            $totalLostLoss += $item['lost'] * $item['originalRate'];
            $totalSoldRevenue += $item['sold'] * ($item['originalRate'] * 0.5);
        }

        $totalLoss = $totalDamageLoss + $totalLostLoss;
        $netRevenue = $totalGood + $totalSoldRevenue;

        $returns = $this->getReturns();
        $maxId = count($returns) ? max(array_column($returns, 'id')) : 0;
        $returnId = 'RET-' . now()->year . '-' . str_pad(count($returns) + 1, 3, '0', STR_PAD_LEFT);

        $firstItem = $rental['items'][0] ?? [];

        $returns[] = [
            'id' => $maxId + 1,
            'returnId' => $returnId,
            'rentalId' => (int) $request->rentalId,
            'invoiceNumber' => $rental['invoiceNumber'],
            'customerId' => $rental['customerId'],
            'customerName' => $rental['customerName'],
            'toolName' => count($rental['items']) > 1 ? count($rental['items']) . ' tools' : $firstItem['toolName'] ?? '-',
            'returnDate' => now()->toDateString(),
            'originalRentalDate' => $rental['rentalStartDate'],
            'requestedReturnDate' => $rental['rentalEndDate'],
            'status' => 'Completed',
            'condition' => $totalDamageLoss > 0 || $totalLostLoss > 0 ? 'Damaged' : 'Good',
            'originalRevenue' => $rental['totalPrice'],
            'totalAuditRevenueLoss' => $totalLoss,
            'netRevenue' => $netRevenue,
            'auditDate' => now()->toDateString(),
            'items' => array_map(
                fn($item) => [
                    'toolId' => $item['toolId'],
                    'toolName' => $item['toolName'],
                    'quantity' => $item['quantity'],
                    'originalRate' => $item['originalRate'],
                    'condition' => $item['damaged'] > 0 ? 'Damaged' : 'Good',
                    'auditDetails' => [
                        'good' => $item['good'],
                        'damaged' => $item['damaged'],
                        'lost' => $item['lost'],
                        'sold' => $item['sold'],
                    ],
                ],
                array_values($auditItems),
            ),
        ];

        session(['returns' => $returns]);

        return redirect()
            ->route('returns.index')
            ->with('success', "Return processed successfully! ID: {$returnId}");
    }
}
