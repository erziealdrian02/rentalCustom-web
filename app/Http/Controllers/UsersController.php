<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    private function getUsers()
    {
        return session('system_users', [['id' => 1, 'name' => 'Admin Utama', 'email' => 'admin@example.com', 'role' => 'Administrator', 'status' => 'Active'], ['id' => 2, 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'role' => 'Warehouse Manager', 'status' => 'Active'], ['id' => 3, 'name' => 'Citra Dewi', 'email' => 'citra@example.com', 'role' => 'Staff', 'status' => 'Active'], ['id' => 4, 'name' => 'Dodi Pratama', 'email' => 'dodi@example.com', 'role' => 'Clerk', 'status' => 'Inactive']]);
    }

    public function masterUsers()
    {
        $users = $this->getUsers();
        return view('master.users', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'role' => 'required|in:Administrator,Warehouse Manager,Staff,Clerk',
            'status' => 'required|in:Active,Inactive',
        ]);

        $users = $this->getUsers();
        $maxId = count($users) ? max(array_column($users, 'id')) : 0;
        $users[] = [
            'id' => $maxId + 1,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ];
        session(['system_users' => $users]);

        return redirect()->route('users.index')->with('success', 'User added successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'role' => 'required|in:Administrator,Warehouse Manager,Staff,Clerk',
            'status' => 'required|in:Active,Inactive',
        ]);

        $users = $this->getUsers();
        foreach ($users as &$user) {
            if ($user['id'] == $id) {
                $user['name'] = $request->name;
                $user['email'] = $request->email;
                $user['role'] = $request->role;
                $user['status'] = $request->status;
                break;
            }
        }
        session(['system_users' => $users]);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    public function destroy($id)
    {
        $users = $this->getUsers();
        $users = array_values(array_filter($users, fn($u) => $u['id'] != $id));
        session(['system_users' => $users]);

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }
}
