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

    /**
     * Display tasks index page with Dashboard Widgets
     */
    public function index()
    {
        // 1. Calculate Stats for Nexus Widgets
        $statsQuery = $this->filterAccess(Task::query(), 'task');
        
        $totalTasksCount = (clone $statsQuery)->count();
        $pendingTasksCount = (clone $statsQuery)->where('status', 'pending')->count();
        $completedTasksCount = (clone $statsQuery)->where('status', 'completed')->count();
        
        // Calculate Overdue (Pending & past due date)
        $overdueTasksCount = (clone $statsQuery)
            ->where('status', '!=', 'completed')
            ->whereDate('due_date', '<', now())
            ->count();

        // 2. Define Table Columns (Nexus Attributes)
        $columns = ['id', 'title', 'user.name', 'due_date', 'status', 'created_at'];
        $renderComponents = true;
        $customActionsView = 'components.default-buttons-table';

        return view('admin.tasks.index', compact(
            'columns', 
            'renderComponents', 
            'customActionsView',
            'totalTasksCount',
            'pendingTasksCount',
            'completedTasksCount',
            'overdueTasksCount'
        ));
    }

    /**
     * Datatable AJAX data
     */
 public function data(Request $request)
{
    $query = Task::with('user');
    $query = $this->filterAccess($query, 'task');

    // Ensure this matches the exact order of columns in your HTML table
    $columns = ['id', 'title', 'user.name', 'due_date', 'status', 'created_at'];
    
    $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
    $service->setActionProps(['routeName' => 'admin.tasks']);

    return $service->make($request);
}

    /**
     * Show create form
     */
    public function create()
    {
        $users = $this->getAccessibleUsers();
        return view('admin.tasks.create', compact('users'));
    }

    /**
     * Store task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|string|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
        ]);

        Task::create($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', __('tasks.create_success'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        $users = $this->getAccessibleUsers();
        
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    /**
     * Update task
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => 'required|string|in:pending,in-progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('admin.tasks.index')
            ->with('success', __('tasks.update_success'));
    }

    /**
     * Delete task
     */
    public function destroy($id)
    {
        Task::findOrFail($id)->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', __('tasks.delete_success'));
    }

    /**
     * Internal Helper for User selection based on role
     */
    protected function getAccessibleUsers()
    {
        if (Auth::guard('admin')->check()) {
            return User::all();
        }
        
        // If your User model has a 'team' relationship
        return auth()->user()->team()->get();
    }
}