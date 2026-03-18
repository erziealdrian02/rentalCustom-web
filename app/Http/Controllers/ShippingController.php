<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShippingController extends Controller
{
    private function getShippings()
    {
        return session('shippings', [
            [
                'id'             => 1,
                'deliveryNumber' => 'DEL-2025-001',
                'driverName'     => 'Ahmad Supardi',
                'vehicleType'    => 'Truck',
                'licensePlate'   => 'B 1234 ABC',
                'phone'          => '085-555-2001',
                'status'         => 'Delivered',
                'departureTime'  => '2025-01-10 08:00',
                'arrivalTime'    => '2025-01-10 11:30',
                'notes'          => 'Delivered on time. All items in good condition.',
                'proofImage'     => null,
                'rentals'        => [
                    [
                        'customerName'  => 'John Doe',
                        'invoiceNumber' => 'INV-2025-001',
                        'items'         => 2,
                        'fromLocation'  => 'Warehouse Alpha',
                        'toLocation'    => 'Jl. Sudirman No. 10, Jakarta',
                    ],
                ],
            ],
            [
                'id'             => 2,
                'deliveryNumber' => 'DEL-2025-002',
                'driverName'     => 'Budi Santoso',
                'vehicleType'    => 'Van',
                'licensePlate'   => 'D 5678 XYZ',
                'phone'          => '085-555-2002',
                'status'         => 'In Transit',
                'departureTime'  => '2025-01-15 09:00',
                'arrivalTime'    => null,
                'notes'          => 'On the way to destination.',
                'proofImage'     => null,
                'rentals'        => [
                    [
                        'customerName'  => 'Jane Smith',
                        'invoiceNumber' => 'INV-2025-002',
                        'items'         => 3,
                        'fromLocation'  => 'Warehouse Beta',
                        'toLocation'    => 'Jl. Gatot Subroto No. 5, Bandung',
                    ],
                ],
            ],
            [
                'id'             => 3,
                'deliveryNumber' => 'DEL-2025-003',
                'driverName'     => 'Candra Wijaya',
                'vehicleType'    => 'Pickup',
                'licensePlate'   => 'L 9012 DEF',
                'phone'          => '085-555-2003',
                'status'         => 'Pending',
                'departureTime'  => '2025-01-20 10:00',
                'arrivalTime'    => null,
                'notes'          => 'Waiting for dispatch confirmation.',
                'proofImage'     => null,
                'rentals'        => [
                    [
                        'customerName'  => 'Bob Johnson',
                        'invoiceNumber' => 'INV-2025-003',
                        'items'         => 1,
                        'fromLocation'  => 'Warehouse Gamma',
                        'toLocation'    => 'Jl. Ahmad Yani No. 88, Surabaya',
                    ],
                ],
            ],
        ]);
    }

    private function getDrivers()
    {
        return session('drivers', [
            ['id' => 1, 'name' => 'Ahmad Supardi', 'vehicleType' => 'Truck',  'licensePlate' => 'B 1234 ABC', 'phone' => '085-555-2001', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Budi Santoso',  'vehicleType' => 'Van',    'licensePlate' => 'D 5678 XYZ', 'phone' => '085-555-2002', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Candra Wijaya', 'vehicleType' => 'Pickup', 'licensePlate' => 'L 9012 DEF', 'phone' => '085-555-2003', 'status' => 'Active'],
            ['id' => 4, 'name' => 'Deni Kurniawan','vehicleType' => 'Truck',  'licensePlate' => 'B 3456 GHI', 'phone' => '085-555-2004', 'status' => 'Inactive'],
        ]);
    }
 
    private function getRentals()
    {
        return session('rentals', [
            ['id' => 1, 'invoiceNumber' => 'INV-2025-001', 'customerName' => 'John Doe',    'customerId' => 1, 'status' => 'Active',    'items' => [['toolName' => 'Angle Grinder'], ['toolName' => 'Hammer']]],
            ['id' => 2, 'invoiceNumber' => 'INV-2025-002', 'customerName' => 'Jane Smith',  'customerId' => 2, 'status' => 'Active',    'items' => [['toolName' => 'Drill Machine'], ['toolName' => 'Safety Helmet'], ['toolName' => 'Wrench']]],
            ['id' => 3, 'invoiceNumber' => 'INV-2025-003', 'customerName' => 'Bob Johnson', 'customerId' => 3, 'status' => 'Completed', 'items' => [['toolName' => 'Ladder']]],
        ]);
    }
 
    public function list()
    {
        $shippings = $this->getShippings();
 
        $totalShipments = count($shippings);
        $delivered      = count(array_filter($shippings, fn($s) => $s['status'] === 'Delivered'));
        $inTransit      = count(array_filter($shippings, fn($s) => $s['status'] === 'In Transit'));
        $pending        = count(array_filter($shippings, fn($s) => $s['status'] === 'Pending'));
 
        return view('shipping.shippingList', compact(
            'shippings', 'totalShipments', 'delivered', 'inTransit', 'pending'
        ));
    }

    public function form()
    {
        // Hanya rental yang belum dikirim
        $rentals = array_values(array_filter(
            $this->getRentals(),
            fn($r) => !in_array($r['status'] ?? '', ['On Check', 'Delivered'])
        ));
 
        // Hanya driver yang aktif
        $drivers = array_values(array_filter(
            $this->getDrivers(),
            fn($d) => $d['status'] === 'Active'
        ));
 
        return view('shipping.createShipping', compact('rentals', 'drivers'));
    }
 
    public function store(Request $request)
    {
        $request->validate([
            'driverId'     => 'required|integer',
            'rentalItems'  => 'required|string', // JSON dari JS
        ]);
 
        $drivers = $this->getDrivers();
        $driver  = collect($drivers)->firstWhere('id', (int) $request->driverId);
 
        if (!$driver) {
            return back()->withErrors(['driverId' => 'Driver not found.'])->withInput();
        }
 
        $addedRentals = json_decode($request->rentalItems, true);
        if (empty($addedRentals)) {
            return back()->withErrors(['rentalItems' => 'Please add at least one rental.'])->withInput();
        }
 
        $shippings      = $this->getShippings();
        $maxId          = count($shippings) ? max(array_column($shippings, 'id')) : 0;
        $deliveryNumber = 'DEL-' . now()->year . '-' . str_pad(count($shippings) + 1, 3, '0', STR_PAD_LEFT);
 
        $shippings[] = [
            'id'             => $maxId + 1,
            'deliveryNumber' => $deliveryNumber,
            'driverName'     => $driver['name'],
            'vehicleType'    => $driver['vehicleType'],
            'licensePlate'   => $driver['licensePlate'],
            'phone'          => $driver['phone'],
            'status'         => 'Pending',
            'departureTime'  => now()->format('Y-m-d H:i'),
            'arrivalTime'    => null,
            'notes'          => 'Shipment created',
            'proofImage'     => null,
            'rentals'        => array_map(fn($item) => [
                'rentalId'      => $item['rentalId'],
                'invoiceNumber' => $item['invoiceNumber'],
                'customerId'    => $item['customerId'] ?? null,
                'customerName'  => $item['customerName'],
                'fromLocation'  => $item['fromLocation'],
                'toLocation'    => $item['toLocation'],
                'items'         => $item['itemCount'],
            ], $addedRentals),
        ];
 
        session(['shippings' => $shippings]);
 
        return redirect()->route('shippings.index')
            ->with('success', "Shipment created successfully! Delivery: {$deliveryNumber}");
    }
}