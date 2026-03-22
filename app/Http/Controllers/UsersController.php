<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    private function getUsers()
    {
        return session('system_users', [['id' => 1, 'name' => 'Admin Utama', 'email' => 'admin@example.com', 'role' => 'Administrator', 'status' => 'Active'], ['id' => 2, 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'role' => 'Warehouse Manager', 'status' => 'Active'], ['id' => 3, 'name' => 'Citra Dewi', 'email' => 'citra@example.com', 'role' => 'Staff', 'status' => 'Active'], ['id' => 4, 'name' => 'Dodi Pratama', 'email' => 'dodi@example.com', 'role' => 'Clerk', 'status' => 'Inactive']]);
    }

    public function masterUsers(Request $request)
    {
        $perPage = in_array($request->per_page, [10, 50, 100]) ? $request->per_page : 10;

        $users = User::paginate($perPage);

        return view('master.users', compact('users'));
    }

    public function masterUsersStore(Request $request)
    {
        $model = new User();
        $model->name = $request->name;
        $model->email = $request->email;
        $model->full_name = $request->fullname;
        $model->phone = $request->phone;
        $model->password = bcrypt('12345678');
        $model->role = strtolower($request->role);
        $model->status = strtolower($request->status);

        $model->save();

        return redirect()->route('master.users')->with('success', 'User added successfully!');
    }

    public function masterUsersUpdate(Request $request, $id)
    {
        $model = User::findOrFail($id);
        $model->name = $request->name;
        $model->email = $request->email;
        $model->full_name = $request->fullname;
        $model->phone = $request->phone;
        $model->role = strtolower($request->role);
        $model->status = strtolower($request->status);

        $model->save();

        return redirect()->route('master.users')->with('success', 'User updated successfully!');
    }

    public function masterUsersReset($id)
    {
        $model = User::findOrFail($id);
        $model->password = bcrypt('12345678');
        $model->save();

        return redirect()->route('master.users')->with('success', 'User password reset successfully!');
    }

    public function masterUsersDestroy($id)
    {
        $model = User::findOrFail($id);
        $model->delete();

        return redirect()->route('master.users')->with('success', 'User deleted successfully!');
    }
}
