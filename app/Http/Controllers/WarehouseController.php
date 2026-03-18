<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private function getWarehouses()
    {
        return session('warehouses', [
            [
                'id' => 1,
                'name' => 'Main Warehouse',
                'location' => 'Downtown',
                'capacity' => 1000,
                'currentStock' => 450,
            ],
            [
                'id' => 2,
                'name' => 'North Branch',
                'location' => 'North District',
                'capacity' => 500,
                'currentStock' => 280,
            ],
            [
                'id' => 3,
                'name' => 'South Facility',
                'location' => 'South Zone',
                'capacity' => 800,
                'currentStock' => 320,
            ],
        ]);
    }

    public function masterWarehouses()
    {
         $warehouses = $this->getWarehouses();
 
        // Group by location
        $grouped = [];
        foreach ($warehouses as $w) {
            $grouped[$w['location']][] = $w;
        }
 
        return view('master.warehouses', compact('warehouses', 'grouped'));
    }

    public function masterWarehousesStore(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'location'     => 'required|string|max:100',
            'capacity'     => 'required|integer|min:1',
            'currentStock' => 'required|integer|min:0',
        ]);
 
        $warehouses   = $this->getWarehouses();
        $maxId        = count($warehouses) ? max(array_column($warehouses, 'id')) : 0;
        $warehouses[] = [
            'id'           => $maxId + 1,
            'name'         => $request->name,
            'location'     => $request->location,
            'capacity'     => (int) $request->capacity,
            'currentStock' => (int) $request->currentStock,
        ];
        session(['warehouses' => $warehouses]);
 
        return redirect()->route('warehouses.index')->with('success', 'Warehouse added successfully!');
    }
 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'location'     => 'required|string|max:100',
            'capacity'     => 'required|integer|min:1',
            'currentStock' => 'required|integer|min:0',
        ]);
 
        $warehouses = $this->getWarehouses();
        foreach ($warehouses as &$w) {
            if ($w['id'] == $id) {
                $w['name']         = $request->name;
                $w['location']     = $request->location;
                $w['capacity']     = (int) $request->capacity;
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
