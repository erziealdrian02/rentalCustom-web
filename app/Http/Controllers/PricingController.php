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

    public function masterPricingStore(Request $request)
    {
        $request->validate([
            'toolId' => 'required|integer',
            'dailyRate' => 'required|numeric|min:0',
            'weeklyRate' => 'required|numeric|min:0',
            'monthlyRate' => 'required|numeric|min:0',
        ]);

        $tools = $this->getTools();
        $tool = collect($tools)->firstWhere('id', (int) $request->toolId);

        $pricing = $this->getPricing();
        $maxId = count($pricing) ? max(array_column($pricing, 'id')) : 0;
        $pricing[] = [
            'id' => $maxId + 1,
            'toolId' => (int) $request->toolId,
            'toolName' => $tool ? $tool['name'] : 'Unknown',
            'dailyRate' => (float) $request->dailyRate,
            'weeklyRate' => (float) $request->weeklyRate,
            'monthlyRate' => (float) $request->monthlyRate,
        ];
        session(['pricing' => $pricing]);

        return redirect()->route('pricing.index')->with('success', 'Pricing added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'toolId' => 'required|integer',
            'dailyRate' => 'required|numeric|min:0',
            'weeklyRate' => 'required|numeric|min:0',
            'monthlyRate' => 'required|numeric|min:0',
        ]);

        $tools = $this->getTools();
        $tool = collect($tools)->firstWhere('id', (int) $request->toolId);

        $pricing = $this->getPricing();
        foreach ($pricing as &$price) {
            if ($price['id'] == $id) {
                $price['toolId'] = (int) $request->toolId;
                $price['toolName'] = $tool ? $tool['name'] : 'Unknown';
                $price['dailyRate'] = (float) $request->dailyRate;
                $price['weeklyRate'] = (float) $request->weeklyRate;
                $price['monthlyRate'] = (float) $request->monthlyRate;
                break;
            }
        }
        session(['pricing' => $pricing]);

        return redirect()->route('pricing.index')->with('success', 'Pricing updated successfully!');
    }

    public function destroy($id)
    {
        $pricing = $this->getPricing();
        $pricing = array_values(array_filter($pricing, fn($p) => $p['id'] != $id));
        session(['pricing' => $pricing]);

        return redirect()->route('pricing.index')->with('success', 'Pricing deleted successfully!');
    }
}
