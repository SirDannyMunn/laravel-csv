<?php

namespace SirDannyMunn\CsvImport\Services;

use Illuminate\Http\UploadedFile;
use SirDannyMunn\CsvImport\Contracts\CsvImportable;
use Exception;

class ImportService
{
    /**
     * Parse CSV file into array
     */
    public function parseCsvFile(UploadedFile $file): array
    {
        $data = [];
        $headers = null;
        
        if (($handle = fopen($file->getPathname(), 'r')) !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                if ($headers === null) {
                    $headers = array_map('trim', $row);
                    continue;
                }
                
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Map row to associative array
                $mappedRow = [];
                foreach ($headers as $index => $header) {
                    $mappedRow[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                }
                
                $data[] = $mappedRow;
            }
            fclose($handle);
        }
        
        if (empty($data)) {
            throw new Exception(__('No data found in CSV file'));
        }
        
        return $data;
    }
    
    /**
     * Import data into model
     */
    public function import(array $data, CsvImportable $model): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                // Process row through model's custom logic
                if (method_exists($model, 'processCsvRow')) {
                    $row = $model->processCsvRow($row);
                }
                
                // Create new instance
                $instance = new (get_class($model));
                
                // Fill with data
                foreach ($row as $key => $value) {
                    if (in_array($key, $instance->getFillable())) {
                        $instance->$key = $value;
                    }
                }
                
                // Save
                $instance->save();
                
                // Handle post-import actions
                if (method_exists($model, 'afterCsvImport')) {
                    $model->afterCsvImport($instance);
                }
                
                // Assign default customer role if it's a User model
                if ($instance instanceof \App\Models\User) {
                    $instance->assignRole(\App\Models\Role::STORECUSTOMER);
                }
                
                $imported++;
            } catch (Exception $e) {
                $failed++;
                $errors[] = [
                    'row' => $index + 1,
                    'error' => $e->getMessage(),
                    'data' => $row,
                ];
            }
        }
        
        return [
            'imported' => $imported,
            'failed' => $failed,
            'errors' => $errors,
            'total' => count($data),
        ];
    }
    
    /**
     * Import single row
     */
    public function importRow(array $row, CsvImportable $model)
    {
        // Process row through model's custom logic
        if (method_exists($model, 'processCsvRow')) {
            $row = $model->processCsvRow($row);
        }
        
        // Create new instance
        $instance = new (get_class($model));
        
        // Fill with data
        foreach ($row as $key => $value) {
            if (in_array($key, $instance->getFillable())) {
                $instance->$key = $value;
            }
        }
        
        // Save
        $instance->save();
        
        // Handle post-import actions
        if (method_exists($model, 'afterCsvImport')) {
            $model->afterCsvImport($instance);
        }
        
        return $instance;
    }
}