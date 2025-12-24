<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ExcelImportService;
use App\Services\DataTables\BaseDataTable;
use App\Traits\HasAccessFilter;
use Illuminate\Support\Facades\Auth;


class ClientsController extends Controller
{
    use HasAccessFilter;

    /**
     * LIST PAGE
     */
    public function index()
    {
        $columns = ['id', 'name', 'phone', 'email', 'company', 'address', 'source', 'status'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.clients.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    /**
     * DATATABLE AJAX
     */
    public function data(Request $request)
    {
        $query = Client::with(['assignedSale', 'assignedManager']);
        $query = $this->filterAccess($query); // for sales
        $columns = ['id', 'name', 'phone', 'email', 'company', 'address', 'source', 'status'];

        $service = new BaseDataTable(
            $query,
            $columns,
            true,
            'components.default-buttons-table'
        );

        $service->setActionProps([
            'routeName' => 'admin.clients',
        ]);

        return $service->make($request);
    }

    /**
     * CREATE FORM
     */
    public function create()
    {
        $users = User::with('role')->get();
        $sales = User::sales()->get();
        $managers = User::managers()->get();


        return view('admin.clients.create', compact('users', 'managers', 'sales'));
    }

    /**
     * STORE NEW CLIENT
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'assigned_to_user' => 'nullable|exists:users,id',
            'assigned_to_manager' => 'nullable|exists:users,id',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        // If logged in via web guard and role is Manager, auto-assign
        $webUser = Auth::guard('web')->user();
        if ($webUser && $webUser->role?->name === 'Manager') {
            $data['assigned_to_manager'] = $webUser->id;
        }

        Client::create($data);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', __('Client created successfully.'));
    }


    /**
     * EDIT FORM
     */
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $users = User::with('role')->get();
        $sales = User::sales()->get();
        $managers = User::managers()->get();


        return view('admin.clients.edit', compact('client', 'users', 'managers', 'sales'));
    }

    /**
     * UPDATE CLIENT
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'assigned_to_user' => 'nullable|exists:users,id',
            'assigned_to_manager' => 'nullable|exists:users,id',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        $client->update($data);

        return redirect()
            ->route('admin.clients.index')
            ->with('success', __('Client updated successfully.'));
    }

    /**
     * DELETE CLIENT
     */
    public function destroy($id)
    {
        Client::findOrFail($id)->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', __('Client deleted successfully.'));
    }

    /**
     * UPLOAD PAGE
     */
    public function uploadForm()
    {
        return view('admin.clients.upload');
    }

    /**
     * PROCESS EXCEL
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        $importService = new ExcelImportService(new Client());
        $result = $importService->import($request->file('file'), ['name', 'phone']);

        $importedCount = $result['imported'] ?? 0;
        $errors = $result['errors'] ?? [];

        $flash = [];

        if ($importedCount) {
            $flash['success'] = __("Imported {$importedCount} clients successfully.");
        }

        if ($errors) {
            $flash['error'] = __('Some rows were skipped due to missing required fields.');
            $flash['error_rows'] = $errors;
        }

        return redirect()
            ->route('admin.clients.index')
            ->with($flash);
    }
}
