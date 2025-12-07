@props([
    'ajaxUrl',
    'columns',
    'renderComponents' => false,
    'customActionsView' => ''
])

<div class="table-responsive mt-5">
    <table class="table table-bordered table-striped nowrap w-100" id="datatable">
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
    new DataTable('#datatable', {
        processing: true,
        serverSide: true,
        ajax: '{{ $ajaxUrl }}',

        // ⭐ Modern DataTables V2 options
        responsive: true,

        rowReorder: {
            selector: 'td:nth-child(2)' // same as your example
        },

        scrollX: true,

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
