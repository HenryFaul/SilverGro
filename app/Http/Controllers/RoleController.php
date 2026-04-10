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

        $request->validate([
            'is_active'     => ['required', 'boolean'],
            'permissions'   => ['present', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        $role->is_active = $request->is_active;
        $role->save();
        $role->syncPermissions($request->permissions);

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Role saved');

        return redirect()->back();
    }
}
