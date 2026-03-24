<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Rentals;
use App\Models\Shipping;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShippingController extends Controller
{
    private function getShippings()
    {
        return session('shippings', [
            [
                'id' => 1,
                'deliveryNumber' => 'DEL-2025-001',
                'driverName' => 'Ahmad Supardi',
                'vehicleType' => 'Truck',
                'licensePlate' => 'B 1234 ABC',
                'phone' => '085-555-2001',
                'status' => 'Delivered',
                'departureTime' => '2025-01-10 08:00',
                'arrivalTime' => '2025-01-10 11:30',
                'notes' => 'Delivered on time. All items in good condition.',
                'proofImage' => null,
                'rentals' => [
                    [
                        'customerName' => 'John Doe',
                        'invoiceNumber' => 'INV-2025-001',
                        'items' => 2,
                        'fromLocation' => 'Warehouse Alpha',
                        'toLocation' => 'Jl. Sudirman No. 10, Jakarta',
                    ],
                ],
            ],
            [
                'id' => 2,
                'deliveryNumber' => 'DEL-2025-002',
                'driverName' => 'Budi Santoso',
                'vehicleType' => 'Van',
                'licensePlate' => 'D 5678 XYZ',
                'phone' => '085-555-2002',
                'status' => 'In Transit',
                'departureTime' => '2025-01-15 09:00',
                'arrivalTime' => null,
                'notes' => 'On the way to destination.',
                'proofImage' => null,
                'rentals' => [
                    [
                        'customerName' => 'Jane Smith',
                        'invoiceNumber' => 'INV-2025-002',
                        'items' => 3,
                        'fromLocation' => 'Warehouse Beta',
                        'toLocation' => 'Jl. Gatot Subroto No. 5, Bandung',
                    ],
                ],
            ],
            [
                'id' => 3,
                'deliveryNumber' => 'DEL-2025-003',
                'driverName' => 'Candra Wijaya',
                'vehicleType' => 'Pickup',
                'licensePlate' => 'L 9012 DEF',
                'phone' => '085-555-2003',
                'status' => 'Pending',
                'departureTime' => '2025-01-20 10:00',
                'arrivalTime' => null,
                'notes' => 'Waiting for dispatch confirmation.',
                'proofImage' => null,
                'rentals' => [
                    [
                        'customerName' => 'Bob Johnson',
                        'invoiceNumber' => 'INV-2025-003',
                        'items' => 1,
                        'fromLocation' => 'Warehouse Gamma',
                        'toLocation' => 'Jl. Ahmad Yani No. 88, Surabaya',
                    ],
                ],
            ],
        ]);
    }

    private function getDrivers()
    {
        return session('drivers', [['id' => 1, 'name' => 'Ahmad Supardi', 'vehicleType' => 'Truck', 'licensePlate' => 'B 1234 ABC', 'phone' => '085-555-2001', 'status' => 'Active'], ['id' => 2, 'name' => 'Budi Santoso', 'vehicleType' => 'Van', 'licensePlate' => 'D 5678 XYZ', 'phone' => '085-555-2002', 'status' => 'Active'], ['id' => 3, 'name' => 'Candra Wijaya', 'vehicleType' => 'Pickup', 'licensePlate' => 'L 9012 DEF', 'phone' => '085-555-2003', 'status' => 'Active'], ['id' => 4, 'name' => 'Deni Kurniawan', 'vehicleType' => 'Truck', 'licensePlate' => 'B 3456 GHI', 'phone' => '085-555-2004', 'status' => 'Inactive']]);
    }

    private function getRentals()
    {
        return session('rentals', [['id' => 1, 'invoiceNumber' => 'INV-2025-001', 'customerName' => 'John Doe', 'customerId' => 1, 'status' => 'Active', 'items' => [['toolName' => 'Angle Grinder'], ['toolName' => 'Hammer']]], ['id' => 2, 'invoiceNumber' => 'INV-2025-002', 'customerName' => 'Jane Smith', 'customerId' => 2, 'status' => 'Active', 'items' => [['toolName' => 'Drill Machine'], ['toolName' => 'Safety Helmet'], ['toolName' => 'Wrench']]], ['id' => 3, 'invoiceNumber' => 'INV-2025-003', 'customerName' => 'Bob Johnson', 'customerId' => 3, 'status' => 'Completed', 'items' => [['toolName' => 'Ladder']]]]);
    }

    public function shippingList()
    {
        $shippings = $this->getShippings();

        $totalShipments = count($shippings);
        $delivered = count(array_filter($shippings, fn($s) => $s['status'] === 'Delivered'));
        $inTransit = count(array_filter($shippings, fn($s) => $s['status'] === 'In Transit'));
        $pending = count(array_filter($shippings, fn($s) => $s['status'] === 'Pending'));

        return view('shipping.shippingList', compact('shippings', 'totalShipments', 'delivered', 'inTransit', 'pending'));
    }

    public function shippingForm()
    {
        $rentals = Rentals::with('customer')
            ->whereIn('rental_status', ['Pending', 'Returning'])
            ->get();

        $drivers = Driver::where('status', 'active')->get();
        $warehouses = Warehouse::all()->keyBy('id'); // <-- tambah ini

        $allMovementIds = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovementIds = array_merge($allMovementIds, $ids);
        }

        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovementIds))->get()->keyBy('id');

        $movementsByRentalId = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $movementsByRentalId[$rental->id] = collect($ids)->map(fn($id) => $movements->get($id))->filter()->values();
        }

        return view('shipping.createShipping', compact('rentals', 'drivers', 'movementsByRentalId', 'warehouses'));
    }

    public function shippingStore(Request $request)
    {
        $request->validate([
            'driverId' => 'required',
            'rentalItems' => 'required|string',
        ]);

        $rentalItems = json_decode($request->rentalItems, true);

        if (empty($rentalItems)) {
            return back()->withErrors(['rentalItems' => 'No rentals added.']);
        }

        // Kumpulkan semua rental_id
        $rentalIds = collect($rentalItems)->pluck('rentalId')->values()->toArray();

        // Ambil data rental untuk dapat warehouse_id tiap rental
        $rentals = Rentals::whereIn('id', $rentalIds)->get()->keyBy('id');

        // Bangun from_location: array of arrays warehouse_id per rental
        // Format: [[3,1], [3]] → kalau 2 rental
        $fromLocation = collect($rentalItems)
            ->map(function ($item) use ($rentals) {
                $rental = $rentals->get($item['rentalId']);
                if (!$rental) {
                    return [];
                }
                return json_decode($rental->warehouse_id, true) ?? [];
            })
            ->values()
            ->toArray();

        // Ambil to_location dari rental pertama (atau bisa disesuaikan)
        // Karena di tabel to_location = varchar (1 value), ambil dari item pertama
        $toLocation = $rentalItems[0]['toLocation'] ?? '';

        // Generate delivery number
        $deliveryNumber = 'DEL-' . strtoupper(Str::random(4)) . '-' . now()->format('Ymd');

        // Pastikan unik
        while (Shipping::where('delivery_number', $deliveryNumber)->exists()) {
            $deliveryNumber = 'DEL-' . strtoupper(Str::random(4)) . '-' . now()->format('Ymd');
        }
        
        // dd($rentalItems);

        $shipping = new Shipping();
        $shipping->id = (string) Str::uuid();
        $shipping->delivery_number = $deliveryNumber;
        $shipping->driver_id = $request->driverId;
        $shipping->rental_id = json_encode($rentalIds);
        $shipping->from_location = json_encode($fromLocation);
        $shipping->to_location = $toLocation;
        $shipping->delivery_status = 'On Track';
        // dd($rentalItems);

        $shipping->save();

        foreach ($rentalItems as $item) {
            $rental = Rentals::find($item['rentalId']);
            if (!$rental) {
                continue; // skip kalau rental tidak ditemukan
            }

            $rental->delivery_id = $deliveryNumber;
            $rental->driver_id = $request->driverId;
            $rental->rental_status = 'On Track';

            $rental->save();
        }

        return redirect()->route('shipping.list')->with('success', 'Shipment created successfully!');
    }
}
