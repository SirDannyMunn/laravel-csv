<?php

namespace SirDannyMunn\CsvImport\Services;

use SirDannyMunn\CsvImport\Contracts\CsvImportable;

class MappingService
{
    /**
     * Map CSV columns to model fields
     */
    public function mapColumns(array $csvHeaders, CsvImportable $model): array
    {
        $modelFields = $model->getCsvFields();
        $mapping = [];
        
        foreach ($csvHeaders as $csvHeader) {
            $csvHeaderLower = strtolower(trim($csvHeader));
            
            // Try exact match first
            if (isset($modelFields[$csvHeader])) {
                $mapping[$csvHeader] = $csvHeader;
                continue;
            }
            
            // Try case-insensitive match
            foreach ($modelFields as $field => $config) {
                if (strtolower($field) === $csvHeaderLower) {
                    $mapping[$csvHeader] = $field;
                    break;
                }
                
                // Try matching by label
                if (isset($config['label'])) {
                    $label = strtolower(trim($config['label']));
                    if ($label === $csvHeaderLower) {
                        $mapping[$csvHeader] = $field;
                        break;
                    }
                }
            }
        }
        
        return $mapping;
    }
    
    /**
     * Apply mapping to data row
     */
    public function applyMapping(array $row, array $mapping): array
    {
        $mappedRow = [];
        
        foreach ($row as $key => $value) {
            if (isset($mapping[$key])) {
                $mappedRow[$mapping[$key]] = $value;
            } else {
                $mappedRow[$key] = $value;
            }
        }
        
        return $mappedRow;
    }
    
    /**
     * Auto-detect delimiter
     */
    public function detectDelimiter(string $csvContent): string
    {
        $delimiters = [',', ';', '\t', '|'];
        $counts = [];
        
        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($csvContent, $delimiter);
        }
        
        return array_search(max($counts), $counts);
    }
    
    /**
     * Transform value based on field configuration
     */
    public function transformValue($value, array $fieldConfig)
    {
        // Handle empty values
        if ($value === '' || $value === null) {
            return $fieldConfig['default'] ?? null;
        }
        
        // Handle boolean values
        if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'boolean') {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on']);
        }
        
        // Handle date values
        if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'date') {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }
        
        // Handle datetime values
        if (isset($fieldConfig['type']) && $fieldConfig['type'] === 'datetime') {
            try {
                return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
            } catch (\Exception $e) {
                return null;
            }
        }
        
        // Handle numeric values
        if (isset($fieldConfig['type']) && in_array($fieldConfig['type'], ['integer', 'number'])) {
            return is_numeric($value) ? (float) $value : null;
        }
        
        return $value;
    }
    
    /**
     * Get suggested mappings for unmapped columns
     */
    public function getSuggestedMappings(array $unmappedColumns, CsvImportable $model): array
    {
        $modelFields = $model->getCsvFields();
        $suggestions = [];
        
        foreach ($unmappedColumns as $column) {
            $columnLower = strtolower(trim($column));
            $bestMatch = null;
            $bestScore = 0;
            
            foreach ($modelFields as $field => $config) {
                // Calculate similarity score
                $fieldLower = strtolower($field);
                $score = 0;
                
                // Check for partial matches
                if (strpos($columnLower, $fieldLower) !== false || strpos($fieldLower, $columnLower) !== false) {
                    $score = 50;
                }
                
                // Use levenshtein distance for similarity
                $distance = levenshtein($columnLower, $fieldLower);
                if ($distance < 5) {
                    $score = max($score, 100 - ($distance * 20));
                }
                
                // Check label similarity if available
                if (isset($config['label'])) {
                    $labelLower = strtolower(trim($config['label']));
                    if (strpos($columnLower, $labelLower) !== false || strpos($labelLower, $columnLower) !== false) {
                        $score = max($score, 60);
                    }
                    
                    $labelDistance = levenshtein($columnLower, $labelLower);
                    if ($labelDistance < 5) {
                        $score = max($score, 100 - ($labelDistance * 20));
                    }
                }
                
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $field;
                }
            }
            
            if ($bestMatch && $bestScore > 30) {
                $suggestions[$column] = [
                    'field' => $bestMatch,
                    'confidence' => $bestScore,
                ];
            }
        }
        
        return $suggestions;
    }
}