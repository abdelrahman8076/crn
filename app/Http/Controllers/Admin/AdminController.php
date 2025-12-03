<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

        return view('admin.admin.index', compact('columns', 'renderComponents', 'customActionsView'));
    }
        public function data(Request $request)
    {
        $query = Admin::query();
        $columns = ['id', 'name', 'email'];

        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        // Optional: send extra props to the view (e.g. routeName)
        $service->setActionProps([
            'routeName' => 'admin.admin',
        ]);
        return $service->make($request);
    }


    public function create()
    {
        return view('admin.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('admin.admin.index')->with('success', 'Admin created successfully.');
    }

    public function edit(Admin $admin)
    {
        return view('admin.admin.edit', compact('admin'));
    }
    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'password' => 'nullable|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('admin.admin.index')->with('success', __('admins.updated_success'));
    }

    public function destroy(Admin $admin)
    {
        $admin->delete();
        return redirect()->route('admin.admin.index')->with('success', 'Admin deleted successfully.');
    }
}
