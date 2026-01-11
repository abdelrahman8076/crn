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
    {{-- Condition: Must be admin, deleteFlag must be true, AND model ID must NOT match current logged-in admin ID --}}
    @if(auth('admin')->check() && $deleteFlag == true && auth('admin')->id() !== $id)
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
