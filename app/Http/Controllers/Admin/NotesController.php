<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Traits\ChecksPermissions;
use App\Services\DataTables\BaseDataTable;


class NotesController extends Controller
{
    use HasAccessFilter, ChecksPermissions;

    // Display notes index page
     public function index()
    {
        $this->requirePermission('view-notes');
        
        $columns = ['id',  'content', 'user.name', 'created_at'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.notes.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable AJAX data
    public function data(Request $request)
    {
        try {
            if (!$this->checkPermission('view-notes')) {
                return response()->json([
                    'draw' => (int) $request->get('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => 'You do not have permission to view notes.'
                ], 200);
            }

            $query = Note::with('user');

            // Apply role-based filtering (Admin, Manager, Sales)
            $query = $this->filterAccess($query);

            $columns = ['id',  'content', 'user.name', 'created_at'];
            $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
            $service->setActionProps(['routeName' => 'admin.notes']);

            return $service->make($request);
        } catch (\Exception $e) {
            \Log::error('NotesController data method error: ' . $e->getMessage());
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
        $this->requirePermission('create-notes');
        
        $users = User::all();
        return view('admin.notes.create', compact('users'));
    }

    // Store note
    public function store(Request $request)
    {
        $this->requirePermission('create-notes');
        
        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        Note::create($request->all());

        return redirect()->route('admin.notes.index')
            ->with('success', __('Note created successfully.'));
    }

    // Show edit form
    public function edit($id)
    {
        $this->requirePermission('edit-notes');
        
        $note = Note::findOrFail($id);
        $users = User::all();

        return view('admin.notes.edit', compact('note', 'users'));
    }

    // Update note
    public function update(Request $request, $id)
    {
        $this->requirePermission('edit-notes');
        
        $note = Note::findOrFail($id);

        $request->validate([
            'title'       => 'required|string|max:255',
            'content'     => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $note->update($request->all());

        return redirect()->route('admin.notes.index')
            ->with('success', __('Note updated successfully.'));
    }

    // Delete note
    public function destroy($id)
    {
        $this->requirePermission('delete-notes');
        
        Note::findOrFail($id)->delete();

        return redirect()->route('admin.notes.index')
            ->with('success', __('Note deleted successfully.'));
    }
}
