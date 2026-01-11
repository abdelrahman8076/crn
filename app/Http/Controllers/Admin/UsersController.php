<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\DataTables\BaseDataTable;
use App\Traits\HasAccessFilter;
use Illuminate\Support\Facades\Auth;
use \App\Models\Role;

class UsersController extends Controller
{
    use HasAccessFilter;

    /**
     * Display the user table view.
     */
    public function index()
    {
        $columns = ['id', 'name', 'email', 'created_at'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.users.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    /**
     * DataTables API
     */
    public function data(Request $request)
    {
        $query = User::query();

        // Apply access filter (from trait)
        $query = $this->filterAccess($query);

        $columns = ['id', 'name', 'email', 'created_at'];

        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps([
            'routeName' => 'admin.users',
            'deleteFlag' => true

        ]);

        return $service->make($request);
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $roles = Role::all();
$users = User::where('role_id', 1)->get();        return view('admin.users.create', compact('roles', 'users'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $users = User::where('id', '!=', $user->id)->get(); // exclude self from manager dropdown

        if (!$this->canAccess($user)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have access to this user.');
        }

        return view('admin.users.edit', compact('user', 'roles', 'users'));
    }



    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'manager_id' => 'nullable|exists:users,id|not_in:' . Auth::id(), // cannot choose self
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $request->role_id,
            'manager_id' => $request->manager_id,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$this->canAccess($user)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You do not have access to update this user.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$id}",
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'manager_id' => 'nullable|exists:users,id|not_in:' . $id, // cannot assign self
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->manager_id = $request->manager_id;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Delete user.
     */
public function destroy($id)
{
    // Prevent the currently logged-in Admin from deleting themselves
    if (auth('admin')->check() && auth('admin')->id() == $id) {
        return redirect()->back()->with('error', 'You cannot delete your own account while logged in.');
    }

    $user = User::findOrFail($id);

    // Your existing access check
    if (!$this->canAccess($user)) {
        return redirect()->route('admin.users.index')
            ->with('error', 'You do not have access to delete this user.');
    }

    // This will trigger the 'deleting' boot method we added to the model
    $user->delete();

    return redirect()->route('admin.users.index')
        ->with('success', 'User and related data handled successfully');
}

    /**
     * Check access helper (flash error compatible).
     */
    // protected function canAccess(User $user): bool
    // {
    //     // Admins can access everything
    //     if (Auth::guard('admin')->check()) {
    //         return true;
    //     }

    //     // Use your trait filter for managers/sales
    //     $filtered = $this->filterAccess(User::where('id', $user->id), 'assigned_to')->exists();
    //     return $filtered;
    // }
}
