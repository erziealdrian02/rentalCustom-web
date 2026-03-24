<?php

namespace App\Http\Controllers;

use App\Models\Rentals;
use App\Models\Shipping;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

    private const ACTIVE_STATUSES = ['Pending', 'Delivered', 'On Track', 'Overdue', 'Returning', 'On Check', 'Waiting'];

    // ── Monitoring page ─────────────────────────────────────────
    public function monitoringActive()
    {
        // 1. Ambil semua rental aktif + eager-load customer
        $rawRentals = Rentals::with('customer')
            ->whereIn('rental_status', ['Delivered', 'On Track'])
            ->orderBy('rental_end_date')
            ->get();

        // 2. Kumpulkan semua movement IDs dari seluruh rental
        $allMovementIds = [];
        foreach ($rawRentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovementIds = array_merge($allMovementIds, $ids);
        }

        // 3. Fetch semua movements + tools sekaligus (N+1 prevention)
        $movements = StockMovement::whereIn('id', $allMovementIds)->with('tool')->get()->keyBy('id');

        // 4. Fetch semua shipping lalu index by rental_id
        $allShippings = Shipping::with('driver')->get();

        $shippingByRental = [];
        foreach ($allShippings as $shipping) {
            $rentalIdsInShipping = json_decode($shipping->rental_id, true) ?? [];
            foreach ($rentalIdsInShipping as $rId) {
                $shippingByRental[$rId] = $shipping;
            }
        }

        // 5. Format data untuk view
        $rentals = $rawRentals->map(function ($rental) use ($movements, $shippingByRental) {
            $today = \Carbon\Carbon::today();
            $endDate = \Carbon\Carbon::parse($rental->rental_end_date);
            $daysRemaining = $today->diffInDays($endDate, false); // negatif jika overdue

            // Ambil movement IDs milik rental ini
            $movementIds = json_decode($rental->movement_id, true) ?? [];

            // Build items array
            $items = [];
            foreach ($movementIds as $mId) {
                $movement = $movements->get($mId);
                if (!$movement || !$movement->tool) {
                    continue;
                }

                $tool = $movement->tool;
                $qty = $movement->quantity;

                // Hitung durasi sewa dalam hari
                $startDate = \Carbon\Carbon::parse($rental->rental_start_date);
                $rentalDays = max(1, $startDate->diffInDays($endDate));

                $dailyRate = $tool->daily_rate ?? 0;
                $subtotal = $dailyRate * $qty * $rentalDays;

                $items[] = [
                    'toolName' => $tool->name,
                    'quantity' => $qty,
                    'dailyRate' => $dailyRate,
                    'subtotal' => $subtotal,
                ];
            }

            $totalRevenue = collect($items)->sum('subtotal');
            $startDate = \Carbon\Carbon::parse($rental->rental_start_date);
            $rentalDays = max(1, $startDate->diffInDays($endDate));
            $dailyAverage = $rentalDays > 0 ? $totalRevenue / $rentalDays : 0;

            // Shipping / driver info
            $shipping = $shippingByRental[$rental->id] ?? null;

            return [
                'id' => $rental->id,
                'invoiceNumber' => $rental->invoice_number,
                'customerName' => $rental->customer->name ?? '-',
                'rentalStartDate' => $rental->rental_start_date,
                'rentalEndDate' => $rental->rental_end_date,
                'daysRemaining' => (int) $daysRemaining,
                'rentalStatus' => $rental->rental_status,
                'paymentStatus' => $rental->payment_status,
                'totalRevenue' => $totalRevenue,
                'dailyAverage' => $dailyAverage,
                'createdDate' => $rental->created_at,
                'notes' => $rental->notes,
                'items' => $items,

                // Delivery info (dari shipping)
                'driverName' => $shipping?->driver?->name,
                'deliveryNumber' => $shipping?->delivery_number,
                'deliveryLocation' => $shipping?->to_location,
                'deliveryStatus' => $shipping?->delivery_status,
                'estimatedDeliveryTime' => $shipping?->estimated_arrival_time ? \Carbon\Carbon::parse($shipping->estimated_arrival_time)->format('d M Y, H:i') : null,
                'actualDeliveryTime' => $shipping?->actual_arrival_time ? \Carbon\Carbon::parse($shipping->actual_arrival_time)->format('d M Y, H:i') : null,
                'departureTime' => $shipping?->departure_time ? \Carbon\Carbon::parse($shipping->departure_time)->format('d M Y, H:i') : null,
            ];
        });

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
