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
    
    [data-bs-theme="dark"] .table-container {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .table-container.shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.3) !important;
    }

    [data-bs-theme="dark"] .table-container.border {
        border-color: #334155 !important;
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

    [data-bs-theme="dark"] #datatable thead th {
        background-color: #334155 !important;
        color: #e2e8f0 !important;
        border-bottom-color: #475569 !important;
    }

    #datatable tbody td {
        padding: 16px 20px;
        vertical-align: middle;
        color: #1f2937;
        font-size: 0.875rem;
        border-bottom: 1px solid #f3f4f6;
    }

    [data-bs-theme="dark"] #datatable tbody td {
        color: #e2e8f0 !important;
        border-bottom-color: #334155 !important;
        background-color: #1e293b !important;
    }

    [data-bs-theme="dark"] #datatable tbody tr:hover td {
        background-color: #334155 !important;
    }

    /* Nexus Soft UI Badges */
    .badge.bg-soft-success { background-color: rgba(16, 185, 129, 0.1) !important; color: #10b981 !important; font-weight: 600; padding: 0.5rem 1.2rem; border-radius: 50rem; }
    .badge.bg-soft-primary { background-color: rgba(59, 130, 246, 0.1) !important; color: #3b82f6 !important; font-weight: 600; padding: 0.5rem 1.2rem; border-radius: 50rem; }
    .badge.bg-soft-warning { background-color: rgba(245, 158, 11, 0.1) !important; color: #f59e0b !important; font-weight: 600; padding: 0.5rem 1.2rem; border-radius: 50rem; }
    .badge.bg-soft-danger { background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; font-weight: 600; padding: 0.5rem 1.2rem; border-radius: 50rem; }

    /* Truncation Styling */
    .view-more-btn {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
        font-size: 0.75rem;
        margin-left: 5px;
    }
    .view-more-btn:hover { text-decoration: underline; }

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

        [data-bs-theme="dark"] #datatable tr {
            border-color: #334155 !important;
            background: #1e293b !important;
        }

        #datatable td {
            text-align: right; 
            padding: 12px 15px 12px 45% !important;
            position: relative;
            border-bottom: 1px solid #f3f4f6;
            min-height: 45px;
        }

        [data-bs-theme="dark"] #datatable td {
            border-bottom-color: #334155 !important;
            color: #e2e8f0 !important;
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

        [data-bs-theme="dark"] #datatable td:last-child {
            background: #334155 !important;
        }
        #datatable td:last-child:before { display: none; }
    }
</style>

<div class="table-container shadow-sm border mt-4">
    <table class="table w-100" id="datatable">
        <thead>
            <tr>
                @foreach ($columns as $col)
                    <th>{{ __("deals.$col") != "deals.$col" ? __("deals.$col") : (__("tasks.$col") != "tasks.$col" ? __("tasks.$col") : (__("users.$col") != "users.$col" ? __("users.$col") : ucwords(str_replace(['.', '_'], ' ', $col)))) }}</th>
                @endforeach
                @if ($renderComponents && !empty($customActionsView))
                    <th class="text-center">Actions</th>
                @endif
            </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="textDetailModal" tabindex="-1" aria-labelledby="textDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="textDetailModalLabel">Detail View</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalFullContent" style="white-space: pre-wrap; word-break: break-all; color: #1f2937;">
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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
        ajax: {
            url: '{!! $ajaxUrl !!}',
            error: function(xhr, error, thrown) {
                console.error('DataTables Error:', error, thrown);
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    alert('Error: ' + xhr.responseJSON.error);
                } else if (xhr.status === 403) {
                    alert('You do not have permission to access this data.');
                } else {
                    alert('An error occurred while loading the data. Please try again.');
                }
            }
        },
        autoWidth: false,
        dom: '<"d-flex justify-content-between align-items-center p-3"lf>rt<"d-flex justify-content-between align-items-center p-3"ip>',
        drawCallback: function(settings) {
            // Apply labels to cells for mobile CSS :before content
            $('#datatable tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    $(this).attr('data-label', labels[index]);
                });
            });
            
            // Check for error in response
            var json = settings.json;
            if (json && json.error) {
                alert('Error: ' + json.error);
            }
        },
        columns: [
            @foreach ($columns as $index => $col)
            { 
                data: '{{ $col }}', 
                name: '{{ $col }}',
                render: function(data, type, row) {
                    if (data === null || data === '') return '';

                    // Apply 20 character limit only for display
                    if (type === 'display' && data.toString().length > 20) {
                        const shortText = data.toString().substring(0, 20);
                        // Store the full text in a data attribute (escaped)
                        const fullText = data.toString().replace(/"/g, '&quot;'); 
                        
                        return `<span>${shortText}...</span> 
                                <a href="javascript:void(0)" 
                                   class="view-more-btn" 
                                   data-content="${fullText}">more</a>`;
                    }
                    return data; 
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
                    return data !== null ? data : '';
                }
            }
            @endif
        ]
    });

    // 3. Handle click on "more" link
    $('#datatable').on('click', '.view-more-btn', function(e) {
        e.preventDefault();
        const content = $(this).attr('data-content');
        $('#modalFullContent').text(content); // Use .text() for security unless content is HTML
        
        const modalEl = document.getElementById('textDetailModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    });
});
</script>