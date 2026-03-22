<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomersController extends Controller
{
    private function getCustomers()
    {
        return session('customers', [['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '081234567890', 'status' => 'Active'], ['id' => 2, 'name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '082345678901', 'status' => 'Active'], ['id' => 3, 'name' => 'Bob Johnson', 'email' => 'bob@example.com', 'phone' => '083456789012', 'status' => 'Inactive'], ['id' => 4, 'name' => 'Alice Brown', 'email' => 'alice@example.com', 'phone' => '084567890123', 'status' => 'Active']]);
    }

    public function masterCustomers(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $customers = Customers::paginate($perPage);

        return view('master.customers', compact('customers'));
    }

    public function masterCustomersStore(Request $request)
    {
        $uuid = Str::uuid();

        $model = new Customers();
        $model->id = $uuid;
        $model->name = $request->name;
        $model->email = $request->email;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->postal_code = $request->postal_code;
        $model->country = $request->country;
        $model->phone = $request->phone;
        $model->status = $request->status;

        $model->save();

        return redirect()->route('master.customers')->with('success', 'Customer added successfully!');
    }

    public function masterCustomersUpdate(Request $request, $id)
    {
        $model = Customers::findOrFail($id);
        $model->name = $request->name;
        $model->email = $request->email;
        $model->address = $request->address;
        $model->city = $request->city;
        $model->postal_code = $request->postal_code;
        $model->country = $request->country;
        $model->phone = $request->phone;
        $model->status = $request->status;

        $model->save();

        return redirect()->route('master.customers')->with('success', 'Customer updated successfully!');
    }

    public function masterCustomersDestroy($id)
    {
        $model = Customers::findOrFail($id);

        $model->delete();

        return redirect()->route('master.customers')->with('success', 'Customer deleted successfully!');
    }
}
