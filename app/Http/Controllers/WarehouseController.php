<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private function getWarehouses()
    {
        return session('warehouses', [
            // Jawa Barat
            [
                'id' => 1,
                'name' => 'Gudang Bekasi Barat',
                'region' => 'Jawa Barat',
                'location' => 'Bekasi Barat, Bekasi',
                'capacity' => 1000,
                'currentStock' => 450,
            ],
            [
                'id' => 2,
                'name' => 'Gudang Bandung Utara',
                'region' => 'Jawa Barat',
                'location' => 'Cicendo, Bandung',
                'capacity' => 800,
                'currentStock' => 620,
            ],
            [
                'id' => 3,
                'name' => 'Gudang Depok',
                'region' => 'Jawa Barat',
                'location' => 'Sukmajaya, Depok',
                'capacity' => 500,
                'currentStock' => 190,
            ],

            // DKI Jakarta
            [
                'id' => 4,
                'name' => 'Gudang Cakung',
                'region' => 'DKI Jakarta',
                'location' => 'Cakung, Jakarta Timur',
                'capacity' => 1500,
                'currentStock' => 1320,
            ],
            [
                'id' => 5,
                'name' => 'Gudang Penjaringan',
                'region' => 'DKI Jakarta',
                'location' => 'Penjaringan, Jakarta Utara',
                'capacity' => 900,
                'currentStock' => 410,
            ],

            // Jawa Tengah
            [
                'id' => 6,
                'name' => 'Gudang Semarang Utara',
                'region' => 'Jawa Tengah',
                'location' => 'Semarang Utara, Semarang',
                'capacity' => 700,
                'currentStock' => 530,
            ],
            [
                'id' => 7,
                'name' => 'Gudang Solo Baru',
                'region' => 'Jawa Tengah',
                'location' => 'Grogol, Sukoharjo',
                'capacity' => 600,
                'currentStock' => 200,
            ],

            // Jawa Timur
            [
                'id' => 8,
                'name' => 'Gudang Surabaya Barat',
                'region' => 'Jawa Timur',
                'location' => 'Benowo, Surabaya',
                'capacity' => 1200,
                'currentStock' => 980,
            ],
            [
                'id' => 9,
                'name' => 'Gudang Malang',
                'region' => 'Jawa Timur',
                'location' => 'Lowokwaru, Malang',
                'capacity' => 550,
                'currentStock' => 120,
            ],
        ]);
    }

    public function masterWarehouses()
    {
        $warehouses = $this->getWarehouses();

        $grouped = [];
        foreach ($warehouses as $w) {
            $grouped[$w['region']][] = $w;
        }

        return view('master.warehouses', compact('warehouses', 'grouped'));
    }

    public function masterWarehousesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'currentStock' => 'required|integer|min:0',
        ]);

        $warehouses = $this->getWarehouses();
        $maxId = count($warehouses) ? max(array_column($warehouses, 'id')) : 0;
        $warehouses[] = [
            'id' => $maxId + 1,
            'name' => $request->name,
            'location' => $request->location,
            'capacity' => (int) $request->capacity,
            'currentStock' => (int) $request->currentStock,
        ];
        session(['warehouses' => $warehouses]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'currentStock' => 'required|integer|min:0',
        ]);

        $warehouses = $this->getWarehouses();
        foreach ($warehouses as &$w) {
            if ($w['id'] == $id) {
                $w['name'] = $request->name;
                $w['location'] = $request->location;
                $w['capacity'] = (int) $request->capacity;
                $w['currentStock'] = (int) $request->currentStock;
                break;
            }
        }
        session(['warehouses' => $warehouses]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully!');
    }

    public function destroy($id)
    {
        $warehouses = $this->getWarehouses();
        $warehouses = array_values(array_filter($warehouses, fn($w) => $w['id'] != $id));
        session(['warehouses' => $warehouses]);

        return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully!');
    }
}
