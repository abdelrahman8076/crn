<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\User;
use App\Models\Client;
use App\Traits\HasAccessFilter;
use App\Services\DataTables\BaseDataTable;
use Illuminate\Support\Facades\DB;

class DealsController extends Controller
{
    use HasAccessFilter;

    /**
     * Display the pipeline overview and deals list.
     */
    public function index()
    {
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
    $query = Deal::with(['client']);
    $query = $this->filterAccess($query, 'deal');

    // Use the custom attributes we created in the Model
    // Note: 'stage_badge' and 'formatted_amount'
    $columns = ['id', 'deal_name', 'formatted_amount', 'stage', 'client.name', 'created_at'];

    $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
    $service->setActionProps(['routeName' => 'admin.deals']);

    return $service->make($request);
}

    /**
     * Show form to create a new deal.
     */
    public function create()
    {
        $clients = $this->getAccessibleClients();
        $users   = User::all();

        return view('admin.deals.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created deal.
     */
    public function store(Request $request)
    {
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