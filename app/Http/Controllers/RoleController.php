<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $all_permissions = Permission::orderBy('name')->get(['id', 'name']);

        return inertia('Role/Index', [
            'roles'           => $roles,
            'all_permissions' => $all_permissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->can('manage_users')) {
            return to_route('no_permission');
        }

        $request->validate(['is_active' => ['required', 'boolean']]);

        $role->is_active = $request->is_active;
        $role->save();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Role updated');

        return redirect()->back();
    }

    public function togglePermission(Request $request, Role $role)
    {
        if (!auth()->user()->can('manage_users')) {
            return to_route('no_permission');
        }

        $request->validate(['permission_name' => ['required', 'string', 'exists:permissions,name']]);

        if ($role->hasPermissionTo($request->permission_name)) {
            $role->revokePermissionTo($request->permission_name);
        } else {
            $role->givePermissionTo($request->permission_name);
        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Permission updated');

        return redirect()->back();
    }
}
