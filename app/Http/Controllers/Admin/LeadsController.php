<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Traits\ChecksPermissions;
use App\Services\DataTables\BaseDataTable;

class LeadsController extends Controller
{
    use HasAccessFilter, ChecksPermissions;

    public function index()
    {
        // Allow Sales and Manager users to view leads without permission check
        // Permissions are only for Admin roles
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            // For web guard users (Sales/Manager), allow access if they have Sales or Manager role
            if (!in_array($user->role?->name, ['Sales', 'Manager'])) {
                // For other roles, require permission
                $this->requirePermission('view-leads');
            }
        } else {
            // For admin guard, require permission
            $this->requirePermission('view-leads');
        }
        
        $columns = ['id', 'title', 'source', 'status', 'client.name'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table';

        return view('admin.leads.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable / AJAX data
    public function data(Request $request)
    {
        try {
            // Allow Sales and Manager users to view leads without permission check
            $user = Auth::guard('web')->user();
            $admin = Auth::guard('admin')->user();
            
            $hasAccess = false;
            if ($admin) {
                $hasAccess = $this->checkPermission('view-leads');
            } elseif ($user && in_array($user->role?->name, ['Sales', 'Manager'])) {
                $hasAccess = true; // Sales and Manager can always view leads
            } else {
                $hasAccess = $this->checkPermission('view-leads');
            }
            
            if (!$hasAccess) {
                return response()->json([
                    'draw' => (int) $request->get('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'You do not have permission to view leads.'
                ], 200);
            }

            $query = Lead::with(['client']);

            // Apply generic access filter
            $query = $this->filterAccess($query, 'lead');

            $columns = ['id', 'title', 'source', 'status', 'client.name'];
            $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
            $service->setActionProps(['routeName' => 'admin.leads']);

            return $service->make($request);
        } catch (\Exception $e) {
            \Log::error('LeadsController data method error: ' . $e->getMessage());
            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading data: ' . $e->getMessage()
            ], 200);
        }
    }

    // Show create form
    public function create()
    {
        $this->requirePermission('create-leads');
        
        $users = User::all();
        $clients = $this->getAccessibleClients(); // only clients the user can access

        return view('admin.leads.create', compact('users', 'clients'));
    }

    // Store lead
    public function store(Request $request)
    {
        $this->requirePermission('create-leads');
        
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
        $this->requirePermission('edit-leads');
        
        $lead = Lead::findOrFail($id);
        $users = User::all();
        $clients = $this->getAccessibleClients();

        return view('admin.leads.edit', compact('lead', 'users', 'clients'));
    }

    // Update lead
    public function update(Request $request, $id)
    {
        $this->requirePermission('edit-leads');
        
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
        $this->requirePermission('delete-leads');
        
        Lead::findOrFail($id)->delete();

        return redirect()->route('admin.leads.index')
            ->with('success', __('Lead deleted successfully.'));
    }

    // Helper to get accessible clients

}
