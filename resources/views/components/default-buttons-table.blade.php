@props([
    'model',              // Eloquent model instance or object with an ID
    'hasDeactivate' => true, // set to false if you don’t want deactivate button
    'routeName'=>null
])

@php
    $id = $model->id ?? null;

    // Dynamic route names
    $editRoute = $routeName ? $routeName . '.edit' : null;
    $inactiveRoute = $routeName ? $routeName . '.deactivate' : null;
   // $routeName = $routeName ?? null;

  //  dd($editRoute);
@endphp

<div class="btn-group" role="group">

    {{-- ✏️ Edit Button --}}
    @if ($editRoute && Route::has($editRoute))
        <a href="{{ route($editRoute, $id) }}" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil-square"></i> {{ __('Edit') }}
        </a>
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
