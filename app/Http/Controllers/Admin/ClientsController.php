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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // Import this!


class ClientsController extends Controller
{
    use HasAccessFilter;

    /**
     * LIST PAGE
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $client = new Client();

        // Get the fillable fields from the model
        $headers = $client->getFillable();

        // Set the headers in the first row
        foreach ($headers as $index => $header) {
            // Convert numeric index (0, 1, 2...) to Excel letter (A, B, C...)
            $columnLetter = Coordinate::stringFromColumnIndex($index + 1);

            // Use setCellValue with the coordinate string (e.g., "A1", "B1")
            $sheet->setCellValue($columnLetter . '1', $header);

            // Bold the header
            $sheet->getStyle($columnLetter . '1')->getFont()->setBold(true);

            // Optional: Auto-size columns for better readability
            $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
        }

        // Add sample data for the user
        $sheet->setCellValue('A2', 'Full Name Example');
        $sheet->setCellValue('B2', '2010XXXXXXXX');
        $sheet->setCellValue('C2', 'example@email.com');

        $writer = new Xlsx($spreadsheet);

        $fileName = 'clients_template.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
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
            'deleteFlag' => true,
            'checkNullFields' => ['assigned_to_sale', 'assigned_to_manager']

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
            'feedback' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            // Form uses 'assigned_to_user' for sales select; map it to DB field 'assigned_to_sale' after validation
            'assigned_to_user' => 'nullable|exists:users,id',
            'assigned_to_manager' => 'nullable|exists:users,id',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        // Map form field to DB field
        if (array_key_exists('assigned_to_user', $data)) {
            $data['assigned_to_sale'] = $data['assigned_to_user'];
            unset($data['assigned_to_user']);
        }

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
            'feedback' => 'nullable|string|max:255',

            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:30',
            'assigned_to_user' => 'nullable|exists:users,id',
            'assigned_to_manager' => 'nullable|exists:users,id',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        // Map form field to DB field
        if (array_key_exists('assigned_to_user', $data)) {
            $data['assigned_to_sale'] = $data['assigned_to_user'];
            unset($data['assigned_to_user']);
        }

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
        $client = Client::findOrFail($id);

        // 1. Remove the relationship links first
        $client->assigned_to_sale = null;
        $client->assigned_to_manager = null;
        $client->save();

        // 2. Now delete the client
        $client->delete();

        return redirect()
            ->route('admin.clients.index')
            ->with('success', __('Client removed and unassigned successfully.'));
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
