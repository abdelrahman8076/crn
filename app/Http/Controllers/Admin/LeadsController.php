<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Services\DataTables\BaseDataTable;

class LeadsController extends Controller
{
    use HasAccessFilter;

    public function index()
    {
        $columns = ['id', 'title', 'source', 'status', 'client.name'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table';

        return view('admin.leads.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable / AJAX data
    public function data(Request $request)
    {
        $query = Lead::with(['client']);

        // Apply generic access filter
        $query = $this->filterAccess($query, 'lead');

        $columns = ['id', 'title', 'source', 'status', 'client.name'];
        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps(['routeName' => 'admin.leads']);

        return $service->make($request);
    }

    // Show create form
    public function create()
    {
        $users = User::all();
        $clients = $this->getAccessibleClients(); // only clients the user can access

        return view('admin.leads.create', compact('users', 'clients'));
    }

    // Store lead
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'client_id' => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Lead::create($request->only(['title', 'source', 'status', 'client_id', 'assigned_to']));

        return redirect()->route('admin.leads.index')
            ->with('success', __('Lead created successfully.'));
    }

    // Show edit form
    public function edit($id)
    {
        $lead = Lead::findOrFail($id);
        $users = User::all();
        $clients = $this->getAccessibleClients();

        return view('admin.leads.edit', compact('lead', 'users', 'clients'));
    }

    // Update lead
    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:50',
            'client_id' => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $lead->update($request->only(['title', 'source', 'status', 'client_id', 'assigned_to']));

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

    // Helper to get accessible clients

}
