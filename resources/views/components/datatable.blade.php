@props([
    'ajaxUrl',
    'columns',
    'renderComponents' => false,
    'customActionsView' => ''
])

<style>
    /* 1. NexusCRM Desktop Table Styling */
    .table-container { 
        background: #fff;
        border-radius: 12px;
        padding: 0;
        overflow: hidden;
    }
    
    #datatable {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        margin-top: 0 !important;
    }

    #datatable thead th {
        background-color: #f8f9fa;
        color: #4b5563;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    #datatable tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        color: #1f2937;
        font-size: 0.875rem;
        border-bottom: 1px solid #f3f4f6;
    }

    /* Nexus Soft UI Badges - Explicit Styles to force rendering */
    .badge.bg-soft-success { 
        background-color: rgba(16, 185, 129, 0.1) !important; 
        color: #10b981 !important; 
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        border-radius: 50rem;
    }
    .badge.bg-soft-primary { 
        background-color: rgba(59, 130, 246, 0.1) !important; 
        color: #3b82f6 !important; 
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        border-radius: 50rem;
    }
    .badge.bg-soft-warning { 
        background-color: rgba(245, 158, 11, 0.1) !important; 
        color: #f59e0b !important; 
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        border-radius: 50rem;
    }
    .badge.bg-soft-danger { 
        background-color: rgba(239, 68, 68, 0.1) !important; 
        color: #ef4444 !important; 
        font-weight: 600;
        padding: 0.5rem 1.2rem;
        border-radius: 50rem;
    }

    /* 2. Mobile "Card" Transformation */
    @media screen and (max-width: 767px) {
        #datatable thead { display: none; }
        #datatable, #datatable tbody, #datatable tr, #datatable td {
            display: block;
            width: 100% !important;
        }
        #datatable tr {
            margin-bottom: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            padding: 10px 0;
            background: #fff;
        }
        #datatable td {
            text-align: right; 
            padding: 12px 15px 12px 45% !important;
            position: relative;
            border-bottom: 1px solid #f3f4f6;
            min-height: 45px;
        }
        #datatable td:before {
            content: attr(data-label);
            position: absolute;
            top: 12px;
            left: 15px;
            width: 40%;
            text-align: left;
            font-weight: 600;
            color: #9ca3af;
            font-size: 0.8rem;
        }
        #datatable td:last-child {
            text-align: center;
            background: #f9fafb;
            border-bottom: none;
            padding: 15px !important;
        }
        #datatable td:last-child:before { display: none; }
    }
</style>

<div class="table-container shadow-sm border mt-4">
    <table class="table w-100" id="datatable">
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th>{{ __("deals.$col") != "deals.$col" ? __("deals.$col") : (__("tasks.$col") != "tasks.$col" ? __("tasks.$col") : ucwords(str_replace(['.', '_'], ' ', $col))) }}</th>
                @endforeach
                @if ($renderComponents && !empty($customActionsView))
                    <th class="text-center">Actions</th>
                @endif
            </tr>
        </thead>
    </table>
</div>

{{-- Load Libraries --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    // 1. Prepare labels for mobile cards
    const labels = [];
    $('#datatable thead th').each(function() {
        labels.push($(this).text().trim());
    });

    // 2. Initialize DataTable
    const table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! $ajaxUrl !!}',
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center p-3"lf>rt<"d-flex justify-content-between align-items-center p-3"ip>',
        drawCallback: function() {
            // Apply labels to cells for mobile CSS :before content
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
                render: function(data, type, row) {
                    // FIX: Return raw data to allow HTML rendering for badges
                    return data !== null ? data : ''; 
                }
            },
            @endforeach

            @if ($renderComponents && !empty($customActionsView))
            { 
                data: 'actions', 
                name: 'actions',
                orderable: false, 
                searchable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    // FIX: Return raw data to allow HTML rendering for action buttons
                    return data !== null ? data : '';
                }
            }
            @endif
        ]
    });
});
</script>