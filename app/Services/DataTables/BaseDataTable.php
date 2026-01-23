<?php
// app/Services/DataTableService.php
namespace App\Services\DataTables;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\Facades\DataTables;

class BaseDataTable
{
    protected Builder $query;
    protected array $columns;
    protected bool $renderComponents;
    protected ?string $customActionsView;
    protected array $actionProps = []; // Generic props for component
        protected array $overrides = [];


    public function __construct(
        Builder $query,
        array $columns,
        bool $renderComponents,
        ?string $customActionsView = null // Allow null or empty string
    ) {
        $this->query = $query;
        $this->columns = $columns;
        $this->renderComponents = $renderComponents;
        $this->customActionsView = $customActionsView;
    }

    /**
     * Set dynamic props for action component
     */
    public function setActionProps(array $props): self
    {
        $this->actionProps = $props;
        return $this;
    }

public function editColumn($column, callable $callback)
{
    $this->overrides[$column] = $callback;
    return $this;
}

   public function make(Request $request)
{
    $baseColumns = [];
    $relationColumns = [];
    $virtualColumns = [];
    // Map virtual/computed columns to their actual database columns for search
    $virtualColumnMap = [
        'formatted_amount' => 'amount', // formatted_amount is a virtual attribute, search in amount instead
        'stage_badge' => 'stage', // stage_badge is a virtual attribute, search in stage instead
    ];

    foreach ($this->columns as $column) {
        if (str_contains($column, '.')) {
            [$relation, $relCol] = explode('.', $column, 2);
            $relationColumns[$relation][] = $relCol;
        } else {
            // Check if this is a virtual column
            if (isset($virtualColumnMap[$column])) {
                $virtualColumns[] = $column;
                // Map virtual columns to their actual database columns for search
                $searchColumn = $virtualColumnMap[$column];
                if (!in_array($searchColumn, $baseColumns)) {
                    $baseColumns[] = $searchColumn;
                }
            } else {
                // Regular database column
                $baseColumns[] = $column;
            }
        }
    }

    // Apply search filter to the query before passing to DataTables
    $search = $request->input('search.value');
    if (!empty($search)) {
        $searchTerm = trim($search);
        $this->query->where(function ($q) use ($searchTerm, $baseColumns, $relationColumns) {
            foreach ($baseColumns as $col) {
                // Only search in actual database columns (virtual columns are already mapped)
                $q->orWhere($col, 'LIKE', "%{$searchTerm}%");
            }

            foreach ($relationColumns as $relation => $cols) {
                $q->orWhereHas($relation, function ($qr) use ($cols, $searchTerm) {
                    foreach ($cols as $col) {
                        $qr->orWhere($col, 'LIKE', "%{$searchTerm}%");
                    }
                });
            }
        });
    }

    // Build the DataTable
    // Note: Search filter is already applied to $this->query above
    // The filter() callback below prevents DataTables from doing its own automatic search
    $dataTable = DataTables::eloquent($this->query)
        ->filter(function ($query) use ($request) {
            // We've already applied the search filter to the query above
            // This empty callback prevents DataTables from doing its own automatic column search
            // which would conflict with our custom search logic
            $search = $request->input('search.value');
            if (empty($search)) {
                // If no search term, let DataTables handle it normally
                return;
            }
            // Search is already applied to $this->query, so we don't need to do anything here
        });

    // Collect raw columns (columns that contain HTML)
    $rawColumns = [];

    // Apply column overrides if they exist
    foreach ($this->overrides as $column => $callback) {
        $dataTable->editColumn($column, $callback);
    }

    // Add relation columns for display
    foreach ($relationColumns as $relation => $cols) {
        foreach ($cols as $col) {
            $columnName = $relation . '.' . $col; // Use dot notation to match column definition
            $dataTable->addColumn($columnName, function ($model) use ($relation, $col) {
                return $model->$relation ? $model->$relation->$col : 'N/A';
            });
        }
    }

    // Add virtual/computed columns for display (these are accessors, not database columns)
    foreach ($virtualColumns as $column) {
        // This is a virtual column, add it for display
        $dataTable->addColumn($column, function ($model) use ($column) {
            return $model->$column ?? '';
        });
        // Mark as raw column if it contains HTML (like formatted_amount)
        if (in_array($column, ['formatted_amount', 'stage_badge'])) {
            $rawColumns[] = $column;
        }
    }

    // Add 'actions' column dynamically
    if ($this->renderComponents && !empty($this->customActionsView)) {
        $dataTable->addColumn('actions', function ($model) {
            $props = array_merge(['model' => $model], $this->actionProps);
            return view($this->customActionsView, $props)->render();
        });

        $rawColumns[] = 'actions';
    }

    // Set all raw columns at once
    if (!empty($rawColumns)) {
        $dataTable->rawColumns($rawColumns);
    }

    return $dataTable->make(true);
}

}
