<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomersController extends Controller
{
    private function getCustomers()
    {
        return session('customers', [
            ['id' => 1, 'name' => 'John Doe',     'email' => 'john@example.com',  'phone' => '081234567890', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Jane Smith',   'email' => 'jane@example.com',  'phone' => '082345678901', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Bob Johnson',  'email' => 'bob@example.com',   'phone' => '083456789012', 'status' => 'Inactive'],
            ['id' => 4, 'name' => 'Alice Brown',  'email' => 'alice@example.com', 'phone' => '084567890123', 'status' => 'Active'],
        ]);
    }

    public function masterCustomers()
    {
        $customers = $this->getCustomers();
        return view('master.customers', compact('customers'));
    }
 
    public function masterCustomersStore(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|max:150',
            'phone'  => 'required|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);
 
        $customers   = $this->getCustomers();
        $maxId       = count($customers) ? max(array_column($customers, 'id')) : 0;
        $customers[] = [
            'id'     => $maxId + 1,
            'name'   => $request->name,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'status' => $request->status,
        ];
        session(['customers' => $customers]);
 
        return redirect()->route('customers.index')->with('success', 'Customer added successfully!');
    }
 
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|max:150',
            'phone'  => 'required|string|max:20',
            'status' => 'required|in:Active,Inactive',
        ]);
 
        $customers = $this->getCustomers();
        foreach ($customers as &$customer) {
            if ($customer['id'] == $id) {
                $customer['name']   = $request->name;
                $customer['email']  = $request->email;
                $customer['phone']  = $request->phone;
                $customer['status'] = $request->status;
                break;
            }
        }
        session(['customers' => $customers]);
 
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully!');
    }
 
    public function destroy($id)
    {
        $customers = $this->getCustomers();
        $customers = array_values(array_filter($customers, fn($c) => $c['id'] != $id));
        session(['customers' => $customers]);
 
        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully!');
    }
}
