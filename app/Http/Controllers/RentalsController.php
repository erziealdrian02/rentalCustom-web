<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Stock;
use App\Models\Tools;
use Illuminate\Http\Request;

class RentalsController extends Controller
{
    private function getCustomers(): array
    {
        return [['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '+62 812-0001-0001'], ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '+62 812-0002-0002'], ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'phone' => '+62 812-0003-0003'], ['id' => 4, 'name' => 'Alice Brown', 'email' => 'alice@example.com', 'phone' => '+62 812-0004-0004']];
    }

    /**
     * Data dummy rentals
     */
    private function getRentals(): array
    {
        return [
            [
                'id' => 1,
                'invoiceNumber' => 'INV-2024-001',
                'customerId' => 1,
                'customerName' => 'John Doe',
                'status' => 'Active',
                'rentalStartDate' => '2024-01-01',
                'rentalEndDate' => '2024-01-08',
                'totalPrice' => 350000,
                'createdDate' => '2024-01-01',
                'items' => [
                    [
                        'toolId' => 1,
                        'toolName' => 'Hammer Drill',
                        'quantity' => 1,
                        'startDate' => '2024-01-01',
                        'endDate' => '2024-01-08',
                        'dailyRate' => 50000,
                        'subtotal' => 350000,
                    ],
                ],
            ],
            [
                'id' => 2,
                'invoiceNumber' => 'INV-2024-002',
                'customerId' => 2,
                'customerName' => 'Jane Smith',
                'status' => 'Completed',
                'rentalStartDate' => '2024-01-05',
                'rentalEndDate' => '2024-01-12',
                'totalPrice' => 560000,
                'createdDate' => '2024-01-05',
                'items' => [
                    [
                        'toolId' => 2,
                        'toolName' => 'Angle Grinder',
                        'quantity' => 1,
                        'startDate' => '2024-01-05',
                        'endDate' => '2024-01-12',
                        'dailyRate' => 40000,
                        'subtotal' => 280000,
                    ],
                    [
                        'toolId' => 3,
                        'toolName' => 'Circular Saw',
                        'quantity' => 1,
                        'startDate' => '2024-01-05',
                        'endDate' => '2024-01-12',
                        'dailyRate' => 40000,
                        'subtotal' => 280000,
                    ],
                ],
            ],
            [
                'id' => 3,
                'invoiceNumber' => 'INV-2024-003',
                'customerId' => 3,
                'customerName' => 'Bob Johnson',
                'status' => 'Active',
                'rentalStartDate' => '2024-01-10',
                'rentalEndDate' => '2024-01-17',
                'totalPrice' => 420000,
                'createdDate' => '2024-01-10',
                'items' => [
                    [
                        'toolId' => 4,
                        'toolName' => 'Pressure Washer',
                        'quantity' => 1,
                        'startDate' => '2024-01-10',
                        'endDate' => '2024-01-17',
                        'dailyRate' => 60000,
                        'subtotal' => 420000,
                    ],
                ],
            ],
            [
                'id' => 4,
                'invoiceNumber' => 'INV-2024-004',
                'customerId' => 4,
                'customerName' => 'Alice Brown',
                'status' => 'Completed',
                'rentalStartDate' => '2024-01-15',
                'rentalEndDate' => '2024-01-22',
                'totalPrice' => 700000,
                'createdDate' => '2024-01-15',
                'items' => [
                    [
                        'toolId' => 5,
                        'toolName' => 'Concrete Mixer',
                        'quantity' => 1,
                        'startDate' => '2024-01-15',
                        'endDate' => '2024-01-22',
                        'dailyRate' => 100000,
                        'subtotal' => 700000,
                    ],
                ],
            ],
        ];
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
        $customers = $this->getCustomers();
        $rentals = $this->getRentals();

        // Hitung summary
        $totalRentals = count($rentals);
        $activeRentals = count(array_filter($rentals, fn($r) => $r['status'] === 'Active'));
        $completedRentals = count(array_filter($rentals, fn($r) => $r['status'] === 'Completed'));
        $totalRevenue = array_sum(array_column($rentals, 'totalPrice'));

        // Buat lookup customers by id untuk modal
        $customersById = [];
        foreach ($customers as $c) {
            $customersById[$c['id']] = $c;
        }

        return view('rentals.rentals', compact('rentals', 'customersById', 'totalRentals', 'activeRentals', 'completedRentals', 'totalRevenue'));
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
        $getCustomers = Customers::get();

        // Ambil tools yang punya available_quantity > 0 di warehouse_stock
        $getTools = Tools::whereHas('stocks', function ($query) {
            $query->where('quantity', '>', 0);
        })->get();

        // dd($getTools);

        $getStock = Stock::get();

        $customers = count(
            array_filter($getCustomers->toArray(), function ($r) {
                return isset($r['status']) && $r['status'] === 'active';
            }),
        );
        $tools = count(
            array_filter($getTools->toArray(), function ($r) {
                return isset($r['status']) && $r['status'] === 'available';
            }),
        );

        $pricingMap = [];
        foreach ($getTools as $tool) {
            $pricingMap[$tool['id_tools']] = [
                'dailyRate' => $tool['daily_rate'],
                'weeklyRate' => $tool['weekly_rate'],
                'monthlyRate' => $tool['monthly_rate'],
            ];
            // dd($pricingMap);
        }

        return view('rentals.createRental', compact('customers', 'getCustomers', 'getTools', 'tools', 'pricingMap'));
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
