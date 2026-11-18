<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::pluck('name');
        return view('admin.users', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $data = $request->validate(['role' => 'required|in:admin,petugas,siswa']);
        $user->syncRoles([$data['role']]);
        return back()->with('status', 'Role diperbarui.');
    }
}