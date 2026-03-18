<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    private function getRentals()
    {
        return session('rentals', [
            [
                'id' => 1,
                'invoiceNumber' => 'INV-2025-001',
                'customerId' => 1,
                'customerName' => 'John Doe',
                'rentalStatus' => 'On Track',
                'status' => 'Active',
                'rentalStartDate' => '2025-01-10',
                'rentalEndDate' => now()->addDays(5)->toDateString(),
                'totalPrice' => 175.0,
                'createdDate' => '2025-01-10',
                'driverName' => 'Ahmad Supardi',
                'deliveryLocation' => 'Jl. Sudirman No. 10, Jakarta',
                'estimatedDeliveryTime' => '2025-01-11 10:00',
                'items' => [['toolName' => 'Angle Grinder', 'quantity' => 1, 'dailyRate' => 25.0, 'subtotal' => 175.0]],
            ],
            [
                'id' => 2,
                'invoiceNumber' => 'INV-2025-002',
                'customerId' => 2,
                'customerName' => 'Jane Smith',
                'rentalStatus' => 'Pending',
                'status' => 'Active',
                'rentalStartDate' => '2025-01-05',
                'rentalEndDate' => now()->addDays(1)->toDateString(),
                'totalPrice' => 210.0,
                'createdDate' => '2025-01-05',
                'driverName' => null,
                'deliveryLocation' => null,
                'estimatedDeliveryTime' => null,
                'items' => [['toolName' => 'Drill Machine', 'quantity' => 1, 'dailyRate' => 20.0, 'subtotal' => 140.0], ['toolName' => 'Safety Helmet', 'quantity' => 2, 'dailyRate' => 5.0, 'subtotal' => 70.0]],
            ],
            [
                'id' => 3,
                'invoiceNumber' => 'INV-2025-003',
                'customerId' => 3,
                'customerName' => 'Bob Johnson',
                'rentalStatus' => 'Menunggak',
                'status' => 'Active',
                'rentalStartDate' => '2024-12-01',
                'rentalEndDate' => now()->subDays(3)->toDateString(),
                'totalPrice' => 105.0,
                'createdDate' => '2024-12-01',
                'driverName' => 'Budi Santoso',
                'deliveryLocation' => 'Jl. Ahmad Yani No. 88, Surabaya',
                'estimatedDeliveryTime' => null,
                'items' => [['toolName' => 'Hammer', 'quantity' => 3, 'dailyRate' => 5.0, 'subtotal' => 105.0]],
            ],
        ]);
    }

    public function monitoringActive()
    {
        $allRentals = $this->getRentals();

        // Hanya tampilkan yang bukan On Check
        $rentals = array_values(array_filter($allRentals, fn($r) => ($r['rentalStatus'] ?? '') !== 'On Check'));

        // Hitung days remaining tiap rental
        foreach ($rentals as &$rental) {
            $end = \Carbon\Carbon::parse($rental['rentalEndDate']);
            $rental['daysRemaining'] = (int) now()->startOfDay()->diffInDays($end->startOfDay(), false);
            $rental['totalRevenue'] = array_sum(array_column($rental['items'], 'subtotal'));

            $start = \Carbon\Carbon::parse($rental['rentalStartDate']);
            $totalDays = max(1, $start->diffInDays($end));
            $rental['dailyAverage'] = $rental['totalRevenue'] / $totalDays;
        }
        unset($rental);

        return view('monitoring.activeMonitoring', compact('rentals'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:On Track,Delivered,Pending,Menunggak,Returning,On Check',
        ]);

        $rentals = $this->getRentals();
        foreach ($rentals as &$rental) {
            if ($rental['id'] == $id) {
                $rental['rentalStatus'] = $request->status;
                break;
            }
        }
        session(['rentals' => $rentals]);

        return response()->json(['success' => true, 'newStatus' => $request->status]);
    }
}
