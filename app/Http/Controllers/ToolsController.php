<?php

namespace App\Http\Controllers;

use App\Models\Tools;
use Illuminate\Http\Request;

class ToolsController extends Controller
{
    private function getTools()
    {
        return session('tools', [
            [
                'id' => 1,
                'code' => 'DRILL001',
                'name' => 'Power Drill',
                'category' => 'Power Tools',
                'serialNumber' => 'SN-2024-001',
                'replacementValue' => 150,
                'status' => 'Available',
            ],
            [
                'id' => 2,
                'code' => 'SAW002',
                'name' => 'Circular Saw',
                'category' => 'Power Tools',
                'serialNumber' => 'SN-2024-002',
                'replacementValue' => 200,
                'status' => 'Rented',
            ],
            [
                'id' => 3,
                'code' => 'SAND003',
                'name' => 'Orbital Sander',
                'category' => 'Power Tools',
                'serialNumber' => 'SN-2024-003',
                'replacementValue' => 120,
                'status' => 'Available',
            ],
            [
                'id' => 4,
                'code' => 'IMPACT004',
                'name' => 'Impact Driver',
                'category' => 'Power Tools',
                'serialNumber' => 'SN-2024-004',
                'replacementValue' => 180,
                'status' => 'Damaged',
            ],
            [
                'id' => 5,
                'code' => 'LADDER005',
                'name' => 'Extension Ladder',
                'category' => 'Safety Equipment',
                'serialNumber' => 'SN-2024-005',
                'replacementValue' => 250,
                'status' => 'Available',
            ],
            [
                'id' => 6,
                'code' => 'WRENCH006',
                'name' => 'Socket Wrench Set',
                'category' => 'Hand Tools',
                'serialNumber' => 'SN-2024-006',
                'replacementValue' => 95,
                'status' => 'Rented',
            ],
            [
                'id' => 7,
                'code' => 'LEVEL007',
                'name' => 'Digital Level',
                'category' => 'Measurement Tools',
                'serialNumber' => 'SN-2024-007',
                'replacementValue' => 85,
                'status' => 'Available',
            ],
            [
                'id' => 8,
                'code' => 'GRINDER008',
                'name' => 'Angle Grinder',
                'category' => 'Power Tools',
                'serialNumber' => 'SN-2024-008',
                'replacementValue' => 220,
                'status' => 'Lost',
            ],
        ]);
    }

    public function masterTools()
    {
        $tools = $this->getTools();

        return view('master.tools', compact('tools'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'             => 'required|string|max:50',
            'name'             => 'required|string|max:100',
            'category'         => 'required|string',
            'serialNumber'     => 'required|string|max:100',
            'replacementValue' => 'required|numeric|min:0',
            'status'           => 'required|string',
        ]);
 
        $tools   = $this->getTools();
        $maxId   = count($tools) ? max(array_column($tools, 'id')) : 0;
        $tools[] = [
            'id'               => $maxId + 1,
            'code'             => $request->code,
            'name'             => $request->name,
            'category'         => $request->category,
            'serialNumber'     => $request->serialNumber,
            'replacementValue' => (int) $request->replacementValue,
            'status'           => $request->status,
        ];
        session(['tools' => $tools]);
 
        return redirect()->route('tools.index')->with('success', 'Tool added successfully!');
    }
 
    public function update(Request $request, $id)
    {
        $request->validate([
            'code'             => 'required|string|max:50',
            'name'             => 'required|string|max:100',
            'category'         => 'required|string',
            'serialNumber'     => 'required|string|max:100',
            'replacementValue' => 'required|numeric|min:0',
            'status'           => 'required|string',
        ]);
 
        $tools = $this->getTools();
        foreach ($tools as &$tool) {
            if ($tool['id'] == $id) {
                $tool['code']             = $request->code;
                $tool['name']             = $request->name;
                $tool['category']         = $request->category;
                $tool['serialNumber']     = $request->serialNumber;
                $tool['replacementValue'] = (int) $request->replacementValue;
                $tool['status']           = $request->status;
                break;
            }
        }
        session(['tools' => $tools]);
 
        return redirect()->route('tools.index')->with('success', 'Tool updated successfully!');
    }
 
    public function destroy($id)
    {
        $tools = $this->getTools();
        $tools = array_values(array_filter($tools, fn($t) => $t['id'] != $id));
        session(['tools' => $tools]);
 
        return redirect()->route('tools.index')->with('success', 'Tool deleted successfully!');
    }
}
