<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuPermission;
use App\Models\User;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    public function index()
    {
        $users = User::query()->select('id','name','email','role','is_protected')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $menuKeys = config('menu.keys', []);
        $granted = $user->menuPermissions()->pluck('menu_key')->toArray();
        return view('admin.users.edit_permissions', compact('user','menuKeys','granted'));
    }

    public function create()
    {
        $menuKeys = config('menu.keys', []);
        return view('admin.users.create', compact('menuKeys'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,staff,supervisor',
            'menus' => 'array',
            'menus.*' => 'string',
        ]);

        $superEmail = config('permissions.super_admin_email', 'admin@gmail.com');
        $isSuper = strcasecmp($data['email'], $superEmail) === 0;

        // Enforce single super admin; otherwise accept staff/supervisor only
        $role = $isSuper ? 'admin' : (in_array($data['role'], ['staff','supervisor']) ? $data['role'] : 'staff');

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => $role,
            'is_protected' => $isSuper,
        ]);

        if ($role !== 'admin') {
            $keys = collect($data['menus'] ?? [])->unique()->values();
            foreach ($keys as $key) {
                MenuPermission::firstOrCreate([
                    'user_id' => $user->id,
                    'menu_key' => $key,
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|in:admin,staff,supervisor',
            'menus' => 'array',
            'menus.*' => 'string',
        ]);

        $superEmail = config('permissions.super_admin_email', 'admin@gmail.com');
        $isSuper = strcasecmp($user->email, $superEmail) === 0;

        // Enforce single super admin: only the configured email can be admin
        if ($isSuper) {
            $user->role = 'admin';
            $user->is_protected = true;
        } else {
            // Non-super users cannot be admin; allow staff/supervisor
            $user->role = in_array($data['role'], ['staff','supervisor']) ? $data['role'] : 'staff';
        }
        $user->save();

        // Admins implicitly have all; clear specific rows
        $user->menuPermissions()->delete();
        if ($user->role !== 'admin') {
            $keys = collect($data['menus'] ?? [])->unique()->values();
            foreach ($keys as $key) {
                MenuPermission::firstOrCreate([
                    'user_id' => $user->id,
                    'menu_key' => $key,
                ]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'Permissions updated.');
    }

    public function destroy(User $user)
    {
        if ($user->is_protected) {
            return back()->withErrors(['error' => 'Protected user cannot be deleted.']);
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
