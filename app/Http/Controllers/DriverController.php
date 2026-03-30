<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DriverController extends Controller
{
    public function masterDrivers(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $allowedSorts = ['name', 'phone', 'license_plate', 'vehicle_type', 'status'];
        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'name';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $search = $request->search;

        $drivers = Driver::when(
            $search,
            fn($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('license_plate', 'like', "%{$search}%")
                    ->orWhere('vehicle_type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%");
            }),
        )
            ->orderBy($sortBy, $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('master.drivers', compact('drivers'));
    }

    public function masterDriversStore(Request $request)
    {
        $uuid = Str::uuid();

        $model = new Driver();
        $model->id = $uuid;
        $model->name = $request->name;
        $model->vehicle_type = $request->vehicle_type;
        $model->license_plate = $request->license_plate;
        $model->phone = $request->phone;
        $model->email = $request->email;
        $model->status = strtolower($request->status);

        $model->save();

        return redirect()->route('master.drivers')->with('success', 'Tool added successfully!');
    }

    public function masterDriversUpdate(Request $request, $id)
    {
        $model = Driver::findOrFail($id);

        $model->name = $request->name;
        $model->vehicle_type = $request->vehicle_type;
        $model->license_plate = $request->license_plate;
        $model->phone = $request->phone;
        $model->email = $request->email;
        $model->status = strtolower($request->status);

        $model->save();

        return redirect()->route('master.drivers')->with('success', 'Tool updated successfully!');
    }

    public function masterDriversDestroy($id_tools)
    {
        $tools = Driver::all();
        $tools = $tools
            ->filter(function ($tool) use ($id_tools) {
                return $tool->id_tools != $id_tools;
            })
            ->values();
        session(['tools' => $tools]);

        return redirect()->route('master.drivers')->with('success', 'Tool deleted successfully!');
    }
}
