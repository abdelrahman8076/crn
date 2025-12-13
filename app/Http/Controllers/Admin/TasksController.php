<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Services\DataTables\BaseDataTable;

class TasksController extends Controller
{
    use HasAccessFilter;

    // Display tasks index page
    public function index()
    {
        $columns = ['id', 'title', 'description', 'user.name', 'due_date', 'status', 'created_at'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.tasks.index', compact('columns', 'renderComponents', 'customActionsView'));
    }

    // Datatable AJAX data
    public function data(Request $request)
    {
        $query = Task::with('user');

        // Apply role-based filtering (Admin, Manager, Sales)
        $query = $this->filterAccess($query, 'task');

        $columns = ['id', 'title', 'description', 'user.name', 'due_date', 'status', 'created_at'];
        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps(['routeName' => 'admin.tasks']);

        return $service->make($request);
    }

    // Show create form
    public function create()
    {
        $authUser = auth()->user(); // get the currently logged-in user
        $users = collect();

        if ($authUser->role?->name === 'Manager') {
            // Assuming your User model has a 'team' relationship that returns Sales users
            $users = $authUser->team()->get();
        } else {
            // For admins or others, get all users
            $users = User::all();
        }

        return view('admin.tasks.create', compact('users'));
    }


    // Store task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|string|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
        ]);

        Task::create($request->all());

        return redirect()->route('admin.tasks.index')
            ->with('success', __('Task created successfully.'));
    }

    // Show edit form
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $authUser = auth()->user(); // get the currently logged-in user
        $users = collect();

        if ($authUser->role?->name === 'Manager') {
            // Assuming your User model has a 'team' relationship that returns Sales users
            $users = $authUser->team()->get();
        } else {
            // For admins or others, get all users
            $users = User::all();
        }
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    // Update task
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|string|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());

        return redirect()->route('admin.tasks.index')
            ->with('success', __('Task updated successfully.'));
    }

    // Delete task
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', __('Task deleted successfully.'));
    }
}
