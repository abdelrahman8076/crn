@if(session('success'))
    <div {{ $attributes->merge(['class' => 'alert alert-success alert-dismissible fade show mt-3']) }} role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
