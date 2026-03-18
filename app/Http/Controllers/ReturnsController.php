<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
                'id' => 1, 'invoiceNumber' => 'INV-2025-001', 'customerId' => 1,
                'customerName' => 'John Doe', 'status' => 'Active',
                'rentalStartDate' => '2025-01-10', 'rentalEndDate' => '2025-01-17', 'totalPrice' => 175.00,
                'items' => [
                    ['toolId' => 1, 'toolName' => 'Angle Grinder', 'quantity' => 2, 'dailyRate' => 25.00, 'subtotal' => 175.00],
                ],
            ],
            [
                'id' => 2, 'invoiceNumber' => 'INV-2025-002', 'customerId' => 2,
                'customerName' => 'Jane Smith', 'status' => 'Active',
                'rentalStartDate' => '2025-01-05', 'rentalEndDate' => '2025-01-12', 'totalPrice' => 210.00,
                'items' => [
                    ['toolId' => 2, 'toolName' => 'Drill Machine', 'quantity' => 1, 'dailyRate' => 20.00, 'subtotal' => 140.00],
                    ['toolId' => 3, 'toolName' => 'Safety Helmet', 'quantity' => 2, 'dailyRate' => 5.00,  'subtotal' => 70.00],
                ],
            ],
            [
                'id' => 3, 'invoiceNumber' => 'INV-2025-003', 'customerId' => 3,
                'customerName' => 'Bob Johnson', 'status' => 'Active',
                'rentalStartDate' => '2025-01-15', 'rentalEndDate' => '2025-01-22', 'totalPrice' => 105.00,
                'items' => [
                    ['toolId' => 4, 'toolName' => 'Hammer', 'quantity' => 3, 'dailyRate' => 5.00, 'subtotal' => 105.00],
                ],
            ],
        ]);
    }

    public function returnsTools()
    {
        $returns = $this->getReturns();
        $total = count($returns);
        $completed = count(array_filter($returns, fn($r) => $r['status'] === 'Completed'));
        $pending = count(array_filter($returns, fn($r) => $r['status'] === 'Pending'));

        return view('returns.returns', compact('returns', 'total', 'completed', 'pending'));
    }

    public function returnsFrom()
    {
        $rentals = $this->getRentals();
        return view('returns.returnForm', compact('rentals'));
    }
 
    // ─── Store ───────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'rentalId'   => 'required|integer',
            'auditItems' => 'required|string',
        ]);
 
        $rentals = $this->getRentals();
        $rental  = collect($rentals)->firstWhere('id', (int) $request->rentalId);
 
        if (!$rental) {
            return back()->withErrors(['rentalId' => 'Rental not found.'])->withInput();
        }
 
        $auditItems = json_decode($request->auditItems, true);
        if (empty($auditItems)) {
            return back()->withErrors(['auditItems' => 'Audit data is missing.'])->withInput();
        }
 
        // Hitung summary finansial
        $totalGood        = 0;
        $totalDamageLoss  = 0;
        $totalLostLoss    = 0;
        $totalSoldRevenue = 0;
 
        foreach ($auditItems as $item) {
            $totalGood        += $item['good']    * $item['originalRate'];
            $totalDamageLoss  += $item['damaged'] * $item['originalRate'];
            $totalLostLoss    += $item['lost']    * $item['originalRate'];
            $totalSoldRevenue += $item['sold']    * ($item['originalRate'] * 0.5);
        }
 
        $totalLoss  = $totalDamageLoss + $totalLostLoss;
        $netRevenue = $totalGood + $totalSoldRevenue;
 
        $returns  = $this->getReturns();
        $maxId    = count($returns) ? max(array_column($returns, 'id')) : 0;
        $returnId = 'RET-' . now()->year . '-' . str_pad(count($returns) + 1, 3, '0', STR_PAD_LEFT);
 
        $firstItem = $rental['items'][0] ?? [];
 
        $returns[] = [
            'id'                    => $maxId + 1,
            'returnId'              => $returnId,
            'rentalId'              => (int) $request->rentalId,
            'invoiceNumber'         => $rental['invoiceNumber'],
            'customerId'            => $rental['customerId'],
            'customerName'          => $rental['customerName'],
            'toolName'              => count($rental['items']) > 1
                                        ? count($rental['items']) . ' tools'
                                        : ($firstItem['toolName'] ?? '-'),
            'returnDate'            => now()->toDateString(),
            'originalRentalDate'    => $rental['rentalStartDate'],
            'requestedReturnDate'   => $rental['rentalEndDate'],
            'status'                => 'Completed',
            'condition'             => ($totalDamageLoss > 0 || $totalLostLoss > 0) ? 'Damaged' : 'Good',
            'originalRevenue'       => $rental['totalPrice'],
            'totalAuditRevenueLoss' => $totalLoss,
            'netRevenue'            => $netRevenue,
            'auditDate'             => now()->toDateString(),
            'items'                 => array_map(fn($item) => [
                'toolId'       => $item['toolId'],
                'toolName'     => $item['toolName'],
                'quantity'     => $item['quantity'],
                'originalRate' => $item['originalRate'],
                'condition'    => $item['damaged'] > 0 ? 'Damaged' : 'Good',
                'auditDetails' => [
                    'good'    => $item['good'],
                    'damaged' => $item['damaged'],
                    'lost'    => $item['lost'],
                    'sold'    => $item['sold'],
                ],
            ], array_values($auditItems)),
        ];
 
        session(['returns' => $returns]);
 
        return redirect()->route('returns.index')
            ->with('success', "Return processed successfully! ID: {$returnId}");
    }
}
