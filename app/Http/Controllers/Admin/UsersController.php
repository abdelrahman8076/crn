<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Target;
use App\Models\Role;
use App\Models\Position;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Services\DataTables\BaseDataTable;
use App\Traits\HasAccessFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsersController extends Controller
{
    use HasAccessFilter;

    public function index()
    {
        $columns = ['id', 'name', 'email', 'created_at'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.users.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    public function data(Request $request)
    {
        $query = User::query();
        $query = $this->filterAccess($query);

        $columns = ['id', 'name', 'email', 'created_at'];

        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps([
            'routeName' => 'admin.users',
            'deleteFlag' => true
        ]);

        return $service->make($request);
    }

    public function create()
    {
        $roles = Role::all();
        $users = User::all();
        $positions = Position::orderBy('level')->orderBy('sort_order')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'users', 'positions', 'permissions'));
    }
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'role_id' => 'required|exists:roles,id', // Legacy role_id for backward compatibility
        'role_ids' => 'nullable|array',
        'role_ids.*' => 'exists:roles,id',
        'position_id' => 'nullable|exists:positions,id',
        'manager_id' => 'nullable|exists:users,id',
        'permission_ids' => 'nullable|array',
        'permission_ids.*' => 'exists:permissions,id',
        'targets.*.amount' => 'nullable|integer|min:1',
        'targets.*.period' => 'required_with:targets.*.amount|string',
    ]);

    DB::transaction(function () use ($request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id, // Keep for backward compatibility
            'manager_id' => $request->manager_id,
            'position_id' => $request->position_id,
        ]);

        // Assign multiple roles using new system
        if ($request->has('role_ids') && !empty($request->role_ids)) {
            $user->syncRoles($request->role_ids);
        } else {
            // Fallback: assign single role from role_id
            $user->assignRole($request->role_id);
        }

        // Assign direct permissions
        if ($request->has('permission_ids') && !empty($request->permission_ids)) {
            $user->syncPermissions($request->permission_ids);
        }

        if ($request->has('targets')) {
            foreach ($request->targets as $targetData) {
                if (!empty($targetData['amount'])) {
                    // This calls your existing recursive logic
                    $this->assignManagerTarget($user, $targetData['amount'], $targetData['period']);
                }
            }
        }
    });

    return redirect()->route('admin.users.index')->with('success', __('users.created_successfully'));
}

    public function edit($id)
    {
        // Load ALL targets ordered by period to show history in the view
        $user = User::with(['targets' => function ($q) {
            $q->orderBy('period', 'desc');
        }])->findOrFail($id);

        if (!$this->canAccess($user)) {
            return redirect()->route('admin.users.index')->with('error', 'No Access.');
        }

        $roles = Role::all();
        $users = User::where('id', '!=', $user->id)->get();
        $positions = Position::orderBy('level')->orderBy('sort_order')->get();
        $permissions = Permission::orderBy('group')->orderBy('name')->get();
        
        // Get user's assigned roles and permissions
        $userRoleIds = $user->modelRoles()->pluck('role_id')->toArray();
        $userPermissionIds = $user->modelPermissions()->pluck('permission_id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'users', 'positions', 'permissions', 'userRoleIds', 'userPermissionIds'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$this->canAccess($user)) {
            return redirect()->route('admin.users.index')->with('error', 'Unauthorized.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id', // Legacy
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:roles,id',
            'position_id' => 'nullable|exists:positions,id',
            'manager_id' => 'nullable|exists:users,id|not_in:' . $id,
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
            'target_total' => 'nullable|integer|min:0',
            'target_period' => 'required_with:target_total|string',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->role_id = $request->role_id; // Keep for backward compatibility
            $user->manager_id = $request->manager_id;
            $user->position_id = $request->position_id;

            if ($request->password) {
                $user->password = bcrypt($request->password);
            }
            $user->save();

            // Sync roles using new system
            if ($request->has('role_ids') && !empty($request->role_ids)) {
                $user->syncRoles($request->role_ids);
            } else {
                // Fallback: sync single role
                $user->syncRoles([$request->role_id]);
            }

            // Sync permissions
            if ($request->has('permission_ids')) {
                $user->syncPermissions($request->permission_ids ?? []);
            }

            if ($request->filled('target_total')) {
                $this->assignManagerTarget($user, $request->target_total, $request->target_period);
            }
        });

        return redirect()->route('admin.users.index')->with('success', __('users.updated_successfully'));
    }

    /**
     * Logic: Assigns target to Manager and auto-distributes to their sales team.
     */
    public function assignManagerTarget(User $manager, int $managerTarget, string $period)
    {
        // 1. Update/Create target for the Manager
        $this->assignTarget($manager, $managerTarget, $period);

        // 2. Distribute to Sales Team
        $sales = $manager->salesTeam; 

        if ($sales && $sales->count() > 0) {
            $salesCount = $sales->count();
            $individualTarget = intval($managerTarget / $salesCount);

            foreach ($sales as $sale) {
                $this->assignTarget($sale, $individualTarget, $period);
            }
        }
    }

    /**
     * Core logic: Supports multiple targets by using 'period' as a unique key per user.
     */
    protected function assignTarget(User $user, int $value, string $period)
    {
        // Determine if this target should be the active one (Current month)
        $currentMonth = Carbon::now()->format('Y-m');
        $isActive = ($period === $currentMonth);

        // If we are setting a new active target, deactivate others
        if ($isActive) {
            Target::where('user_id', $user->id)->update(['is_active' => false]);
        }

        // updateOrCreate ensures we don't have two records for '2024-05' for the same user
        return Target::updateOrCreate(
            [
                'user_id' => $user->id,
                'period'  => $period,
            ],
            [
                'target_total'     => $value,
                'target_remaining' => $value, // Logic: reset remaining on update, or adjust as needed
                'is_active'        => $isActive,
            ]
        );
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if (!$this->canAccess($user)) return back()->with('error', 'Unauthorized');

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User Deleted');
    }

    protected function canAccess(User $user): bool
    {
        if (Auth::guard('admin')->check()) return true;
        return $this->filterAccess(User::where('id', $user->id))->exists();
    }
}