@props([
    'ajaxUrl',
    'columns',
    'renderComponents' => false,
    'customActionsView' => ''
])
<div class="table-responsive mt-5">
    <table class="table table-bordered" id="datatable">
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th>{{ ucwords(str_replace(['.', '_'], ' ', $col)) }}</th>
                @endforeach
                   @if ($renderComponents && !empty($customActionsView))
                <th>Actions</th>
            @endif
            </tr>
        </thead>
    </table>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: '{{ $ajaxUrl }}',
        columns: [
            @foreach ($columns as $col)
                { data: '{{ $col }}', name: '{{ $col }}' },
            @endforeach

            @if ($renderComponents && !empty($customActionsView))
                { data: 'actions', orderable: false, searchable: false }
            @endif
        ]
    });
});
</script>

