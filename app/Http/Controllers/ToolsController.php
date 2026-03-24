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

        // Kolom yang boleh di-sort (whitelist biar aman)
        $allowedSorts = ['code_tools', 'name', 'serial_number', 'replacement_value', 'status'];
        $sortBy = in_array($request->sort_by, $allowedSorts) ? $request->sort_by : 'code_tools';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';

        $search = $request->search;

        $tools = Tools::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code_tools', 'like', "%{$search}%")
                        ->orWhere('serial_number', 'like', "%{$search}%");
                });
            })
            // Sort by category name perlu join
            ->when(
                $sortBy === 'category',
                function ($query) use ($sortDir) {
                    $query->leftJoin('categories', 'tools.category_id', '=', 'categories.id')->orderBy('categories.name', $sortDir)->select('tools.*');
                },
                function ($query) use ($sortBy, $sortDir) {
                    $query->orderBy($sortBy, $sortDir);
                },
            )
            ->paginate($perPage)
            ->withQueryString(); // supaya query string (search, sort, per_page) ikut di pagination link

        $categories = Categories::get();

        return view('master.tools', compact('tools', 'categories'));
    }

    public function masterToolsStore(Request $request)
    {
        $uuid = Str::uuid();

        $category = Categories::findOrFail($request->category);
        $words = explode(' ', strtoupper($category->name));

        if (count($words) > 1) {
            $prefix = substr($words[0], 0, 2) . substr($words[1], 0, 2);
        } else {
            $prefix = substr($words[0], 0, 4);
        }

        $count = Tools::where('category_id', $category->id)->count() + 1;
        $number = str_pad($count, 3, '0', STR_PAD_LEFT);
        $code = $prefix . '-' . $number;
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
