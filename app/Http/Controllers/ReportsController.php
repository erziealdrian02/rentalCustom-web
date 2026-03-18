<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    private function getRentals()
    {
        return session('rentals', [
            [
                'id' => 1,
                'invoiceNumber' => 'INV-2025-001',
                'customerName' => 'John Doe',
                'toolName' => 'Angle Grinder',
                'startDate' => '2026-01-10',
                'endDate' => '2026-01-17',
                'totalPrice' => 175.0,
                'status' => 'Active',
            ],
            [
                'id' => 2,
                'invoiceNumber' => 'INV-2025-002',
                'customerName' => 'Jane Smith',
                'toolName' => 'Drill Machine',
                'startDate' => '2026-01-05',
                'endDate' => '2026-01-12',
                'totalPrice' => 210.0,
                'status' => 'Completed',
            ],
            [
                'id' => 3,
                'invoiceNumber' => 'INV-2025-003',
                'customerName' => 'Bob Johnson',
                'toolName' => 'Hammer',
                'startDate' => '2026-01-15',
                'endDate' => '2026-01-22',
                'totalPrice' => 105.0,
                'status' => 'Active',
            ],
            [
                'id' => 4,
                'invoiceNumber' => 'INV-2025-004',
                'customerName' => 'Alice Brown',
                'toolName' => 'Angle Grinder',
                'startDate' => '2026-02-01',
                'endDate' => '2026-02-08',
                'totalPrice' => 200.0,
                'status' => 'Completed',
            ],
            [
                'id' => 5,
                'invoiceNumber' => 'INV-2025-005',
                'customerName' => 'Charlie Lee',
                'toolName' => 'Safety Helmet',
                'startDate' => '2026-02-10',
                'endDate' => '2026-02-17',
                'totalPrice' => 56.0,
                'status' => 'Active',
            ],
        ]);
    }

    private function getMonthlyRevenue()
    {
        return session('monthlyRevenue', [['month' => 'Aug', 'revenue' => 1200], ['month' => 'Sep', 'revenue' => 1800], ['month' => 'Oct', 'revenue' => 1500], ['month' => 'Nov', 'revenue' => 2200], ['month' => 'Dec', 'revenue' => 2800], ['month' => 'Jan', 'revenue' => 2100], ['month' => 'Feb', 'revenue' => 3200], ['month' => 'Mar', 'revenue' => 2600]]);
    }

    private function getTools()
    {
        return session('tools', [
            ['id' => 1, 'code' => 'TL-001', 'name' => 'Angle Grinder', 'category' => 'Power Tools', 'status' => 'Available', 'replacementValue' => 250],
            ['id' => 2, 'code' => 'TL-002', 'name' => 'Hammer', 'category' => 'Hand Tools', 'status' => 'Rented', 'replacementValue' => 30],
            ['id' => 3, 'code' => 'TL-003', 'name' => 'Safety Helmet', 'category' => 'Safety Equipment', 'status' => 'Available', 'replacementValue' => 80],
            ['id' => 4, 'code' => 'TL-004', 'name' => 'Drill Machine', 'category' => 'Power Tools', 'status' => 'Rented', 'replacementValue' => 200],
            ['id' => 5, 'code' => 'TL-005', 'name' => 'Screwdriver', 'category' => 'Hand Tools', 'status' => 'Available', 'replacementValue' => 20],
            ['id' => 6, 'code' => 'TL-006', 'name' => 'Measuring Tape', 'category' => 'Measurement Tools', 'status' => 'Damaged', 'replacementValue' => 15],
            ['id' => 7, 'code' => 'TL-007', 'name' => 'Wrench Set', 'category' => 'Hand Tools', 'status' => 'Available', 'replacementValue' => 60],
            ['id' => 8, 'code' => 'TL-008', 'name' => 'Ladder', 'category' => 'Safety Equipment', 'status' => 'Lost', 'replacementValue' => 120],
        ]);
    }

    private function getWarehouses()
    {
        return session('warehouses', [['id' => 1, 'name' => 'Warehouse Alpha', 'location' => 'Jakarta', 'capacity' => 500, 'currentStock' => 320], ['id' => 2, 'name' => 'Warehouse Beta', 'location' => 'Jakarta', 'capacity' => 300, 'currentStock' => 210], ['id' => 3, 'name' => 'Warehouse Gamma', 'location' => 'Surabaya', 'capacity' => 400, 'currentStock' => 180], ['id' => 4, 'name' => 'Warehouse Delta', 'location' => 'Surabaya', 'capacity' => 250, 'currentStock' => 230], ['id' => 5, 'name' => 'Warehouse Epsilon', 'location' => 'Bandung', 'capacity' => 350, 'currentStock' => 90]]);
    }

    private function getStockOverview()
    {
        return session('stock_overview', [['toolName' => 'Angle Grinder', 'quantity' => 5], ['toolName' => 'Hammer', 'quantity' => 12], ['toolName' => 'Safety Helmet', 'quantity' => 2], ['toolName' => 'Drill Machine', 'quantity' => 7], ['toolName' => 'Screwdriver', 'quantity' => 1]]);
    }

    private function getMonthlyRentals()
    {
        return session('monthlyRentals', [['month' => 'Aug', 'rentals' => 5], ['month' => 'Sep', 'rentals' => 8], ['month' => 'Oct', 'rentals' => 6], ['month' => 'Nov', 'rentals' => 10], ['month' => 'Dec', 'rentals' => 12], ['month' => 'Jan', 'rentals' => 9]]);
    }

    // Rental Reports

    public function rentalReports(Request $request)
    {
        $rentals = $this->getRentals();
        $monthlyData = $this->getMonthlyRentals();

        // Filter by date range jika ada
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        $filtered = array_values(
            array_filter($rentals, function ($r) use ($startDate, $endDate) {
                $rDate = $r['startDate'] ?? ($r['rentalStartDate'] ?? null);
                if (!$rDate) {
                    return true;
                }
                return $rDate >= $startDate && $rDate <= $endDate;
            }),
        );

        // Summary
        $total = count($rentals);
        $active = count(array_filter($rentals, fn($r) => $r['status'] === 'Active'));
        $completed = count(array_filter($rentals, fn($r) => $r['status'] === 'Completed'));
        $avgValue = $total > 0 ? array_sum(array_column($rentals, 'totalPrice')) / $total : 0;

        // Tool popularity dari semua rentals
        $toolCounts = [];
        foreach ($rentals as $r) {
            $name = $r['toolName'] ?? 'Unknown';
            $toolCounts[$name] = ($toolCounts[$name] ?? 0) + 1;
        }

        // Kirim ke view sebagai JSON untuk Chart.js
        $monthlyLabels = array_column($monthlyData, 'month');
        $monthlyValues = array_column($monthlyData, 'rentals');
        $toolLabels = array_keys($toolCounts);
        $toolValues = array_values($toolCounts);

        return view('reports.rentalReport', compact('filtered', 'total', 'active', 'completed', 'avgValue', 'monthlyLabels', 'monthlyValues', 'toolLabels', 'toolValues', 'startDate', 'endDate'));
    }

    public function rentalReportsexport(Request $request)
    {
        $rentals = $this->getRentals();
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        // Buat CSV sederhana
        $filename = 'rental-report-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rentals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice', 'Customer', 'Tool', 'Start Date', 'End Date', 'Total Price', 'Status']);
            foreach ($rentals as $r) {
                fputcsv($handle, [$r['invoiceNumber'], $r['customerName'], $r['toolName'] ?? '-', $r['startDate'] ?? ($r['rentalStartDate'] ?? '-'), $r['endDate'] ?? ($r['rentalEndDate'] ?? '-'), $r['totalPrice'], $r['status']]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Revenue Reports

    public function revenueReports(Request $request)
    {
        $rentals = $this->getRentals();
        $monthlyData = $this->getMonthlyRevenue();

        // Summary
        $totalRevenue = array_sum(array_column($rentals, 'totalPrice'));
        $totalCount = count($rentals);
        $avgRevenue = $totalCount > 0 ? $totalRevenue / $totalCount : 0;

        // Peak month dari monthlyRevenue
        $peakMonth = 'N/A';
        if (!empty($monthlyData)) {
            $maxRevenue = max(array_column($monthlyData, 'revenue'));
            foreach ($monthlyData as $m) {
                if ($m['revenue'] === $maxRevenue) {
                    $peakMonth = $m['month'];
                    break;
                }
            }
        }

        // Revenue by Tool
        $toolRevenue = [];
        foreach ($rentals as $r) {
            $name = $r['toolName'] ?? 'Unknown';
            $toolRevenue[$name] = ($toolRevenue[$name] ?? 0) + $r['totalPrice'];
        }
        arsort($toolRevenue);

        // Revenue by Customer (sorted desc)
        $customerRevenue = [];
        foreach ($rentals as $r) {
            $name = $r['customerName'] ?? 'Unknown';
            $customerRevenue[$name] = ($customerRevenue[$name] ?? 0) + $r['totalPrice'];
        }
        arsort($customerRevenue);

        // Revenue by Status (untuk doughnut chart)
        $statusRevenue = [];
        foreach ($rentals as $r) {
            $status = $r['status'] ?? 'Unknown';
            $statusRevenue[$status] = ($statusRevenue[$status] ?? 0) + $r['totalPrice'];
        }

        // Siapkan data untuk Chart.js
        $monthlyLabels = array_column($monthlyData, 'month');
        $monthlyValues = array_column($monthlyData, 'revenue');
        $toolLabels = array_keys($toolRevenue);
        $toolValues = array_values($toolRevenue);
        $statusLabels = array_keys($statusRevenue);
        $statusValues = array_values($statusRevenue);

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        return view('reports.revenueReport', compact('totalRevenue', 'avgRevenue', 'totalCount', 'peakMonth', 'customerRevenue', 'monthlyLabels', 'monthlyValues', 'toolLabels', 'toolValues', 'statusLabels', 'statusValues', 'startDate', 'endDate'));
    }

    public function revenueReportsexport()
    {
        $rentals = $this->getRentals();
        $filename = 'revenue-report-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rentals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Invoice', 'Customer', 'Tool', 'Total Price', 'Status', 'Date']);
            foreach ($rentals as $r) {
                fputcsv($handle, [$r['invoiceNumber'], $r['customerName'], $r['toolName'] ?? '-', $r['totalPrice'], $r['status'], $r['startDate'] ?? '-']);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Inventory Reports

    public function inventoryReports(Request $request)
    {
        $tools = $this->getTools();
        $warehouses = $this->getWarehouses();
        $stockOverview = $this->getStockOverview();

        // Summary
        $totalTools = count($tools);
        $totalValue = array_sum(array_column($tools, 'replacementValue'));
        $lowStockCount = count(array_filter($stockOverview, fn($s) => $s['quantity'] < 3));
        $totalWarehouses = count($warehouses);

        // Tool status distribution → doughnut
        $statusCount = [];
        foreach ($tools as $t) {
            $statusCount[$t['status']] = ($statusCount[$t['status']] ?? 0) + 1;
        }

        // Tool category distribution → bar
        $categoryCount = [];
        foreach ($tools as $t) {
            $categoryCount[$t['category']] = ($categoryCount[$t['category']] ?? 0) + 1;
        }

        // Warna per status
        $statusColorMap = [
            'Available' => 'rgba(34, 197, 94, 0.7)',
            'Rented' => 'rgba(59, 130, 246, 0.7)',
            'Damaged' => 'rgba(234, 179, 8, 0.7)',
            'Lost' => 'rgba(239, 68, 68, 0.7)',
        ];
        $statusColors = array_values(array_map(fn($s) => $statusColorMap[$s] ?? 'rgba(156, 163, 175, 0.7)', array_keys($statusCount)));

        // Hitung utilization tiap warehouse
        foreach ($warehouses as &$wh) {
            $wh['utilization'] = $wh['capacity'] > 0 ? round(($wh['currentStock'] / $wh['capacity']) * 100) : 0;
            $wh['utilizationColor'] = $wh['utilization'] > 80 ? 'bg-red-400' : ($wh['utilization'] > 50 ? 'bg-yellow-400' : 'bg-green-400');
        }
        unset($wh);

        $startDate = $request->get('start_date', Carbon::now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->toDateString());

        return view('reports.inventoryReport', compact('tools', 'warehouses', 'totalTools', 'totalValue', 'lowStockCount', 'totalWarehouses', 'statusCount', 'statusColors', 'categoryCount', 'startDate', 'endDate'));
    }

    public function inventoryReportsexport()
    {
        $tools = $this->getTools();
        $filename = 'inventory-report-' . now()->format('Ymd-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($tools) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Code', 'Name', 'Category', 'Status', 'Replacement Value']);
            foreach ($tools as $t) {
                fputcsv($handle, [$t['code'], $t['name'], $t['category'], $t['status'], $t['replacementValue']]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
