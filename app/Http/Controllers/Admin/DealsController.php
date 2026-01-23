<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\User;
use App\Models\Client;
use App\Traits\HasAccessFilter;
use App\Traits\ChecksPermissions;
use App\Services\DataTables\BaseDataTable;
use Illuminate\Support\Facades\DB;

class DealsController extends Controller
{
    use HasAccessFilter, ChecksPermissions;

    /**
     * Display the pipeline overview and deals list.
     */
    public function index()
    {
        // Allow Sales and Manager users to view deals without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            if (!in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
                $this->requirePermission('view-deals');
            }
        } else {
            $this->requirePermission('view-deals');
        }
        
        // 1. Dashboard Stats (NexusCRM Widgets)
        $statsQuery = $this->filterAccess(Deal::query(), 'deal');
        
        $totalDealsCount   = (clone $statsQuery)->count();
        $closedDealsValue  = (clone $statsQuery)->where('stage', 'closed-won')->sum('amount');
        $pendingDealsCount = (clone $statsQuery)->whereIn('stage', ['proposal', 'negotiation'])->count();

        // 2. DataTable Configuration
        $columns = ['id', 'deal_name', 'client.name', 'amount', 'stage', 'created_at'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.deals.index', compact(
            'columns', 
            'renderComponents', 
            'customActionsView',
            'totalDealsCount',
            'closedDealsValue',
            'pendingDealsCount'
        ));
    }

    /**
     * Fetch JSON data for DataTable with Nexus styling logic.
     */
 public function data(Request $request)
{
    try {
        // Allow Sales and Manager users to view deals without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        $hasAccess = false;
        if ($admin) {
            $hasAccess = $this->checkPermission('view-deals');
        } elseif ($user && in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
            $hasAccess = true;
        } else {
            $hasAccess = $this->checkPermission('view-deals');
        }
        
        if (!$hasAccess) {
            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'You do not have permission to view deals.'
            ], 200);
        }

        $query = Deal::with(['client']);
        $query = $this->filterAccess($query, 'deal');

        // Use the custom attributes we created in the Model
        // Note: 'stage_badge' and 'formatted_amount'
        $columns = ['id', 'deal_name', 'formatted_amount', 'stage', 'client.name', 'created_at'];

        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps(['routeName' => 'admin.deals']);

        return $service->make($request);
    } catch (\Exception $e) {
        \Log::error('DealsController data method error: ' . $e->getMessage());
        return response()->json([
            'draw' => (int) $request->get('draw', 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
            'error' => 'An error occurred while loading data: ' . $e->getMessage()
        ], 200);
    }
}

    /**
     * Show form to create a new deal.
     */
    public function create()
    {
        // Allow Sales and Manager users to create deals without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            if (!in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
                $this->requirePermission('create-deals');
            }
        } else {
            $this->requirePermission('create-deals');
        }
        
        $clients = $this->getAccessibleClients();
        $users   = User::all();

        return view('admin.deals.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created deal.
     */
    public function store(Request $request)
    {
        // Allow Sales and Manager users to create deals without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            if (!in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
                $this->requirePermission('create-deals');
            }
        } else {
            $this->requirePermission('create-deals');
        }
        
        $validated = $request->validate([
            'deal_name'   => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'stage'       => 'required|string|in:proposal,negotiation,closed-won,closed-lost',
            'client_id'   => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Deal::create($validated);

        return redirect()->route('admin.deals.index')
            ->with('success', __('deals.create_success_msg') ?? 'Deal created successfully.');
    }

    /**
     * Show form to edit a deal.
     */
    public function edit($id)
    {
        // Allow Sales and Manager users to edit deals without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (in_array($role, ['sales', 'manager'])) {
                // Allow access, skip permission check
                // The deal will be filtered by filterAccess in the view
            } else {
                $this->requirePermission('edit-deals');
            }
        } elseif ($admin) {
            // Admin guard - require permission
            $this->requirePermission('edit-deals');
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
        $deal    = Deal::findOrFail($id);
        $clients = $this->getAccessibleClients();
        $users   = User::all();
      
        return view('admin.deals.edit', compact('deal', 'clients', 'users'));
    }

    /**
     * Update an existing deal.
     */
    public function update(Request $request, $id)
    {
        // Allow Sales and Manager users to update deals without permission check
        $user = \Illuminate\Support\Facades\Auth::guard('web')->user();
        $admin = \Illuminate\Support\Facades\Auth::guard('admin')->user();
        
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (in_array($role, ['sales', 'manager'])) {
                // Allow access, skip permission check
                // The deal will be filtered by filterAccess
            } else {
                $this->requirePermission('edit-deals');
            }
        } elseif ($admin) {
            // Admin guard - require permission
            $this->requirePermission('edit-deals');
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
        $deal = Deal::findOrFail($id);

        $validated = $request->validate([
            'deal_name'   => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'stage'       => 'required|string|in:proposal,negotiation,closed-won,closed-lost',
            'client_id'   => 'required|exists:clients,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $deal->update($validated);

        return redirect()->route('admin.deals.index')
            ->with('success', __('deals.update_success_msg') ?? 'Deal updated successfully.');
    }

    /**
     * Remove a deal from the database.
     */
    public function destroy($id)
    {
        $this->requirePermission('delete-deals');
        
        $deal = Deal::findOrFail($id);
        $deal->delete();

        return redirect()->route('admin.deals.index')
            ->with('success', __('deals.delete_success_msg') ?? 'Deal deleted successfully.');
    }

    /**
     * Internal helper for role-based client selection.
     */
    protected function getAccessibleClients()
    {
        $query = Client::query();
        return $this->filterAccess($query, 'client')->orderBy('name')->get();
    }
}