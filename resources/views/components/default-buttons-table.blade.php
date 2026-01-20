@props([
    'model',              // Eloquent model instance or object with an ID
    'hasDeactivate' => true, // set to false if you don’t want deactivate button
    'routeName'=>null,
    'deleteFlag'=>false
])

@php
    $id = $model->id ?? null;

    // Dynamic route names
    $editRoute = $routeName ? $routeName . '.edit' : null;
    $inactiveRoute = $routeName ? $routeName . '.deactivate' : null;
    $deleteRoute= $routeName ? $routeName . '.destroy' :null;
   // $routeName = $routeName ?? null;

  //  dd($editRoute);
@endphp

@php
    // Helper function to check permission for both guards
    if (!function_exists('checkPermission')) {
        function checkPermission($permission) {
            if (auth()->guard('admin')->check()) {
                $admin = auth()->guard('admin')->user();
                return !$admin->role_id || !$admin->role ? true : $admin->hasPermission($permission);
            } elseif (auth()->guard('web')->check()) {
                $user = auth()->guard('web')->user();
                return $user && $user->role && $user->hasPermission($permission);
            }
            return false;
        }
    }
    
    // Determine permission based on route name
    $canEdit = false;
    $canDeletePermission = false;
    
    if (str_contains($routeName ?? '', 'users')) {
        $canEdit = checkPermission('edit-users');
        $canDeletePermission = checkPermission('delete-users');
    } elseif (str_contains($routeName ?? '', 'admin')) {
        $canEdit = checkPermission('edit-admins');
        $canDeletePermission = checkPermission('delete-admins');
    } elseif (str_contains($routeName ?? '', 'clients')) {
        $canEdit = checkPermission('edit-clients');
        $canDeletePermission = checkPermission('delete-clients');
    } elseif (str_contains($routeName ?? '', 'leads')) {
        $canEdit = checkPermission('edit-leads');
        $canDeletePermission = checkPermission('delete-leads');
    } elseif (str_contains($routeName ?? '', 'deals')) {
        $canEdit = checkPermission('edit-deals');
        $canDeletePermission = checkPermission('delete-deals');
    } elseif (str_contains($routeName ?? '', 'tasks')) {
        $canEdit = checkPermission('edit-tasks');
        $canDeletePermission = checkPermission('delete-tasks');
    } elseif (str_contains($routeName ?? '', 'notes')) {
        $canEdit = true; // Add notes permissions if needed
        $canDeletePermission = true;
    } else {
        // Default: allow if admin guard or has access-admin permission
        $canEdit = auth()->guard('admin')->check() || checkPermission('access-admin');
        $canDeletePermission = auth()->guard('admin')->check() || checkPermission('access-admin');
    }
    
    // Check if model has specific fields that must be empty before deletion
    $canDeleteModel = true;
    if (isset($checkNullFields) && is_array($checkNullFields)) {
        foreach ($checkNullFields as $field) {
            if (!empty($model->$field)) {
                $canDeleteModel = false;
                break;
            }
        }
    }
    
    // Final delete permission = permission check AND model check
    $canDelete = $canDeletePermission && $canDeleteModel;
@endphp

<div class="btn-group" role="group">

  @if ($canEdit && $editRoute && Route::has($editRoute))
        <a href="{{ route($editRoute, $id) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil-square"></i> {{ __('admins.edit') }}
        </a>
    @endif

{{-- 🗑️ Delete Button --}}
{{-- Final Condition --}}
@if(
    $canDelete && 
    ($deleteFlag ?? false) && 
    // Only hide the button if it's the Admin table AND the IDs match
    (!($isAdminTable ?? false) || auth('admin')->id() !== $id) &&
    $deleteRoute && Route::has($deleteRoute)
)
    <form action="{{ route($deleteRoute, $id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger ms-2" 
                onclick="return confirm('{{ __('admin.confirm_delete') ?? 'Are you sure you want to delete this item?' }}')">
            <i class="bi bi-trash"></i> {{ __('admins.delete') }}
        </button>
    </form>
@endif

    {{-- 🚫 Deactivate Button --}}
    @if ($hasDeactivate && $inactiveRoute && Route::has($inactiveRoute))
        <form action="{{ route($inactiveRoute, $id) }}" method="POST" style="display:inline;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline-warning"
                    onclick="return confirm('{{ __('Are you sure you want to deactivate this item?') }}')">
                <i class="bi bi-slash-circle"></i> {{ __('Deactivate') }}
            </button>
        </form>
    @endif

    @if(isset($routeName) && $routeName === 'admin.guest')
    <a href="{{ route('admin.guests.convert', $model->id) }}" 
       class="btn btn-sm btn-warning">
        <i class="bi bi-person-plus"></i> {{ __('guest_messages.convert') }}
    </a>
@endif


</div>
