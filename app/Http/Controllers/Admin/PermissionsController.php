<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    /**
     * Display a listing of permissions.
     */
    public function index()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        $groups = $permissions->groupBy('group');
        
        return view('admin.permissions.index', compact('permissions', 'groups'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();
        return view('admin.permissions.create', compact('groups'));
    }

    /**
     * Store a newly created permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Permission::create($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(Permission $permission)
    {
        $groups = Permission::distinct()->pluck('group')->filter()->sort()->values();
        return view('admin.permissions.edit', compact('permission', 'groups'));
    }

    /**
     * Update the specified permission.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'group' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $permission->update($validated);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission.
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to roles or directly to users/admins
        $roleAssignments = $permission->roles()->count();
        $directAssignments = \Illuminate\Support\Facades\DB::table('model_permissions')
            ->where('permission_id', $permission->id)
            ->count();
        
        if ($roleAssignments > 0 || $directAssignments > 0) {
            return back()->with('error', 'Cannot delete permission: it is assigned to roles or users/admins. Please remove assignments first.');
        }

        $permission->delete();

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
