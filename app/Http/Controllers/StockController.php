<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private function getWarehouses()
    {
        return session('warehouses', [['id' => 1, 'name' => 'Warehouse Alpha', 'location' => 'Jakarta', 'capacity' => 500, 'currentStock' => 320], ['id' => 2, 'name' => 'Warehouse Beta', 'location' => 'Jakarta', 'capacity' => 300, 'currentStock' => 210], ['id' => 3, 'name' => 'Warehouse Gamma', 'location' => 'Surabaya', 'capacity' => 400, 'currentStock' => 180], ['id' => 4, 'name' => 'Warehouse Delta', 'location' => 'Surabaya', 'capacity' => 250, 'currentStock' => 230], ['id' => 5, 'name' => 'Warehouse Epsilon', 'location' => 'Bandung', 'capacity' => 350, 'currentStock' => 90]]);
    }

    private function getStockOverview()
    {
        return session('stock_overview', [['id' => 1, 'toolName' => 'Angle Grinder', 'warehouseName' => 'Warehouse Alpha', 'quantity' => 5, 'status' => 'Good'], ['id' => 2, 'toolName' => 'Hammer', 'warehouseName' => 'Warehouse Alpha', 'quantity' => 12, 'status' => 'Good'], ['id' => 3, 'toolName' => 'Safety Helmet', 'warehouseName' => 'Warehouse Beta', 'quantity' => 2, 'status' => 'Low'], ['id' => 4, 'toolName' => 'Drill Machine', 'warehouseName' => 'Warehouse Beta', 'quantity' => 7, 'status' => 'Good'], ['id' => 5, 'toolName' => 'Screwdriver', 'warehouseName' => 'Warehouse Gamma', 'quantity' => 1, 'status' => 'Low'], ['id' => 6, 'toolName' => 'Wrench Set', 'warehouseName' => 'Warehouse Gamma', 'quantity' => 8, 'status' => 'Good'], ['id' => 7, 'toolName' => 'Measuring Tape', 'warehouseName' => 'Warehouse Delta', 'quantity' => 15, 'status' => 'Good'], ['id' => 8, 'toolName' => 'Ladder', 'warehouseName' => 'Warehouse Epsilon', 'quantity' => 2, 'status' => 'Low']]);
    }

    private function getMovements()
    {
        return session('stock_movement', [
            ['id' => 1, 'date' => '2025-01-15', 'toolName' => 'Angle Grinder', 'warehouseName' => 'Warehouse Alpha', 'type' => 'IN', 'quantity' => 10, 'reference' => 'PO-001'],
            ['id' => 2, 'date' => '2025-01-16', 'toolName' => 'Hammer', 'warehouseName' => 'Warehouse Alpha', 'type' => 'IN', 'quantity' => 20, 'reference' => 'PO-002'],
            ['id' => 3, 'date' => '2025-01-17', 'toolName' => 'Safety Helmet', 'warehouseName' => 'Warehouse Beta', 'type' => 'RENT', 'quantity' => 3, 'reference' => 'RNT-001'],
            ['id' => 4, 'date' => '2025-01-18', 'toolName' => 'Drill Machine', 'warehouseName' => 'Warehouse Beta', 'type' => 'OUT', 'quantity' => 2, 'reference' => 'OUT-001'],
            ['id' => 5, 'date' => '2025-01-19', 'toolName' => 'Screwdriver', 'warehouseName' => 'Warehouse Gamma', 'type' => 'RETURN', 'quantity' => 5, 'reference' => 'RET-001'],
            ['id' => 6, 'date' => '2025-01-20', 'toolName' => 'Wrench Set', 'warehouseName' => 'Warehouse Gamma', 'type' => 'DAMAGED', 'quantity' => 1, 'reference' => 'DMG-001'],
            ['id' => 7, 'date' => '2025-01-21', 'toolName' => 'Measuring Tape', 'warehouseName' => 'Warehouse Delta', 'type' => 'IN', 'quantity' => 8, 'reference' => 'PO-003'],
            ['id' => 8, 'date' => '2025-01-22', 'toolName' => 'Ladder', 'warehouseName' => 'Warehouse Epsilon', 'type' => 'LOST', 'quantity' => 1, 'reference' => 'LST-001'],
            ['id' => 9, 'date' => '2025-01-23', 'toolName' => 'Angle Grinder', 'warehouseName' => 'Warehouse Alpha', 'type' => 'RENT', 'quantity' => 2, 'reference' => 'RNT-002'],
            ['id' => 10, 'date' => '2025-01-24', 'toolName' => 'Hammer', 'warehouseName' => 'Warehouse Alpha', 'type' => 'RETURN', 'quantity' => 4, 'reference' => 'RET-002'],
        ]);
    }

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

    public function movement(Request $request)
    {
        $movements = $this->getMovements();
        $typeFilter = $request->get('type', '');

        $filtered = $typeFilter ? array_values(array_filter($movements, fn($m) => $m['type'] === $typeFilter)) : $movements;

        $types = ['IN', 'OUT', 'RENT', 'RETURN', 'LOST', 'DAMAGED', 'RESTOCK', 'AUDIT', 'ADJUSTMENT'];

        return view('stock.stock-movement', compact('filtered', 'typeFilter', 'types'));
    }
}
