<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Tools;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function masterPricing(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $tools = Tools::with('category')->paginate($perPage);
        $categories = Categories::get();

        return view('master.pricing', compact('tools'));
    }

    public function masterPricingUpdate(Request $request, $id_tools)
    {
        $model = Tools::findOrFail($id_tools);

        $model->daily_rate = (int) $request->daily_rate;
        $model->weekly_rate = (int) $request->weekly_rate;
        $model->monthly_rate = (int) $request->monthly_rate;

        $model->save();

        return redirect()->route('master.pricing')->with('success', 'Pricing updated successfully!');
    }
}
