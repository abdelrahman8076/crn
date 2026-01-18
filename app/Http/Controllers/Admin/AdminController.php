<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Position;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; // ✅ Make sure this line exists
use App\Models\User;
use App\Services\DataTables\BaseDataTable;



class AdminController extends Controller
{


    public function index()
    {
        $columns = ['id', 'name', 'email'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.admins.index', compact('columns', 'renderComponents', 'customActionsView'));
    }
        public function data(Request $request)
    {
        $query = Admin::query();
        $columns = ['id', 'name', 'email'];

        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        // Optional: send extra props to the view (e.g. routeName)
        $service->setActionProps([
            'routeName' => 'admin.admin',
            'deleteFlag'=> true
        ]);
        return $service->make($request);
    }


    public function create()
    {
        $roles = Role::all();
        $positions = Position::orderBy('level')->orderBy('sort_order')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        return view('admin.admins.create', compact('roles', 'positions', 'permissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
            'position_id' => 'nullable|exists:positions,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'position_id' => $validated['position_id'] ?? null,
            ]);

            // Assign roles
            if ($request->has('role_ids') && !empty($request->role_ids)) {
                $admin->syncRoles($request->role_ids);
            }

            // Assign direct permissions
            if ($request->has('permission_ids') && !empty($request->permission_ids)) {
                $admin->syncPermissions($request->permission_ids);
            }
        });

        return redirect()->route('admin.admin.index')->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin)
    {
        $roles = Role::all();
        $positions = Position::orderBy('level')->orderBy('sort_order')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        // Get admin's assigned roles and permissions
        $adminRoleIds = $admin->modelRoles()->pluck('role_id')->toArray();
        $adminPermissionIds = $admin->modelPermissions()->pluck('permission_id')->toArray();

        return view('admin.admins.edit', compact('admin', 'roles', 'positions', 'permissions', 'adminRoleIds', 'adminPermissionIds'));
    }
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:6|confirmed',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
            'position_id' => 'nullable|exists:positions,id',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($validated, $request, $admin) {
            $admin->name = $validated['name'];
            $admin->email = $validated['email'];
            $admin->position_id = $validated['position_id'] ?? null;

            if (!empty($validated['password'])) {
                $admin->password = Hash::make($validated['password']);
            }

            $admin->save();

            // Sync roles
            if ($request->has('role_ids')) {
                $admin->syncRoles($request->role_ids ?? []);
            }

            // Sync permissions
            if ($request->has('permission_ids')) {
                $admin->syncPermissions($request->permission_ids ?? []);
            }
        });

        return redirect()->route('admin.admin.index')->with('success', 'Admin Updated success');
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.admin.index')->with('success', 'Admin deleted successfully.');
    }
}
