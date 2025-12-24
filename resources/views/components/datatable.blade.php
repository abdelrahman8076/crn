@props([
    'ajaxUrl',
    'columns',
    'renderComponents' => false,
    'customActionsView' => ''
])

<div class="mt-5">
    {{-- Removed table-responsive wrapper as 'responsive: true' handles the layout --}}
    <table class="table table-bordered table-striped nowrap" id="datatable" style="width:100%">
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
    const table = new DataTable('#datatable', {
        processing: true,
        serverSide: true,
        ajax: '{{ $ajaxUrl }}',

        // ⭐ Mobile Optimization: Responsive Extension
        responsive: true,
        autoWidth: false, // Prevents table from breaking layout on small screens

        // ⭐ Use RowReorder with touch support
        rowReorder: {
            selector: 'td:nth-child(2)',
            update: true
        },

        // Removed scrollX: true as it conflicts with the 'responsive' extension in v2
        
        columns: [
            @foreach ($columns as $index => $col)
                { 
                    data: '{{ $col }}', 
                    name: '{{ $col }}',
                    // Priority 1 ensures the first column stays visible on mobile
                    responsivePriority: {{ $index === 0 ? 1 : 10000 }}
                },
            @endforeach

            @if ($renderComponents && !empty($customActionsView))
                { 
                    data: 'actions', 
                    orderable: false, 
                    searchable: false,
                    // Priority 2 ensures actions are the second-to-last thing hidden
                    responsivePriority: 2 
                }
            @endif
        ]
    });

    // Fix for potential layout issues if table is inside a hidden tab/modal
    window.addEventListener('resize', () => {
        table.columns.adjust().responsive.recalc();
    });
});
</script>
