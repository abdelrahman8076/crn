<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller; // ✅ Make sure this line exists
use App\Models\User;
use App\Services\DataTables\BaseDataTable;
use App\Traits\ChecksPermissions;



class AdminController extends Controller
{
    use ChecksPermissions;


    public function index()
    {
        // Allow Managers to access without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $isManager = $user && strtolower($user->role?->name ?? '') === 'manager';
        
        if (!$isManager) {
            $this->requirePermission('view-admins');
        }
        
        $columns = ['id', 'name', 'email'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.admins.index', compact('columns', 'renderComponents', 'customActionsView'));
    }
    public function data(Request $request)
    {
        try {
            // Allow Managers to access without permission check
            $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
            $isManager = $user && strtolower($user->role?->name ?? '') === 'manager';
            
            if (!$isManager && !$this->checkPermission('view-admins')) {
                return response()->json([
                    'draw' => (int) $request->get('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'You do not have permission to view admins.'
                ], 200);
            }

            $query = Admin::query();
            $columns = ['id', 'name', 'email'];

            $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
            // Optional: send extra props to the view (e.g. routeName)
            $service->setActionProps([
                'routeName' => 'admin.admin',
                'deleteFlag'=> true
            ]);
            return $service->make($request);
        } catch (\Exception $e) {
            \Log::error('AdminController data method error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 200);
        }
    }


    public function create()
    {
        $this->requirePermission('create-admins');
        
        // Only show Manager and Sales roles for selection
        $roles = Role::whereIn('name', ['Manager', 'Sales'])->get();
        $permissions = Permission::whereIn('slug', [
            // Users
            'view-users', 'create-users', 'edit-users', 'delete-users',
            // Admins
            'view-admins', 'create-admins', 'edit-admins', 'delete-admins',
            // Access
            'access-admin', 'access-user-side',
            // Dashboard & Reports
            'view-dashboard', 'view-reports',
            // Leads
            'view-leads', 'create-leads', 'edit-leads', 'delete-leads',
            // Deals
            'view-deals', 'create-deals', 'edit-deals', 'delete-deals',
            // Tasks
            'view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks'
        ])->get()->groupBy(function ($permission) {
            // Group by module
            $parts = explode('-', $permission->slug);
            $action = $parts[0] ?? '';
            $module = $parts[1] ?? 'Other';
            
            // Separate 'users' and 'admins' into different modules
            if ($module === 'users' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Users';
            } elseif ($module === 'admins' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Admins';
            } elseif (in_array($permission->slug, ['access-admin', 'access-user-side'])) {
                return 'Access';
            } elseif (in_array($permission->slug, ['view-dashboard', 'view-reports'])) {
                return 'Dashboard & Reports';
            } elseif ($module === 'leads' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Leads';
            } elseif ($module === 'deals' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Deals';
            } elseif ($module === 'tasks' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Tasks';
            }
            return 'Other';
        });
        return view('admin.admins.create', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $this->requirePermission('create-admins');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role_id' => $validated['role_id'],
            ]);

            // Always sync permissions (even if empty array to remove all permissions)
            $role = Role::findOrFail($validated['role_id']);
            $permissions = $request->input('permissions', []);
            // Filter out any empty values and ensure we have an array
            $permissions = is_array($permissions) ? array_filter($permissions, fn($p) => !empty($p)) : [];
            $role->permissions()->sync($permissions);
        });

        return redirect()->route('admin.admin.index')->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin)
    {
        $this->requirePermission('edit-admins');
        
        // Only show Manager and Sales roles for selection
        $roles = Role::whereIn('name', ['Manager', 'Sales'])->get();
        $permissions = Permission::whereIn('slug', [
            // Users
            'view-users', 'create-users', 'edit-users', 'delete-users',
            // Admins
            'view-admins', 'create-admins', 'edit-admins', 'delete-admins',
            // Access
            'access-admin', 'access-user-side',
            // Dashboard & Reports
            'view-dashboard', 'view-reports',
            // Leads
            'view-leads', 'create-leads', 'edit-leads', 'delete-leads',
            // Deals
            'view-deals', 'create-deals', 'edit-deals', 'delete-deals',
            // Tasks
            'view-tasks', 'create-tasks', 'edit-tasks', 'delete-tasks'
        ])->get()->groupBy(function ($permission) {
            // Group by module
            $parts = explode('-', $permission->slug);
            $action = $parts[0] ?? '';
            $module = $parts[1] ?? 'Other';
            
            // Separate 'users' and 'admins' into different modules
            if ($module === 'users' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Users';
            } elseif ($module === 'admins' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Admins';
            } elseif (in_array($permission->slug, ['access-admin', 'access-user-side'])) {
                return 'Access';
            } elseif (in_array($permission->slug, ['view-dashboard', 'view-reports'])) {
                return 'Dashboard & Reports';
            } elseif ($module === 'leads' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Leads';
            } elseif ($module === 'deals' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Deals';
            } elseif ($module === 'tasks' && in_array($action, ['view', 'create', 'edit', 'delete'])) {
                return 'Tasks';
            }
            return 'Other';
        });
        $rolePermissions = $admin->role ? $admin->role->permissions->pluck('id')->toArray() : [];
        
        return view('admin.admins.edit', compact('admin', 'roles', 'permissions', 'rolePermissions'));
    }
    public function update(Request $request, Admin $admin)
    {
        $this->requirePermission('edit-admins');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        DB::transaction(function () use ($request, $admin, &$validated) {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $admin->update($validated);

            // Always sync permissions (even if empty array to remove all permissions)
            $role = Role::findOrFail($validated['role_id']);
            $permissions = $request->input('permissions', []);
            // Filter out any empty values and ensure we have an array
            $permissions = is_array($permissions) ? array_filter($permissions, fn($p) => !empty($p)) : [];
            $role->permissions()->sync($permissions);
        });

        return redirect()->route('admin.admin.index')->with('success', 'Admin Updated success');
    }

    public function destroy(Admin $admin)
    {
        $this->requirePermission('delete-admins');
        
        $admin->delete();
        return redirect()->route('admin.admin.index')->with('success', 'Admin deleted successfully.');
    }
}
