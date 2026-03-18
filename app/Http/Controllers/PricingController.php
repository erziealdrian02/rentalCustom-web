<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PricingController extends Controller
{
    private function getPricing()
    {
        return session('pricing', [['id' => 1, 'toolId' => 1, 'toolName' => 'Angle Grinder', 'dailyRate' => 25.0, 'weeklyRate' => 150.0, 'monthlyRate' => 500.0], ['id' => 2, 'toolId' => 2, 'toolName' => 'Hammer', 'dailyRate' => 5.0, 'weeklyRate' => 30.0, 'monthlyRate' => 100.0], ['id' => 3, 'toolId' => 3, 'toolName' => 'Safety Helmet', 'dailyRate' => 3.0, 'weeklyRate' => 18.0, 'monthlyRate' => 60.0]]);
    }

    private function getTools()
    {
        // Ambil dari session tools supaya sinkron dengan halaman Tools
        return session('tools', [['id' => 1, 'name' => 'Angle Grinder'], ['id' => 2, 'name' => 'Hammer'], ['id' => 3, 'name' => 'Safety Helmet']]);
    }

    public function masterPricing()
    {
        $pricing = $this->getPricing();
        $tools = $this->getTools();
        return view('master.pricing', compact('pricing', 'tools'));
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
