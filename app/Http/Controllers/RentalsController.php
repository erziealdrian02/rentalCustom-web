<?php

namespace App\Http\Controllers;

use App\Exports\RentalsExport;
use App\Models\Customers;
use App\Models\Rentals;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tools;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class RentalsController extends Controller
{
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

    public function uploadPaymentProof(Request $request, $rentalId)
    {
        $rental = Rentals::findOrFail($rentalId);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');

        $rental->payment_proof_image = $path;
        $rental->payment_status = 'paid';
        $rental->save();

        return response()->json(['success' => true, 'path' => Storage::url($path)]);
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

        $pricingMap = [];
        foreach ($getTools as $tool) {
            $pricingMap[$tool['id_tools']] = [
                'dailyRate' => $tool['daily_rate'],
                'weeklyRate' => $tool['weekly_rate'],
                'monthlyRate' => $tool['monthly_rate'],
            ];
        }

        return view('rentals.createRental', compact('getCustomers', 'getTools', 'pricingMap'));
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
        $rentals = Rentals::get();
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

    public function rentalExport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $fromDate = $request->input('from_date');
        $untilDate = Carbon::parse($request->input('until_date'))->addDay();
        $stat = $request->input('stat');

        // TODO: Create CashAdvancedExport class or implement export logic
        return Excel::download(new RentalsExport($startDate, $endDate, $fromDate, $untilDate, $stat), 'cash_advanced.xlsx');
    }
}
