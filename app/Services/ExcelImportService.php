<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportService
{
    protected Model $model;

    /**
     * Constructor: accept any Eloquent model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Import data from Excel
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $requiredColumns Columns that must be present (for validation)
     * @return array ['imported' => int, 'errors' => array]
     */
    public function import($file, array $requiredColumns = [])
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        $fillable = $this->model->getFillable();
        $rowsToInsert = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 0)
                continue; // Skip header row

            $data = [];
            foreach ($fillable as $i => $field) {
                $value = trim($row[$i] ?? '');
                $data[$field] = $value === '' ? null : $value;
                if ($data[$field] === null) {
                    unset($data[$field]);

                }
            }


            // Skip completely empty rows
            if (collect($data)->every(fn($v) => is_null($v))) {
                continue;
            }

            // Validate required columns
            $hasError = false;
            foreach ($requiredColumns as $column) {
                if (empty($data[$column])) {
                    $hasError = true;
                    break;
                }
            }

            if ($hasError) {
                $errors[] = array_merge(['row' => $index + 1], $data);
                continue;
            }

            // Add timestamps if model uses them
            if ($this->model->timestamps) {
                $data['created_at'] = now();
                $data['updated_at'] = now();
            }

            $rowsToInsert[] = $data;
        }

        // Bulk insert valid rows
        $imported = 0;
        if (!empty($rowsToInsert)) {
            $this->model->insert($rowsToInsert);
            $imported = count($rowsToInsert);
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }
}
