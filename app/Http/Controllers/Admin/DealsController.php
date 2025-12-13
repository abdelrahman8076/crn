<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Services\DataTables\BaseDataTable;
use App\Models\Client;



class DealsController extends Controller
{
    use HasAccessFilter;

    // List deals page
    public function index()
    {
        $columns = ['id', 'deal_name', 'amount', 'stage', 'lead.client.name'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.deals.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable / AJAX data
 public function data(Request $request)
{
    // Start query with relationships
    $query = Deal::with(['lead.client']);

    // Apply access filter for Deal hierarchy
    $query = $this->filterAccess($query, 'deal');

    // Columns for DataTable
    $columns = ['id', 'deal_name', 'amount', 'stage', 'lead.client.name'];

    // Build DataTable
    $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
    $service->setActionProps(['routeName' => 'admin.deals']);

    return $service->make($request);
}

    // Show create form
    public function create()
    {
        $leads = $this->filterAccess( $query = Lead::with(['client']) , 'lead')->get();
        $users = User::all();

        return view('admin.deals.create', compact('leads', 'users'));
    }

    // Store deal
    public function store(Request $request)
    {
        $request->validate([
            'deal_name' => 'required|string|max:255',
            'amount'    => 'required|numeric',
            'stage'     => 'required|string|in:proposal,negotiation,closed-won,closed-lost',
            'lead_id'   => 'required|exists:leads,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Deal::create($request->all());

        return redirect()->route('admin.deals.index')
            ->with('success', __('Deal created successfully.'));
    }

    // Show edit form
    public function edit($id)
    {
        $deal = Deal::findOrFail($id);
        $leads = $this->filterAccess( $query = Lead::with(['client']) , 'lead')->get();
        $users = User::all();
      
        return view('admin.deals.edit', compact('deal', 'leads', 'users'));
    }

    // Update deal
    public function update(Request $request, $id)
    {
        $deal = Deal::findOrFail($id);

        $request->validate([
            'deal_name' => 'required|string|max:255',
            'amount'    => 'required|numeric',
            'stage'     => 'required|string|in:proposal,negotiation,closed-won,closed-lost',
            'lead_id'   => 'required|exists:leads,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $deal->update($request->all());

        return redirect()->route('admin.deals.index')
            ->with('success', __('Deal updated successfully.'));
    }

    // Delete deal
    public function destroy($id)
    {
        Deal::findOrFail($id)->delete();

        return redirect()->route('admin.deals.index')
            ->with('success', __('Deal deleted successfully.'));
    }
}
