<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\Rentals;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tools;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Pest\ArchPresets\Custom;

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

    public function rental(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $customers = Customers::all();
        $allRentals = Rentals::all();
        $rentals = Rentals::with('customer')->paginate($perPage);

        // Kumpulkan semua movement_id dari semua rental
        $allMovementIds = [];
        foreach ($rentals->items() as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovementIds = array_merge($allMovementIds, $ids);
        }

        // Ambil semua stock_movements yang relevan sekaligus (with tool)
        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovementIds))->get()->keyBy('id');

        // Hitung summary
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

        return view('rentals.rentals', compact('rentals', 'customersById', 'movementsByRentalId', 'totalRentals', 'activeRentals', 'completedRentals', 'totalRevenue'));
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

    public function rentalStore(Request $request)
    {
        $request->validate([
            'customerId' => 'required|exists:customers,id',
            'items' => 'required|string',
        ]);

        $items = json_decode($request->items, true);

        // dd($request->all(), $items);

        $customer = Customers::findOrFail($request->customerId);
        // $customer = collect($customers)->firstWhere('id', (int) $request->customerId);

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

        foreach ($items as $item) {
            $findWarehouse = Stock::where('tool_id', $item['toolId'])->orderByDesc('quantity')->value('warehouse_id');
            $warehouse = Warehouse::findOrFail($findWarehouse);

            // 🔤 Name Code
            $nameWords = explode(' ', $customer->name);
            $nameCode = '';
            foreach ($nameWords as $word) {
                $nameCode .= strtoupper(substr($word, 0, 1));
            }
            // 🔤 City Code
            $cityWords = explode(' ', $customer->city);
            $cityCode = '';
            foreach ($cityWords as $word) {
                $cityCode .= strtoupper(substr($word, 0, 1));
            }
            if (strlen($cityCode) < 3) {
                $cityCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $customer->city), 0, 3));
            }
            // 🔢 Count: berapa stock movement IN yang sudah pernah masuk ke customer ini
            $count = StockMovement::where('warehouse_id', $warehouse->id)->where('stock_type', 'RENT')->count();
            $number = str_pad($count + 1, 5, '0', STR_PAD_LEFT);

            // 🏷️ Final reference_id
            $referenceId = "RENT-{$nameCode}-{$cityCode}-{$number}";

            $movement = new StockMovement();
            $movement->id = (string) Str::uuid();
            $movement->reference_id = $referenceId;
            $movement->warehouse_id = $warehouse->id;
            $movement->tool_id = $item['toolId'];
            $movement->movement_type = 'WAITING';
            $movement->stock_type = 'RENT';
            $movement->quantity = $item['quantity'];
            $movement->notes = $request->notes;
            $movement->created_by = auth()->id();

            $movementIds[] = $movement->id;
            $warehouseIds[] = $warehouse->id;

            $stock = Stock::where('tool_id', $item['toolId'])->where('warehouse_id', $findWarehouse)->firstOrFail();
            $stock->quantity -= $item['quantity'];

            $movement->save();
            $stock->save();
        }

        // dd($items);

        $rental = new Rentals();
        $rental->id = (string) Str::uuid();
        $rental->invoice_number = $invoiceNum;
        $rental->customer_id = $request->customerId;
        $rental->warehouse_id = json_encode(array_unique($warehouseIds));
        $rental->movement_id = json_encode($movementIds);
        $rental->rental_start_date = $item['startDate'];
        $rental->rental_end_date = $item['endDate'];
        $rental->estimated_delivery_time = Carbon::parse($item['startDate'])->addDay();
        $rental->total_price = $totalPrice;
        $rental->rental_status = 'pending';
        $rental->payment_status = 'unpaid';
        // $rental->notes = $request->notes;
        $rental->created_by = auth()->id();

        $rental->save();

        return redirect()
            ->route('transactions.rentals')
            ->with('success', "Rental created! Invoice: {$invoiceNum}");
    }
}
