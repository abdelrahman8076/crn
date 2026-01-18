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

<div class="btn-group" role="group">

  @if (
    $editRoute !== 'admin.clients'
    || auth('admin')->check()
    || auth('web')->user()?->role?->name !== 'Sales'
)
    @if ($editRoute && Route::has($editRoute))
        <a href="{{ route($editRoute, $id) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil-square"></i> {{ __('admins.edit') }}
        </a>
    @endif
@endif

{{-- 🗑️ Delete Button --}}
@php
    $canDelete = true;

    // Logic: Check if there are specific fields that must be empty
    if (isset($checkNullFields) && is_array($checkNullFields)) {
        foreach ($checkNullFields as $field) {
            // If the field on the model has a value, we cannot delete
            if (!empty($item->$field)) {
                $canDelete = false;
                break;
            }
        }
    }
@endphp

{{-- Final Condition --}}
@if(
    Auth::guard('admin')->check() && 
    ($deleteFlag ?? false) && 
    $canDelete && 
    // Only hide the button if it's the Admin table AND the IDs match
    (!($isAdminTable ?? false) || auth('admin')->id() !== $id)
)
    <form action="{{ route($deleteRoute, $id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger ms-2" 
                onclick="return confirm('{{ __('admin.confirm_delete') }}')">
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
