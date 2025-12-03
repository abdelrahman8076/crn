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
        $clientsToInsert = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // skip header

            $data = [];
            foreach ($fillable as $i => $field) {
                $data[$field] = trim($row[$i] ?? '');
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

            $clientsToInsert[] = $data;
        }

        // Bulk insert for performance
        $imported = 0;
        if (!empty($clientsToInsert)) {
            $this->model->insert($clientsToInsert);
            $imported = count($clientsToInsert);
        }

        return [
            'imported' => $imported,
            'errors'   => $errors,
        ];
    }
}
