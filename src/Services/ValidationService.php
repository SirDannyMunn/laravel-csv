<?php

namespace SirDannyMunn\CsvImport\Services;

use Illuminate\Support\Facades\Validator;
use SirDannyMunn\CsvImport\Contracts\CsvImportable;

class ValidationService
{
    /**
     * Validate CSV data
     */
    public function validateCsvData(array $data, CsvImportable $model): array
    {
        $rules = $model->getCsvValidationRules([]);
        $fields = $model->getCsvFields();
        $errors = [];
        $valid = true;
        
        foreach ($data as $index => $row) {
            // Build validation rules for this row
            $rowRules = [];
            foreach ($rules as $field => $rule) {
                // Skip validation for fields not in CSV
                if (!array_key_exists($field, $row)) {
                    continue;
                }
                
                // Adjust unique rules to exclude current row
                if (is_string($rule) && strpos($rule, 'unique:') !== false) {
                    // For CSV imports, we check uniqueness but allow updating existing records
                    $rowRules[$field] = $rule;
                } elseif (is_array($rule)) {
                    $rowRules[$field] = $rule;
                } else {
                    $rowRules[$field] = $rule;
                }
            }
            
            // Validate row
            $validator = Validator::make($row, $rowRules);
            
            if ($validator->fails()) {
                $valid = false;
                $errors[] = [
                    'row' => $index + 2, // +2 because of 0-index and header row
                    'errors' => $validator->errors()->toArray(),
                    'data' => $row,
                ];
            }
        }
        
        return [
            'valid' => $valid,
            'errors' => $errors,
        ];
    }
    
    /**
     * Validate single row
     */
    public function validateRow(array $row, CsvImportable $model): array
    {
        $rules = $model->getCsvValidationRules([]);
        $validator = Validator::make($row, $rules);
        
        return [
            'valid' => !$validator->fails(),
            'errors' => $validator->errors()->toArray(),
        ];
    }
    
    /**
     * Check required fields
     */
    public function checkRequiredFields(array $headers, CsvImportable $model): array
    {
        $fields = $model->getCsvFields();
        $missing = [];
        
        foreach ($fields as $field => $config) {
            if (isset($config['required']) && $config['required'] === true) {
                if (!in_array($field, $headers)) {
                    $missing[] = $field;
                }
            }
        }
        
        return $missing;
    }
    
    /**
     * Validate CSV headers
     */
    public function validateHeaders(array $headers, CsvImportable $model): array
    {
        $fields = $model->getCsvFields();
        $validFields = array_keys($fields);
        $invalidHeaders = [];
        
        foreach ($headers as $header) {
            if (!in_array($header, $validFields)) {
                $invalidHeaders[] = $header;
            }
        }
        
        return [
            'valid' => empty($invalidHeaders),
            'invalid_headers' => $invalidHeaders,
            'missing_required' => $this->checkRequiredFields($headers, $model),
        ];
    }
}