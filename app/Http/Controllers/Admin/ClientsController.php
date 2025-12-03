<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ExcelImportService;
use App\Services\DataTables\BaseDataTable;


class ClientsController extends Controller
{
    // LIST

    public function index()
    {
        $columns = ['id', 'name', 'phone', 'email', 'company', 'address'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.clients.index', compact('columns', 'renderComponents', 'customActionsView'));
    }
    public function data(Request $request)
    {
        $query = Client::with('assignedUser')
            ->when(auth()->user()->role_id == 2, function ($q) {
                // Sales can only see their own clients
                $q->where('assigned_to', auth()->id());
            })
            ->when(auth()->user()->role_id == 3, function ($q) {
                // Manager sees own clients + sales under him
                $managerId = auth()->id();

                // Get all sales users under this manager
                $salesIds = User::where('manager_id', $managerId)
                    ->pluck('id')
                    ->toArray();

                // Manager sees: his clients + his team’s clients
                $q->whereIn('assigned_to', array_merge([$managerId], $salesIds));
            });

        $columns = ['id', 'name', 'phone', 'email', 'company', 'address'];

        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        // Optional: send extra props to the view (e.g. routeName)
        $service->setActionProps([
            'routeName' => 'admin.clients',
        ]);
        return $service->make($request);
    }

    // ADD FORM
    public function create()
    {
        $users = User::all();
        return view('admin.clients.create', compact('users'));
    }

    // ADD STORE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Client::create($request->all());

        return redirect()->route('admin.clients.index')
            ->with('success', __('Client created successfully.'));
    }

    // EDIT FORM
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        $users = User::all();
        return view('admin.clients.edit', compact('client', 'users'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $client->update($request->all());

        return redirect()->route('admin.clients.index')
            ->with('success', __('Client updated successfully.'));
    }

    // DELETE
    public function destroy($id)
    {
        Client::findOrFail($id)->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', __('Client deleted successfully.'));
    }

    // UPLOAD EXCEL PAGE
    public function uploadForm()
    {
        return view('admin.clients.upload');
    }


    public function upload(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        // Initialize the generic Excel import service for Client model
        $importService = new ExcelImportService(new Client());

        // Specify required columns for validation (name and phone)
        $result = $importService->import($request->file('file'), ['name', 'phone']);

        $importedCount = $result['imported'] ?? 0;
        $errors = $result['errors'] ?? [];

        // Prepare flash messages
        $flashData = [];

        if ($importedCount > 0) {
            $flashData['success'] = __("Imported {$importedCount} clients successfully.");
        }

        if (!empty($errors)) {
            $flashData['error'] = __('Some rows were skipped due to missing required fields.');
            $flashData['error_rows'] = $errors; // pass skipped rows to the view
        }

        // Redirect back to the clients index with messages
        return redirect()
            ->route('admin.clients.index')
            ->with($flashData);
    }

}
