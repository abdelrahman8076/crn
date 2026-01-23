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
        
        // Get header row to map columns
        $headers = [];
        if (!empty($rows)) {
            $headerRow = array_map('strtolower', array_map('trim', $rows[0]));
            foreach ($fillable as $field) {
                // Try to find matching header (case-insensitive, with/without spaces)
                $headerIndex = false;
                foreach ($headerRow as $idx => $header) {
                    if (strtolower(trim($header)) === strtolower($field)) {
                        $headerIndex = $idx;
                        break;
                    }
                }
                $headers[$field] = $headerIndex !== false ? $headerIndex : null;
            }
        }

        foreach ($rows as $index => $row) {
            if ($index === 0)
                continue; // Skip header row

            $data = [];
            // Map columns by header if available, otherwise by position
            if (!empty($headers)) {
                foreach ($fillable as $field) {
                    $colIndex = $headers[$field];
                    if ($colIndex !== null && isset($row[$colIndex])) {
                        $value = trim($row[$colIndex] ?? '');
                        $data[$field] = $value === '' ? null : $value;
                    } else {
                        $data[$field] = null;
                    }
                    if ($data[$field] === null) {
                        unset($data[$field]);
                    }
                }
            } else {
                // Fallback to position-based mapping
                foreach ($fillable as $i => $field) {
                    $value = trim($row[$i] ?? '');
                    $data[$field] = $value === '' ? null : $value;
                    if ($data[$field] === null) {
                        unset($data[$field]);
                    }
                }
            }


            // Skip completely empty rows
            if (collect($data)->every(fn($v) => is_null($v))) {
                continue;
            }

            // Skip rows where both name and phone are empty (silently skip, no error)
            $nameEmpty = empty($data['name'] ?? null);
            $phoneEmpty = empty($data['phone'] ?? null);
            if ($nameEmpty && $phoneEmpty) {
                continue; // Skip silently
            }

            // Validate required columns only if row has some data
            $hasError = false;
            $missingFields = [];
            foreach ($requiredColumns as $column) {
                if (empty($data[$column] ?? null)) {
                    $hasError = true;
                    $missingFields[] = $column;
                }
            }

            if ($hasError) {
                $errors[] = array_merge([
                    'row' => $index + 1, 
                    'error' => 'Missing required fields: ' . implode(', ', $missingFields)
                ], $data);
                continue;
            }

            // Ensure phone field contains only numbers (phone is required)
            if (isset($data['phone']) && !empty($data['phone'])) {
                $data['phone'] = preg_replace('/[^0-9]/', '', $data['phone']);
                if (empty($data['phone'])) {
                    $errors[] = array_merge([
                        'row' => $index + 1, 
                        'error' => 'Phone must contain numbers'
                    ], $data);
                    continue;
                }
            } elseif (in_array('phone', $requiredColumns)) {
                // Phone is required but missing
                $errors[] = array_merge([
                    'row' => $index + 1, 
                    'error' => 'Phone is required'
                ], $data);
                continue;
            }

            // Add default values for required fields if missing
            // Status is required in the form, so set default if missing
            if (empty($data['status'] ?? null)) {
                $data['status'] = 'new';
            }
            // Source is required in the form, so set default if missing
            if (empty($data['source'] ?? null)) {
                $data['source'] = 'Unknown';
            }
            
            // Ensure phone is set (required field) - should already be validated above
            // But add it here as a safety check
            if (in_array('phone', $requiredColumns) && empty($data['phone'] ?? null)) {
                $errors[] = array_merge([
                    'row' => $index + 1, 
                    'error' => 'Phone is required and cannot be empty'
                ], $data);
                continue;
            }

            // Add timestamps if model uses them
            if ($this->model->timestamps) {
                $data['created_at'] = now();
                $data['updated_at'] = now();
            }
            
            // Remove any fields that are not in fillable to prevent errors
            $data = array_intersect_key($data, array_flip($fillable));
            
            // Ensure timestamps are included if model uses them
            if ($this->model->timestamps) {
                $data['created_at'] = $data['created_at'] ?? now();
                $data['updated_at'] = $data['updated_at'] ?? now();
            }

            $rowsToInsert[] = $data;
        }

        // Bulk insert valid rows
        $imported = 0;
        if (!empty($rowsToInsert)) {
            try {
                // Use DB facade for better error handling
                \DB::table($this->model->getTable())->insert($rowsToInsert);
                $imported = count($rowsToInsert);
            } catch (\Exception $e) {
                // If bulk insert fails, try inserting one by one to identify the problematic row
                foreach ($rowsToInsert as $rowData) {
                    try {
                        \DB::table($this->model->getTable())->insert($rowData);
                        $imported++;
                    } catch (\Exception $rowException) {
                        $errors[] = [
                            'row' => 'Unknown',
                            'error' => $rowException->getMessage(),
                            'data' => $rowData
                        ];
                    }
                }
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }
}
