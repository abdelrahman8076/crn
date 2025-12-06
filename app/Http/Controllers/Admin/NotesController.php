<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Services\DataTables\BaseDataTable;


class NotesController extends Controller
{
    use HasAccessFilter;

    // Display notes index page
     public function index()
    {
        $columns = ['id', 'title', 'content', 'assigned_to', 'created_at'];
        $renderComponents = true; // or false based on your condition
        $customActionsView = 'components.default-buttons-table'; // full view path

        return view('admin.notes.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable AJAX data
    public function data(Request $request)
    {
        $query = Note::with('assignedUser');

        // Apply role-based filtering (Admin, Manager, Sales)
        $query = $this->filterAccess($query, 'assigned_to');

        $columns = ['id', 'title', 'content', 'assigned_to', 'created_at'];
        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps(['routeName' => 'admin.notes']);

        return $service->make($request);
    }

    // Show create form
    public function create()
    {
        $users = User::all();
        return view('admin.notes.create', compact('users'));
    }

    // Store note
    public function store(Request $request)
    {
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
        $note = Note::findOrFail($id);
        $users = User::all();

        return view('admin.notes.edit', compact('note', 'users'));
    }

    // Update note
    public function update(Request $request, $id)
    {
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
        Note::findOrFail($id)->delete();

        return redirect()->route('admin.notes.index')
            ->with('success', __('Note deleted successfully.'));
    }
}
