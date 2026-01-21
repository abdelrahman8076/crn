<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Target;
use App\Models\Role;
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
    public function activateTarget(Target $target)
    {
        DB::transaction(function () use ($target) {
            // 1. Set all targets for this specific user to inactive
            Target::where('user_id', $target->user_id)
                ->update(['is_active' => false]);

            // 2. Set the selected target to active
            $target->update(['is_active' => true]);
        });

        return back()->with('success', __('users.target_activated_successfully'));
    }
    public function updateTarget(Request $request, Target $target)
    {
        $validated = $request->validate([
            'target_total' => 'required|numeric|min:0',
            'period' => 'required|date',
        ]);

       // Optional: Recalculate remaining if total changed
        $diff = $validated['target_total'] - $target->target_total;
        $target->target_remaining += $diff;

        $target->update([
            'target_total' => $validated['target_total'],
            'period' => $validated['period'],
        ]);

        return back()->with('success', __('users.target_updated_successfully'));
    }
    public function storeTarget(Request $request, User $user)
{
    // 1. Validation
    $validated = $request->validate([
        'target_total'  => 'required|numeric|min:0',
        'target_period' => 'required|date',
    ]);

    // 2. Create the target
    // We set target_remaining equal to target_total initially
    $user->targets()->create([
        'target_total'     => $validated['target_total'],
        'target_remaining' => $validated['target_total'],
        'period'           => $validated['target_period'],
        'is_active'        => false, // New targets are inactive by default
    ]);

    // 3. Redirect back with success message
    return back()->with('success', __('users.target_added_successfully'));
}

    public function create()
    {
        $roles = Role::all();
        $users = User::all();
        return view('admin.users.create', compact('roles', 'users'));
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

        return view('admin.users.edit', compact('user', 'roles', 'users'));
    }
    // --- STORE METHOD ---
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'role_id' => 'required|exists:roles,id',
        'manager_id' => 'nullable|exists:users,id',
        'targets.*.amount' => 'nullable|numeric|min:1',
        'targets.*.period' => 'required_with:targets.*.amount|string',
    ]);

    $role = Role::findOrFail($request->role_id);
    $roleName = strtolower($role->name);

    DB::transaction(function () use ($request, $roleName) {
        $managerId = in_array($roleName, ['admin', 'manager']) ? null : $request->manager_id;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'manager_id' => $managerId,
        ]);

        // Trigger target reflection logic
        if (!in_array($roleName, ['admin', 'manager']) && $request->has('targets')) {
            foreach ($request->targets as $targetData) {
                if (!empty($targetData['amount'])) {
                    $this->assignManagerTarget($user, $targetData['amount'], $targetData['period']);
                }
            }
        }
    });

    return redirect()->route('admin.users.index')->with('success', __('users.created_successfully'));
}

// --- UPDATE METHOD ---
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => "required|email|unique:users,email,{$id}",
        'password' => 'nullable|min:6|confirmed',
        'role_id' => 'required|exists:roles,id',
        'manager_id' => 'nullable|exists:users,id|not_in:' . $id,
        'targets.*.amount' => 'nullable|numeric|min:0',
        'targets.*.period' => 'required_with:targets.*.amount|string',
    ]);

    $role = Role::findOrFail($request->role_id);
    $roleName = strtolower($role->name);

    DB::transaction(function () use ($request, $user, $roleName) {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->manager_id = in_array($roleName, ['admin', 'manager']) ? null : $request->manager_id;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        // Trigger target reflection logic
        if (!in_array($roleName, ['admin', 'manager']) && $request->has('targets')) {
            foreach ($request->targets as $targetData) {
                if (!empty($targetData['amount'])) {
                    $this->assignManagerTarget($user, $targetData['amount'], $targetData['period']);
                }
            }
        }
    });

    return redirect()->route('admin.users.index')->with('success', __('users.updated_successfully'));
}
protected function assignManagerTarget($user, $amount, $period)
{
    // 1. Update/Create the target for the current user
    $user->targets()->updateOrCreate(
        ['period' => $period],
        [
            'target_total' => $amount,
            'target_remaining' => $amount // Set remaining equal to total initially
        ]
    );

    // 2. Reflect up to the Manager
    if ($user->manager_id) {
        $manager = User::find($user->manager_id);
        
        if ($manager) {
            // Calculate sum of team targets for this period
            $teamTotal = \App\Models\Target::whereHas('user', function($query) use ($manager) {
                $query->where('manager_id', $manager->id);
            })
            ->where('period', $period)
            ->sum('target_total');

            // Update manager's target and set their remaining amount as well
            $manager->targets()->updateOrCreate(
                ['period' => $period],
                [
                    'target_total' => $teamTotal,
                    'target_remaining' => $teamTotal // Update manager's remaining total
                ]
            );

            if ($manager->manager_id) {
                $this->assignManagerTarget($manager, $teamTotal, $period);
            }
        }
    }
}


    /**
     * Logic: Assigns target to Manager and auto-distributes to their sales team.
     */


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
