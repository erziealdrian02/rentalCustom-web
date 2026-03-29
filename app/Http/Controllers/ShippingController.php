<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Rentals;
use App\Models\Shipping;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $shippings = Shipping::all();

        // Kumpulkan semua rental_id dan driver_id yang dibutuhkan
        $allRentalIds = [];
        $allDriverIds = [];

        foreach ($shippings as $shipping) {
            $rentalIds = is_array($shipping->rental_id) ? $shipping->rental_id : json_decode($shipping->rental_id, true) ?? [];

            $allRentalIds = array_merge($allRentalIds, $rentalIds);
            $allDriverIds[] = $shipping->driver_id;
        }

        // Fetch semua data yang dibutuhkan sekaligus
        $rentals = Rentals::with('customer')->whereIn('id', array_unique($allRentalIds))->get()->keyBy('id');
        $drivers = Driver::whereIn('id', array_unique($allDriverIds))->get()->keyBy('id');
        $warehouses = Warehouse::all()->keyBy('id');

        // Kumpulkan semua movement_id dari rentals
        $allMovementIds = [];
        foreach ($rentals as $rental) {
            $ids = json_decode($rental->movement_id, true) ?? [];
            $allMovementIds = array_merge($allMovementIds, $ids);
        }
        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovementIds))->get()->keyBy('id');

        // Summary counts
        $totalShipments = $shippings->count();
        $delivered = $shippings->where('delivery_status', 'Delivered')->count();
        $onTrack = $shippings->where('delivery_status', 'Pending')->count();
        $pending = $shippings->where('delivery_status', 'Pending')->count();

        return view('shipping.shippingList', compact('shippings', 'rentals', 'drivers', 'warehouses', 'movements', 'totalShipments', 'delivered', 'onTrack', 'pending'));
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
        $shipping->delivery_status = 'Pending';
        // dd($rentalItems);

        $shipping->save();

        foreach ($rentalItems as $item) {
            $rental = Rentals::find($item['rentalId']);
            if (!$rental) {
                continue; // skip kalau rental tidak ditemukan
            }

            $rental->delivery_id = $deliveryNumber;
            $rental->driver_id = $request->driverId;
            $rental->rental_status = 'Pending';

            $rental->save();
        }

        return redirect()->route('shipping.list')->with('success', 'Shipment created successfully!');
    }

    // ShippingController.php

    public function shippingDriver($id)
    {
        $driver = Driver::findOrFail($id);

        // Active: pending & in_transit
        $activeShippings = Shipping::where('driver_id', $id)
            ->whereIn('delivery_status', ['Pending','On Track'])
            ->orderBy('created_at', 'desc')
            ->get();

        // History: delivered, failed, cancelled
        $historyShippings = Shipping::where('driver_id', $id)
            ->whereIn('delivery_status', ['delivered', 'failed', 'cancelled'])
            ->orderBy('actual_arrival_time', 'desc')
            ->get();

        $allShippings = $activeShippings->merge($historyShippings);

        // Resolve rentals
        $allRentalIds = [];
        foreach ($allShippings as $shipping) {
            $ids = is_array($shipping->rental_id) ? $shipping->rental_id : json_decode($shipping->rental_id, true) ?? [];
            $allRentalIds = array_merge($allRentalIds, $ids);
        }

        $rentals = Rentals::with('customer')->whereIn('id', array_unique($allRentalIds))->get()->keyBy('id');
        $warehouses = Warehouse::all()->keyBy('id');

        $allMovIds = [];
        foreach ($rentals as $r) {
            $ids = json_decode($r->movement_id, true) ?? [];
            $allMovIds = array_merge($allMovIds, $ids);
        }
        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovIds))->get()->keyBy('id');

        return view('shipping.driver.driverShippingList', compact('driver', 'activeShippings', 'historyShippings', 'rentals', 'warehouses', 'movements'));
    }

    public function shippingDriverDeparture($delivery_number)
    {
        $shipping = Shipping::where('delivery_number', $delivery_number)->firstOrFail();
        $driver = Driver::find($shipping->driver_id);

        $rentalIds = is_array($shipping->rental_id) ? $shipping->rental_id : json_decode($shipping->rental_id, true) ?? [];

        $rentals = Rentals::with('customer')->whereIn('id', $rentalIds)->get()->keyBy('id');
        $warehouses = Warehouse::all()->keyBy('id');

        $allMovIds = [];
        foreach ($rentals as $r) {
            $ids = json_decode($r->movement_id, true) ?? [];
            $allMovIds = array_merge($allMovIds, $ids);
        }
        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovIds))->get()->keyBy('id');

        $fromLocation = is_array($shipping->from_location) ? $shipping->from_location : json_decode($shipping->from_location, true) ?? [];

        return view('shipping.driver.driverDeparture', compact('shipping', 'driver', 'rentals', 'warehouses', 'movements', 'fromLocation'));
    }

    public function shippingDriverDepartureUpdate(Request $request, $delivery_number)
    {
        $shipping = Shipping::where('delivery_number', $delivery_number)->firstOrFail();

        $now = now();

        $shipping->departure_time = $now;
        $shipping->delivery_status = 'On Track';

        $rentals = Rentals::whereIn('id', json_decode($shipping->rental_id, true) ?? [])->get();

        foreach ($rentals as $rental) {
            $movements = StockMovement::whereIn('id', json_decode($rental->movement_id, true) ?? [])->get();
            foreach ($movements as $movement) {
                $movement->movement_type = 'Shipping';
                $movement->save();
            }
            $rental->rental_status = 'On Track';
            $rental->save();
        }

        $shipping->save();

        return redirect()->route('shipping.driver.arrival', $delivery_number)->with('success', 'Departure confirmed! Delivery is now in Pending.');
    }

    public function shippingDriverArrival($delivery_number)
    {
        $shipping = Shipping::where('delivery_number', $delivery_number)->firstOrFail();

        // Kalau belum departure, redirect ke departure dulu
        if (!$shipping->departure_time) {
            return redirect()->route('shipping.driver.departure', $delivery_number);
        }

        $driver = Driver::find($shipping->driver_id);

        $rentalIds = is_array($shipping->rental_id) ? $shipping->rental_id : json_decode($shipping->rental_id, true) ?? [];

        $rentals = Rentals::with('customer')->whereIn('id', $rentalIds)->get()->keyBy('id');
        $warehouses = Warehouse::all()->keyBy('id');

        $allMovIds = [];
        foreach ($rentals as $r) {
            $ids = json_decode($r->movement_id, true) ?? [];
            $allMovIds = array_merge($allMovIds, $ids);
        }
        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovIds))->get()->keyBy('id');

        return view('shipping.driver.driverArrival', compact('shipping', 'driver', 'rentals', 'warehouses', 'movements'));
    }

    public function shippingDriverArrivalUpdate(Request $request, $delivery_number)
    {
        $request->validate([
            'proof_image' => 'required|image|max:5120',
            'notes' => 'nullable|string|max:1000',
        ]);

        $shipping = Shipping::where('delivery_number', $delivery_number)->firstOrFail();

        // Upload proof image
        $path = $request->file('proof_image')->store('delivery-proofs', 'public');

        $shipping->actual_arrival_time = now();
        $shipping->delivery_status = 'Delivered';
        $shipping->proof_image_url = $path;
        $shipping->notes = $request->notes;

        $rentals = Rentals::whereIn('id', json_decode($shipping->rental_id, true) ?? [])->get();

        foreach ($rentals as $rental) {
            $movements = StockMovement::whereIn('id', json_decode($rental->movement_id, true) ?? [])->get();
            foreach ($movements as $movement) {
                $movement->movement_type = 'Arrived';
                $movement->save();
            }
            $rental->rental_status = 'Delivered';
            $rental->save();
        }

        $shipping->save();

        return redirect()->route('shipping.driver', $shipping->driver_id)->with('success', 'Delivery completed successfully!');
    }

    public function shippingDriverHistoryDetail($id, $delivery_number)
    {
        $driver = Driver::findOrFail($id);
        $shipping = Shipping::where('delivery_number', $delivery_number)->where('driver_id', $id)->firstOrFail();

        $rentalIds = is_array($shipping->rental_id) ? $shipping->rental_id : json_decode($shipping->rental_id, true) ?? [];

        $rentals = Rentals::with('customer')->whereIn('id', $rentalIds)->get()->keyBy('id');
        $warehouses = Warehouse::all()->keyBy('id');

        $allMovIds = [];
        foreach ($rentals as $r) {
            $ids = json_decode($r->movement_id, true) ?? [];
            $allMovIds = array_merge($allMovIds, $ids);
        }
        $movements = StockMovement::with('tool')->whereIn('id', array_unique($allMovIds))->get()->keyBy('id');

        $fromLocation = is_array($shipping->from_location) ? $shipping->from_location : json_decode($shipping->from_location, true) ?? [];

        return view('shipping.driver.driverHistoryDetail', compact('driver', 'shipping', 'rentals', 'warehouses', 'movements', 'fromLocation'));
    }

    public function shippingDriverReupload(Request $request, $id, $delivery_number)
    {
        $shipping = Shipping::where('delivery_number', $delivery_number)->firstOrFail();
        // dd($shipping);

        // Hapus file lama kalau ada
        if ($shipping->proof_image_url && Storage::disk('public')->exists($shipping->proof_image_url)) {
            Storage::disk('public')->delete($shipping->proof_image_url);
        }

        $path = $request->file('proof_image')->store('delivery-proofs', 'public');

        $shipping->proof_image_url = $path;
        $shipping->notes = $request->notes ?? $shipping->notes;
        
        $shipping->save();

        return redirect()
            ->route('shipping.driver.history', [
                'id' => $id,
                'delivery_number' => $delivery_number,
            ])
            ->with('success', 'Bukti pengiriman berhasil diperbarui.');
    }
}
