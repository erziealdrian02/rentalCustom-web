<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Tools;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

class ToolsController extends Controller
{
    public function masterTools(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $tools = Tools::with('category')->paginate($perPage);
        $categories = Categories::get();

        return view('master.tools', compact('tools', 'categories'));
    }

    public function masterToolsStore(Request $request)
    {
        $uuid = Str::uuid();

        // 🔹 Ambil category
        $category = Categories::findOrFail($request->category);

        // 🔹 Generate prefix dari nama category
        $words = explode(' ', strtoupper($category->name));

        if (count($words) > 1) {
            // Ambil 2 huruf dari tiap kata
            $prefix = substr($words[0], 0, 2) . substr($words[1], 0, 2);
        } else {
            // Ambil 4 huruf pertama
            $prefix = substr($words[0], 0, 4);
        }

        // 🔹 Hitung jumlah tools dalam category tsb
        $count = Tools::where('category_id', $category->id)->count() + 1;

        // 🔹 Format angka (001, 002, dst)
        $number = str_pad($count, 3, '0', STR_PAD_LEFT);

        // 🔹 Final code
        $code = $prefix . '-' . $number;

        // 🔹 Serial number
        $year = date('Y');
        $serialNumber = "SN-{$year}-{$code}";

        // 🔹 Simpan data
        $model = new Tools();
        $model->id_tools = $uuid;
        $model->code = $code;
        $model->name = $request->name;
        $model->category_id = $category->id;
        $model->serial_number = $serialNumber;
        $model->replacement_value = (int) $request->replacementValue;
        $model->status = strtolower($request->status);

        $model->save();

        return redirect()->route('master.tools')->with('success', 'Tool added successfully!');
    }

    public function masterToolsUpdate(Request $request, $id_tools)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|exists:tool_categories,id',
            'replacementValue' => 'required|numeric|min:0',
            'status' => 'required|string',
        ]);

        $model = Tools::findOrFail($id_tools);

        $model->name = $request->name;
        $model->category_id = $request->category;
        $model->replacement_value = (int) $request->replacementValue;
        $model->status = strtolower($request->status);

        $model->save();

        return redirect()->route('master.tools')->with('success', 'Tool updated successfully!');
    }

    public function masterToolsDestroy($id_tools)
    {
        $tools = Tools::all();
        $tools = $tools
            ->filter(function ($tool) use ($id_tools) {
                return $tool->id_tools != $id_tools;
            })
            ->values();
        session(['tools' => $tools]);

        return redirect()->route('master.tools')->with('success', 'Tool deleted successfully!');
    }
}
