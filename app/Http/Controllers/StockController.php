<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Tools;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function overview(Request $request)
    {
        $warehouses = Warehouse::all();
        $warehouseFilter = $request->get('warehouse', '');

        // Query dengan eager loading 3 tabel sekaligus
        $query = Stock::with(['warehouse', 'tool']);

        if ($warehouseFilter) {
            $query->whereHas('warehouse', fn($q) => $q->where('name', $warehouseFilter));
        }

        $stockData = $query->get();

        // Map ke format yang dipakai view
        $filteredStock = $stockData
            ->map(
                fn($s) => [
                    'toolName' => $s->tool->name ?? '-',
                    'warehouseName' => $s->warehouse->name ?? '-',
                    'quantity' => $s->quantity,
                    'status' => ucfirst($s->status),
                ],
            )
            ->toArray();

        $totalItems = $stockData->sum('quantity');
        $totalTools = $stockData->unique('tool_id')->count();
        $lowStockCount = $stockData->filter(fn($s) => $s->quantity < 3)->count();

        return view('stock.stock-overview', compact('warehouses', 'filteredStock', 'totalItems', 'totalTools', 'lowStockCount', 'warehouseFilter'));
    }

    public function restockForm()
    {
        $warehouses = Warehouse::all();
        $tools = Tools::all();

        return view('stock.createReStock', compact('warehouses', 'tools'));
    }

    public function movement(Request $request)
    {
        $movements = $this->getMovements();
        $typeFilter = $request->get('type', '');

        $filtered = $typeFilter ? array_values(array_filter($movements, fn($m) => $m['type'] === $typeFilter)) : $movements;

        $types = ['IN', 'OUT', 'RENT', 'RETURN', 'LOST', 'DAMAGED', 'RESTOCK', 'AUDIT', 'ADJUSTMENT'];

        return view('stock.stock-movement', compact('filtered', 'typeFilter', 'types'));
    }
}
