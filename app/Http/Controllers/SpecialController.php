<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpecialController extends Controller
{
    private function getLostTools()
    {
        return session('lostTools', [
            ['id' => 1, 'toolCode' => 'TL-001', 'toolName' => 'Angle Grinder', 'serialNumber' => 'SN-AG-001', 'replacementValue' => 250.00, 'lostDate' => '2025-01-15', 'invoiceNumber' => 'INV-2025-001', 'customerName' => 'John Doe'],
            ['id' => 2, 'toolCode' => 'TL-003', 'toolName' => 'Safety Helmet', 'serialNumber' => 'SN-SH-003', 'replacementValue' => 80.00,  'lostDate' => '2025-01-18', 'invoiceNumber' => 'INV-2025-002', 'customerName' => 'Jane Smith'],
            ['id' => 3, 'toolCode' => 'TL-007', 'toolName' => 'Wrench Set',    'serialNumber' => 'SN-WR-007', 'replacementValue' => 120.00, 'lostDate' => '2025-01-22', 'invoiceNumber' => 'INV-2025-003', 'customerName' => 'Bob Johnson'],
        ]);
    }

    private function getSoldTools()
    {
        return session('soldTools', [
            ['id' => 1, 'toolCode' => 'TL-002', 'toolName' => 'Hammer',        'serialNumber' => 'SN-HM-002', 'replacementValue' => 30.00,  'soldPrice' => 15.00,  'soldDate' => '2025-01-12'],
            ['id' => 2, 'toolCode' => 'TL-005', 'toolName' => 'Screwdriver',   'serialNumber' => 'SN-SD-005', 'replacementValue' => 20.00,  'soldPrice' => 10.00,  'soldDate' => '2025-01-14'],
            ['id' => 3, 'toolCode' => 'TL-008', 'toolName' => 'Measuring Tape','serialNumber' => 'SN-MT-008', 'replacementValue' => 15.00,  'soldPrice' => 8.00,   'soldDate' => '2025-01-19'],
            ['id' => 4, 'toolCode' => 'TL-010', 'toolName' => 'Drill Bit Set', 'serialNumber' => 'SN-DB-010', 'replacementValue' => 45.00,  'soldPrice' => 30.00,  'soldDate' => '2025-01-21'],
        ]);
    }
 
    public function lostTools()
    {
        $lostTools = $this->getLostTools();
        return view('special.lostTools', compact('lostTools'));
    }

    public function soldTools()
    {
        $soldTools    = $this->getSoldTools();
        $totalSold    = count($soldTools);
        $totalRevenue = array_sum(array_column($soldTools, 'soldPrice'));
 
        return view('special.soldTools', compact('soldTools', 'totalSold', 'totalRevenue'));
    }
}
