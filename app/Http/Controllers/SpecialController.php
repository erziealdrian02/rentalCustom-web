<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class SpecialController extends Controller
{
    private function getLostTools()
    {
        return session('lostTools', [['id' => 1, 'toolCode' => 'TL-001', 'toolName' => 'Angle Grinder', 'serialNumber' => 'SN-AG-001', 'replacementValue' => 250.0, 'lostDate' => '2025-01-15', 'invoiceNumber' => 'INV-2025-001', 'customerName' => 'John Doe'], ['id' => 2, 'toolCode' => 'TL-003', 'toolName' => 'Safety Helmet', 'serialNumber' => 'SN-SH-003', 'replacementValue' => 80.0, 'lostDate' => '2025-01-18', 'invoiceNumber' => 'INV-2025-002', 'customerName' => 'Jane Smith'], ['id' => 3, 'toolCode' => 'TL-007', 'toolName' => 'Wrench Set', 'serialNumber' => 'SN-WR-007', 'replacementValue' => 120.0, 'lostDate' => '2025-01-22', 'invoiceNumber' => 'INV-2025-003', 'customerName' => 'Bob Johnson']]);
    }

    private function getSoldTools()
    {
        return session('soldTools', [['id' => 1, 'toolCode' => 'TL-002', 'toolName' => 'Hammer', 'serialNumber' => 'SN-HM-002', 'replacementValue' => 30.0, 'soldPrice' => 15.0, 'soldDate' => '2025-01-12'], ['id' => 2, 'toolCode' => 'TL-005', 'toolName' => 'Screwdriver', 'serialNumber' => 'SN-SD-005', 'replacementValue' => 20.0, 'soldPrice' => 10.0, 'soldDate' => '2025-01-14'], ['id' => 3, 'toolCode' => 'TL-008', 'toolName' => 'Measuring Tape', 'serialNumber' => 'SN-MT-008', 'replacementValue' => 15.0, 'soldPrice' => 8.0, 'soldDate' => '2025-01-19'], ['id' => 4, 'toolCode' => 'TL-010', 'toolName' => 'Drill Bit Set', 'serialNumber' => 'SN-DB-010', 'replacementValue' => 45.0, 'soldPrice' => 30.0, 'soldDate' => '2025-01-21']]);
    }

    public function lostTools(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;
        $lostTools = StockMovement::where('stock_type', 'LOST')->paginate($perPage);

        return view('special.lostTools', compact('lostTools'));
    }

    public function soldTools(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;
        $soldTools = StockMovement::where('stock_type', 'SOLD')->get();
        $totalSold = count($soldTools);
        // dd($soldTools);

        $totalRevenue = $soldTools->sum(function ($item) {
            return ($item->tool->replacement_value ?? 0) * $item->quantity;
        });

        return view('special.soldTools', compact('soldTools', 'totalSold', 'totalRevenue'));
    }
}
