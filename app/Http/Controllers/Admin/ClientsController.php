<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ExcelImportService;
use App\Services\DataTables\BaseDataTable;
use App\Traits\HasAccessFilter;
use App\Traits\ChecksPermissions;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate; // Import this!
use Illuminate\Support\Facades\Log;



class ClientsController extends Controller
{
    use HasAccessFilter, ChecksPermissions;

    /**
     * Sales/Manager can edit only clients they can access (based on filterAccess()).
     */
    private function assertWebUserCanAccessClient(int $clientId): void
    {
        if (Auth::guard('admin')->check()) {
            return; // admin guard handled by permissions elsewhere
        }

        $user = Auth::guard('web')->user();
        $role = strtolower($user->role?->name ?? '');
        if (!$user || !in_array($role, ['sales', 'manager'])) {
            return; // other roles rely on permission checks
        }

        $query = Client::query()->where('id', $clientId);
        $query = $this->filterAccess($query, 'client');
        if (!$query->exists()) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }

    /**
     * LIST PAGE
     */
    public function downloadTemplate()
    {
        $this->requirePermission('create-clients');
        
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
        // Allow Sales and Manager users to view clients without permission check
        // Permissions are only for Admin roles
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            // For web guard users (Sales/Manager), allow access if they have Sales or Manager role
            if (!in_array($user->role?->name, ['Sales', 'Manager'])) {
                // For other roles, require permission (check both view-clients and view-leads)
                if (!$this->checkPermission('view-clients') && !$this->checkPermission('view-leads')) {
                    abort(403, 'You do not have permission to perform this action.');
                }
            }
        } else {
            // For admin guard, require permission (check both view-clients and view-leads)
            if (!$this->checkPermission('view-clients') && !$this->checkPermission('view-leads')) {
                abort(403, 'You do not have permission to perform this action.');
            }
        }
        
        $columns = ['id', 'name', 'phone', 'email', 'company', 'address', 'source', 'status','feedback'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.clients.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    /**
     * DATATABLE AJAX
     */
    public function data(Request $request)
    {
        try {
            // Allow Sales and Manager users to view clients without permission check
            $user = Auth::guard('web')->user();
            $admin = Auth::guard('admin')->user();
            
            $hasAccess = false;
            if ($admin) {
                $hasAccess = $this->checkPermission('view-clients') || $this->checkPermission('view-leads');
            } elseif ($user && in_array($user->role?->name, ['Sales', 'Manager'])) {
                $hasAccess = true; // Sales and Manager can always view clients
            } else {
                $hasAccess = $this->checkPermission('view-clients') || $this->checkPermission('view-leads');
            }
            
            if (!$hasAccess) {
                return response()->json([
                    'draw' => (int) $request->get('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'You do not have permission to view clients.'
                ], 200);
            }
        $query = Client::with(['assignedSale', 'assignedManager']);
        $query = $this->filterAccess($query); // for sales
        $columns = ['id', 'name', 'phone', 'email', 'company', 'address', 'source', 'status','feedback'];

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
                // 'deleteFlag' => true,
                'checkNullFields' => ['assigned_to_sale', 'assigned_to_manager']

            ]);

            return $service->make($request);
        } catch (\Exception $e) {
            Log::error('ClientsController data method error: ' . $e->getMessage());
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
     * CREATE FORM
     */
    public function create()
    {
        // Allow Sales and Manager users to create clients without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (!in_array($role, ['sales', 'manager'])) {
                // For other roles, require permission (check both create-clients and create-leads)
                if (!$this->checkPermission('create-clients') && !$this->checkPermission('create-leads')) {
                    abort(403, 'You do not have permission to perform this action.');
                }
            }
            // Sales/Manager can proceed without permission check
        } elseif ($admin) {
            // Admin guard - require permission (check both create-clients and create-leads)
            if (!$this->checkPermission('create-clients') && !$this->checkPermission('create-leads')) {
                abort(403, 'You do not have permission to perform this action.');
            }
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
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
        // Allow Sales and Manager users to create clients without permission check
        $webUser = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if ($webUser) {
            $webRole = strtolower($webUser->role?->name ?? '');
            if (!in_array($webRole, ['sales', 'manager'])) {
                // For other roles, require permission (check both create-clients and create-leads)
                if (!$this->checkPermission('create-clients') && !$this->checkPermission('create-leads')) {
                    abort(403, 'You do not have permission to perform this action.');
                }
            }
            // Sales/Manager can proceed without permission check
        } elseif ($admin) {
            // Admin guard - require permission (check both create-clients and create-leads)
            if (!$this->checkPermission('create-clients') && !$this->checkPermission('create-leads')) {
                abort(403, 'You do not have permission to perform this action.');
            }
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'feedback' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9]+$/'],
            // Form uses 'assigned_to_user' for sales select; map it to DB field 'assigned_to_sale' after validation
            'assigned_to_user' => 'nullable|exists:users,id',
            'assigned_to_manager' => 'nullable|exists:users,id',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ], [
            'phone.required' => 'The phone number field is required.',
            'phone.regex' => 'The phone field must contain only numbers.',
            'email.email' => 'Please enter a valid email address.',
        ]);

        // Map form field to DB field
        if (array_key_exists('assigned_to_user', $data)) {
            $data['assigned_to_sale'] = $data['assigned_to_user'];
            unset($data['assigned_to_user']);
        }

        // If logged in via web guard, auto-assign ownership to enforce scope
        if ($webUser) {
            if ($webRole === 'manager') {
                $data['assigned_to_manager'] = $webUser->id;
            } elseif ($webRole === 'sales') {
                $data['assigned_to_sale'] = $webUser->id;
                // If sales belongs to a manager, keep it consistent
                if (!empty($webUser->manager_id)) {
                    $data['assigned_to_manager'] = $webUser->manager_id;
                }
            }
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
        // Allow Sales and Manager users to edit clients without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        // Check if user is Sales/Manager via web guard
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (in_array($role, ['sales', 'manager'])) {
                // Verify they can access this client
                $this->assertWebUserCanAccessClient((int) $id);
                // Allow access, skip permission check
            } else {
                // For other roles, require permission (check both edit-clients and edit-leads)
                if (!$this->checkPermission('edit-clients') && !$this->checkPermission('edit-leads')) {
                    abort(403, 'You do not have permission to perform this action.');
                }
            }
        } elseif ($admin) {
            // Admin guard - require permission (check both edit-clients and edit-leads)
            if (!$this->checkPermission('edit-clients') && !$this->checkPermission('edit-leads')) {
                abort(403, 'You do not have permission to perform this action.');
            }
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
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
        // Allow Sales and Manager users to update clients without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        // Check if user is Sales/Manager via web guard
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (in_array($role, ['sales', 'manager'])) {
                // Verify they can access this client
                $this->assertWebUserCanAccessClient((int) $id);
                // Allow access, skip permission check
            } else {
                // For other roles, require permission (check both edit-clients and edit-leads)
                if (!$this->checkPermission('edit-clients') && !$this->checkPermission('edit-leads')) {
                    abort(403, 'You do not have permission to perform this action.');
                }
            }
        } elseif ($admin) {
            // Admin guard - require permission (check both edit-clients and edit-leads)
            if (!$this->checkPermission('edit-clients') && !$this->checkPermission('edit-leads')) {
                abort(403, 'You do not have permission to perform this action.');
            }
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
        $client = Client::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'feedback' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9]+$/'],
            'assigned_to_user' => 'nullable|exists:users,id',
            'assigned_to_manager' => 'nullable|exists:users,id',
            'source' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ], [
            'phone.required' => 'The phone number field is required.',
            'phone.regex' => 'The phone field must contain only numbers.',
            'email.email' => 'Please enter a valid email address.',
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
        $this->requirePermission('delete-clients');
        
        $client = Client::findOrFail($id);

        // 1. Remove the relationship links first
        $client->assigned_to_sale = null;
        $client->assigned_to_manager = null;
        $client->save();

        // 2. Now delete the client

        return redirect()
            ->route('admin.clients.index')
            ->with('success', __('Client removed and unassigned successfully.'));
    }

    /**
     * UPLOAD PAGE
     */
    public function uploadForm()
    {
        $this->requirePermission('create-clients');
        
        return view('admin.clients.upload');
    }

    /**
     * PROCESS EXCEL
     */
    public function upload(Request $request)
    {
        $this->requirePermission('create-clients');
        
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
