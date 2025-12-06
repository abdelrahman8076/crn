<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Services\DataTables\BaseDataTable;


class LeadsController extends Controller
{
    use HasAccessFilter;

    public function index()
    {
        $columns = ['id', 'name', 'email', 'phone', 'company', 'assigned_to'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.leads.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable / AJAX data
    public function data(Request $request)
    {
        $query = Lead::with('assignedUser');

        // Apply generic access filter
        $query = $this->filterAccess($query, 'assigned_to');

        $columns = ['id', 'name', 'email', 'phone', 'company', 'assigned_to'];
        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps(['routeName' => 'admin.leads']);

        return $service->make($request);
    }

    // Show create form
    public function create()
    {
        $users = User::all();
        return view('admin.leads.create', compact('users'));
    }

    // Store lead
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:30',
            'company'     => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Lead::create($request->all());

        return redirect()->route('admin.leads.index')
            ->with('success', __('Lead created successfully.'));
    }

    // Show edit form
    public function edit($id)
    {
        $lead = Lead::findOrFail($id);
        $users = User::all();

        return view('admin.leads.edit', compact('lead', 'users'));
    }

    // Update lead
    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:30',
            'company'     => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update($request->all());

        return redirect()->route('admin.leads.index')
            ->with('success', __('Lead updated successfully.'));
    }

    // Delete lead
    public function destroy($id)
    {
        Lead::findOrFail($id)->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', __('Lead deleted successfully.'));
    }
}
