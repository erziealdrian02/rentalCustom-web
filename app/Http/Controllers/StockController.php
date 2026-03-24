<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Tools;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockController extends Controller
{
    public function overview(Request $request)
    {
        $warehouses = Warehouse::all();
        $warehouseFilter = $request->get('warehouse', '');

        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $query = Stock::with(['warehouse', 'tool']);

        if ($warehouseFilter) {
            $query->whereHas('warehouse', fn($q) => $q->where('name', $warehouseFilter));
        }

        // ✅ paginate langsung dari query
        $stockData = $query->paginate($perPage);

        // ✅ transform tanpa merusak pagination
        $filteredStock = $stockData->through(
            fn($s) => [
                'toolName' => $s->tool->name ?? '-',
                'warehouseName' => $s->warehouse->name ?? '-',
                'quantity' => $s->quantity,
                'status' => ucfirst($s->status),
            ],
        );

        // ⚠️ aggregate harus dari query terpisah (jangan dari paginate)
        $allData = (clone $query)->get();

        $pendingItems = $allData->where('status', 'pending')->sum('quantity');
        $totalItems = $allData->where('status', '!=', 'pending')->sum('quantity');
        $totalTools = $allData->unique('tool_id')->count();
        $lowStockCount = $allData->where('quantity', '<', 3)->count();

        return view('stock.stock-overview', compact('warehouses', 'filteredStock', 'totalItems', 'pendingItems', 'totalTools', 'lowStockCount', 'warehouseFilter'));
    }

    public function restockForm()
    {
        $warehouses = Warehouse::all();
        $tools = Tools::all();

        return view('stock.createReStock', compact('warehouses', 'tools'));
    }

    public function restockStore(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        // Decode items dari JSON
        $items = json_decode($request->items, true);

        if (empty($items)) {
            return back()->withErrors(['items' => 'No items provided.']);
        }

        // Ambil data warehouse untuk generate reference_id
        $warehouse = Warehouse::findOrFail($request->warehouse_id);

        // 🔤 Region Code
        $regionWords = explode(' ', $warehouse->region);
        $regionCode = '';
        foreach ($regionWords as $word) {
            $regionCode .= strtoupper(substr($word, 0, 1));
        }

        // 🔤 Location Code
        $locationWords = explode(' ', $warehouse->location);
        $locationCode = '';
        foreach ($locationWords as $word) {
            $locationCode .= strtoupper(substr($word, 0, 1));
        }
        if (strlen($locationCode) < 3) {
            $locationCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $warehouse->location), 0, 3));
        }

        // 🔢 Count: berapa stock movement IN yang sudah pernah masuk ke warehouse ini
        $count = StockMovement::where('warehouse_id', $request->warehouse_id)->where('stock_type', 'IN')->count();

        $number = str_pad($count + 1, 5, '0', STR_PAD_LEFT);

        // 🏷️ Final reference_id
        $referenceId = "IN-{$regionCode}-{$locationCode}-{$number}";

        // Insert semua items
        foreach ($items as $item) {
            $warehouse->used_stock += $item['quantity'];
            // dd($uuid);
            $movement = new StockMovement();
            $movement->id = (string) Str::uuid();
            $movement->reference_id = $referenceId;
            $movement->warehouse_id = $request->warehouse_id;
            $movement->tool_id = $item['toolId'];
            $movement->movement_type = 'Arrived';
            $movement->stock_type = 'IN';
            $movement->quantity = $item['quantity'];
            $movement->notes = $request->notes;
            $movement->created_by = auth()->id();

            $stock = new Stock();
            $stock->id = (string) Str::uuid();
            $stock->warehouse_id = $request->warehouse_id;
            $stock->tool_id = $item['toolId'];
            $stock->quantity = $item['quantity'];
            $stock->status = 'good';

            $warehouse->save();
            $movement->save();
            $stock->save();
        }

        return redirect()
            ->route('stock.overview')
            ->with('success', "Restock berhasil disimpan dengan referensi {$referenceId}.");
    }

    public function movement(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;
        $typeFilter = $request->get('type', '');
        $query = StockMovement::query();

        if ($typeFilter) {
            $query->where('stock_type', $typeFilter);
        }

        $filtered = $query->paginate($perPage);

        $types = ['IN', 'OUT', 'RENT', 'RETURN', 'LOST', 'DAMAGED', 'RESTOCK', 'AUDIT', 'ADJUSTMENT'];

        return view('stock.stock-movement', compact('filtered', 'typeFilter', 'types'));
    }
}
