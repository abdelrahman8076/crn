<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $role = Role::create([
                'name' => $validated['name'],
            ]);

            // Assign permissions
            if ($request->has('permission_ids') && !empty($request->permission_ids)) {
                $role->syncPermissions($request->permission_ids);
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $rolePermissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated, $request, $role) {
            $role->update([
                'name' => $validated['name'],
            ]);

            // Sync permissions
            if ($request->has('permission_ids')) {
                $role->syncPermissions($request->permission_ids ?? []);
            }
        });

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Check if role has users/admins assigned via model_roles table
        $assignedCount = DB::table('model_roles')->where('role_id', $role->id)->count();
        
        if ($assignedCount > 0) {
            return back()->with('error', 'Cannot delete role: it has users or admins assigned. Please reassign them first.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
