<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RentalsController extends Controller
{
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
                'createdDate' => '2025-01-10',
                'items' => [['toolName' => 'Angle Grinder', 'quantity' => 1, 'dailyRate' => 25.0, 'subtotal' => 175.0, 'startDate' => '2025-01-10', 'endDate' => '2025-01-17']],
            ],
            [
                'id' => 2,
                'invoiceNumber' => 'INV-2025-002',
                'customerId' => 2,
                'customerName' => 'Jane Smith',
                'status' => 'Completed',
                'rentalStartDate' => '2025-01-05',
                'rentalEndDate' => '2025-01-12',
                'totalPrice' => 210.0,
                'createdDate' => '2025-01-05',
                'items' => [['toolName' => 'Drill Machine', 'quantity' => 1, 'dailyRate' => 20.0, 'subtotal' => 140.0, 'startDate' => '2025-01-05', 'endDate' => '2025-01-12'], ['toolName' => 'Safety Helmet', 'quantity' => 2, 'dailyRate' => 5.0, 'subtotal' => 70.0, 'startDate' => '2025-01-05', 'endDate' => '2025-01-12']],
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
                'createdDate' => '2025-01-15',
                'items' => [['toolName' => 'Hammer', 'quantity' => 3, 'dailyRate' => 5.0, 'subtotal' => 105.0, 'startDate' => '2025-01-15', 'endDate' => '2025-01-22']],
            ],
        ]);
    }

    private function getCustomers()
    {
        return session('customers', [['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '081234567890'], ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '082345678901'], ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'phone' => '083456789012']]);
    }

    private function getTools()
    {
        return session('tools', [['id' => 1, 'code' => 'TL-001', 'name' => 'Angle Grinder', 'status' => 'Available'], ['id' => 2, 'code' => 'TL-002', 'name' => 'Hammer', 'status' => 'Available'], ['id' => 3, 'code' => 'TL-003', 'name' => 'Safety Helmet', 'status' => 'Available'], ['id' => 4, 'code' => 'TL-004', 'name' => 'Drill Machine', 'status' => 'Available']]);
    }

    private function getPricing()
    {
        return session('pricing', [['toolId' => 1, 'dailyRate' => 25.0, 'weeklyRate' => 150.0, 'monthlyRate' => 500.0], ['toolId' => 2, 'dailyRate' => 5.0, 'weeklyRate' => 30.0, 'monthlyRate' => 100.0], ['toolId' => 3, 'dailyRate' => 3.0, 'weeklyRate' => 18.0, 'monthlyRate' => 60.0], ['toolId' => 4, 'dailyRate' => 20.0, 'weeklyRate' => 120.0, 'monthlyRate' => 400.0]]);
    }

    public function rental()
    {
        $rentals = $this->getRentals();
        $customers = $this->getCustomers();

        $totalRentals = count($rentals);
        $activeRentals = count(array_filter($rentals, function ($r) {
            return isset($r['status']) && $r['status'] === 'Active';
        }));
        $completedRentals = count(array_filter($rentals, function ($r) {
            return isset($r['status']) && $r['status'] === 'Completed';
        }));
        $totalRevenue = array_sum(array_column($rentals, 'totalPrice'));

        return view('rentals.rentals', compact('rentals', 'customers', 'totalRentals', 'activeRentals', 'completedRentals', 'totalRevenue'));
    }

    public function show($id)
    {
        $rentals = $this->getRentals();
        $customers = $this->getCustomers();

        $rental = collect($rentals)->firstWhere('id', (int) $id);
        if (!$rental) {
            abort(404);
        }

        $customer = collect($customers)->firstWhere('id', $rental['customerId']);

        return view('rentals.rental-show', compact('rental', 'customer'));
    }

    public function rentalForm()
    {
        $getCustomers = $this->getCustomers();
        $getTools = $this->getTools();

        $customers = count(array_filter($getCustomers, function ($r) {
            return isset($r['status']) && $r['status'] === 'Active';
        }));
        $tools = count(array_filter($getTools, function ($r) {
            return isset($r['status']) && $r['status'] === 'Available';
        }));

        // Buat pricing map: toolId => rates (untuk dipakai JS)
        $pricingMap = [];
        foreach ($this->getPricing() as $p) {
            $pricingMap[$p['toolId']] = [
                'dailyRate' => $p['dailyRate'],
                'weeklyRate' => $p['weeklyRate'],
                'monthlyRate' => $p['monthlyRate'],
            ];
        }

        return view('rentals.createRental', compact('customers','getCustomers','getTools', 'tools', 'pricingMap'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customerId' => 'required|integer',
            'items' => 'required|string',
        ]);

        $customers = $this->getCustomers();
        $customer = collect($customers)->firstWhere('id', (int) $request->customerId);

        if (!$customer) {
            return back()
                ->withErrors(['customerId' => 'Customer not found.'])
                ->withInput();
        }

        $items = json_decode($request->items, true);
        if (empty($items)) {
            return back()
                ->withErrors(['items' => 'Please add at least one tool.'])
                ->withInput();
        }

        $totalPrice = array_sum(array_column($items, 'subtotal'));
        $rentals = $this->getRentals();
        $maxId = count($rentals) ? max(array_column($rentals, 'id')) : 0;
        $invoiceNum = 'INV-' . now()->year . '-' . str_pad(count($rentals) + 1, 3, '0', STR_PAD_LEFT);

        // Rentang tanggal keseluruhan dari semua item
        $allStarts = array_column($items, 'startDate');
        $allEnds = array_column($items, 'endDate');
        sort($allStarts);
        rsort($allEnds);

        $rentals[] = [
            'id' => $maxId + 1,
            'invoiceNumber' => $invoiceNum,
            'customerId' => (int) $request->customerId,
            'customerName' => $customer['name'],
            'items' => $items,
            'totalPrice' => $totalPrice,
            'status' => 'Active',
            'createdDate' => now()->toDateString(),
            'rentalStartDate' => $allStarts[0],
            'rentalEndDate' => $allEnds[0],
        ];

        session(['rentals' => $rentals]);

        return redirect()
            ->route('rentals.index')
            ->with('success', "Rental created! Invoice: {$invoiceNum}");
    }

    // public function print($id)
    // {
    //     $rentals = $this->getRentals();
    //     $customers = $this->getCustomers();

    //     $rental = collect($rentals)->firstWhere('id', (int) $id);
    //     if (!$rental) {
    //         abort(404);
    //     }

    //     $customer = collect($customers)->firstWhere('id', $rental['customerId']);

    //     return view('rentals.print', compact('rental', 'customer'));
    // }
}
