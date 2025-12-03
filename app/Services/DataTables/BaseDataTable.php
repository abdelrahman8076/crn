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

   public function make(Request $request)
{
    $baseColumns = [];
    $relationColumns = [];

    foreach ($this->columns as $column) {
        if (str_contains($column, '.')) {
            [$relation, $relCol] = explode('.', $column, 2);
            $relationColumns[$relation][] = $relCol;
        } else {
            $baseColumns[] = $column;
        }
    }

    $dataTable = DataTables::eloquent($this->query)
        ->filter(function ($query) use ($request, $baseColumns, $relationColumns) {
            $search = $request->get('search')['value'] ?? null;

            if ($search) {
                $query->where(function ($q) use ($search, $baseColumns, $relationColumns) {
                    foreach ($baseColumns as $col) {
                        $q->orWhere($col, 'LIKE', "%$search%");
                    }

                    foreach ($relationColumns as $relation => $cols) {
                        $q->orWhereHas($relation, function ($qr) use ($cols, $search) {
                            foreach ($cols as $col) {
                                $qr->orWhere($col, 'LIKE', "%$search%");
                            }
                        });
                    }
                });
            }
        });

    // Add relation columns for display
    foreach ($relationColumns as $relation => $cols) {
        foreach ($cols as $col) {
            $dataTable->addColumn($relation . '_' . $col, function ($model) use ($relation, $col) {
                return $model->$relation ? $model->$relation->$col : 'N/A';
            });
        }
    }

    // Add 'actions' column dynamically
    if ($this->renderComponents && !empty($this->customActionsView)) {
        $dataTable->addColumn('actions', function ($model) {
            $props = array_merge(['model' => $model], $this->actionProps);
            return view($this->customActionsView, $props)->render();
        });

        $dataTable->rawColumns(['actions']);
    }

    return $dataTable->make(true);
}

}
