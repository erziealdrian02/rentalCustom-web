<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function masterWarehouses(Request $request)
    {
        $warehouses = Warehouse::get();

        $grouped = [];
        foreach ($warehouses as $w) {
            $grouped[$w['region']][] = $w;
        }

        return view('master.warehouses', compact('warehouses', 'grouped'));
    }

    public function masterWarehousesStore(Request $request)
    {
        $model = new Warehouse();
        $model->name = $request->name;
        $model->region = $request->region;
        $model->location = $request->location;
        $model->capacity = (int) $request->capacity;

        // 🔤 REGION CODE (ambil huruf depan tiap kata)
        $regionWords = explode(' ', $request->region);
        $regionCode = '';

        foreach ($regionWords as $word) {
            $regionCode .= strtoupper(substr($word, 0, 1));
        }

        $locationWords = explode(' ', $request->location);
        $locationCode = '';

        foreach ($locationWords as $word) {
            $locationCode .= strtoupper(substr($word, 0, 1));
        }

        if (strlen($locationCode) < 3) {
            $locationCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $request->location), 0, 3));
        }

        $count = Warehouse::where('region', $request->region)->where('location', $request->location)->count();

        $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        $model->code = "{$regionCode}-{$locationCode}-{$number}";

        $model->save();

        return redirect()->route('master.warehouses')->with('success', 'Warehouse added successfully!');
    }

    public function masterWarehousesUpdate(Request $request, $id)
    {
        $model = Warehouse::findOrFail($id);

        $region = $request->region === '__new__' ? $request->new_region : $request->region;

        $model->name = $request->name;
        $model->region = $region;
        $model->location = $request->location;
        $model->capacity = (int) $request->capacity;

        // Regenerate code hanya jika region atau location berubah
        if ($model->isDirty('region') || $model->isDirty('location')) {
            $regionWords = explode(' ', $region);
            $regionCode = '';
            foreach ($regionWords as $word) {
                $regionCode .= strtoupper(substr($word, 0, 1));
            }

            $locationCode = '';
            $locationWords = explode(' ', $request->location);
            foreach ($locationWords as $word) {
                $locationCode .= strtoupper(substr($word, 0, 1));
            }

            if (strlen($locationCode) < 3) {
                $locationCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $request->location), 0, 3));
            }

            // Exclude warehouse ini sendiri dari count agar tidak dobel
            $count = Warehouse::where('region', $region)->where('location', $request->location)->where('id', '!=', $id)->count();
            $number = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            $model->code = "{$regionCode}-{$locationCode}-{$number}";
        }

        $model->save(); // ← ini yang kurang

        return redirect()->route('master.warehouses')->with('success', 'Warehouse updated successfully!');
    }

    public function masterWarehousesDestroy($id)
    {
        $warehouse = Warehouse::find($id);
        if ($warehouse) {
            $warehouse->delete();
        }

        return redirect()->route('master.warehouses')->with('success', 'Warehouse deleted successfully!');
    }
}
