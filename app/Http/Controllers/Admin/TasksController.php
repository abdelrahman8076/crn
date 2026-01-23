<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Traits\HasAccessFilter;
use App\Traits\ChecksPermissions;
use App\Services\DataTables\BaseDataTable;

class TasksController extends Controller
{
    use HasAccessFilter, ChecksPermissions;

    /**
     * Display tasks index page with Dashboard Widgets
     */
    public function index()
    {
        // Allow Sales and Manager users to view tasks without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            if (!in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
                $this->requirePermission('view-tasks');
            }
        } else {
            $this->requirePermission('view-tasks');
        }
        
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
    try {
        // Allow Sales and Manager users to view tasks without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        $hasAccess = false;
        if ($admin) {
            $hasAccess = $this->checkPermission('view-tasks');
        } elseif ($user && in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
            $hasAccess = true;
        } else {
            $hasAccess = $this->checkPermission('view-tasks');
        }
        
        if (!$hasAccess) {
            return response()->json([
                'draw' => (int) $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'You do not have permission to view tasks.'
            ], 200);
        }

        $query = Task::with('user');
        $query = $this->filterAccess($query, 'task');

        // Ensure this matches the exact order of columns in your HTML table
        $columns = ['id', 'title', 'user.name', 'due_date', 'status', 'created_at'];
        
        $service = new BaseDataTable($query, $columns, true, 'components.default-buttons-table');
        $service->setActionProps(['routeName' => 'admin.tasks']);

        return $service->make($request);
    } catch (\Exception $e) {
        \Log::error('TasksController data method error: ' . $e->getMessage());
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
     * Show create form
     */
    public function create()
    {
        // Allow Sales and Manager users to create tasks without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            if (!in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
                $this->requirePermission('create-tasks');
            }
        } else {
            $this->requirePermission('create-tasks');
        }
        
        $users = $this->getAccessibleUsers();
        return view('admin.tasks.create', compact('users'));
    }

    /**
     * Store task
     */
    public function store(Request $request)
    {
        // Allow Sales and Manager users to create tasks without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if (!$admin && $user) {
            if (!in_array(strtolower($user->role?->name ?? ''), ['sales', 'manager'])) {
                $this->requirePermission('create-tasks');
            }
        } else {
            $this->requirePermission('create-tasks');
        }
        
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
        // Allow Sales and Manager users to edit tasks without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (in_array($role, ['sales', 'manager'])) {
                // Allow access, skip permission check
                // The task will be filtered by filterAccess in the view
            } else {
                $this->requirePermission('edit-tasks');
            }
        } elseif ($admin) {
            // Admin guard - require permission
            $this->requirePermission('edit-tasks');
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
        $task = Task::findOrFail($id);
        $users = $this->getAccessibleUsers();
        
        return view('admin.tasks.edit', compact('task', 'users'));
    }

    /**
     * Update task
     */
    public function update(Request $request, $id)
    {
        // Allow Sales and Manager users to update tasks without permission check
        $user = Auth::guard('web')->user();
        $admin = Auth::guard('admin')->user();
        
        if ($user) {
            $role = strtolower($user->role?->name ?? '');
            if (in_array($role, ['sales', 'manager'])) {
                // Allow access, skip permission check
                // The task will be filtered by filterAccess
            } else {
                $this->requirePermission('edit-tasks');
            }
        } elseif ($admin) {
            // Admin guard - require permission
            $this->requirePermission('edit-tasks');
        } else {
            abort(403, 'You do not have permission to perform this action.');
        }
        
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
        $this->requirePermission('delete-tasks');
        
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