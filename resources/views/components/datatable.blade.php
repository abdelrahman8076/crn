@props([
    'ajaxUrl',
    'columns',
    'renderComponents' => false,
    'customActionsView' => ''
])

<style>
    /* 1. Desktop Styles */
    .table-container { width: 100%; padding: 15px; }
    
    /* 2. Mobile "Card" Transformation (Max-width: 767px) */
    @media screen and (max-width: 767px) {
        /* Hide table headers */
        #datatable thead {
            display: none;
        }

        /* Force block display for card layout */
        #datatable, #datatable tbody, #datatable tr, #datatable td {
            display: block;
            width: 100% !important;
        }

        #datatable tr {
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            background: #fff;
            overflow: hidden;
        }

        /* Value Styling */
        #datatable td {
            text-align: center; 
            padding: 2px 5px 2px 20% !important; /* Left gutter for labels */
            position: relative;
            border-bottom: 1px solid #f1f1f1;
            min-height: 46px; /* Prevents collapse if data is empty */
            word-break: break-word; /* Handles long emails/strings */
            color: #333;
        }

        /* Label Styling (The "Keys") */
        #datatable td:before {
            content: attr(data-label);
            position: absolute;
            top: 12px;
            left: 15px;
            width: 40%;
            text-align: left;
            font-weight: 700;
            color: #6c757d;
            text-transform: capitalize;
            font-size: 0.85rem;
        }

        /* Action Row Styling */
        #datatable td:last-child {
            text-align: center;
            background: #fdfdfd;
            border-bottom: none;
            padding: 15px !important;
        }

        #datatable td:last-child:before {
            display: none;
        }
    }
</style>

<div class="table-container mt-4">
    <table class="table table-hover w-100" id="datatable">
        <thead class="table-light">
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    // 1. Capture the labels from the header
    const labels = [];
    $('#datatable thead th').each(function() {
        labels.push($(this).text());
    });

    // 2. Initialize DataTable
    const table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! $ajaxUrl !!}',
        autoWidth: false,
        responsive: false, // We are using custom CSS for responsiveness
        
        // Apply labels whenever the table is drawn (pagination, search, etc.)
        drawCallback: function() {
            $('#datatable tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    $(this).attr('data-label', labels[index]);
                });
            });
        },

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
                    className: 'text-center'
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