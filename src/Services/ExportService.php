<?php

namespace SirDannyMunn\CsvImport\Services;

use Illuminate\Support\Collection;
use SirDannyMunn\CsvImport\Contracts\CsvImportable;

class ExportService
{
    /**
     * Export collection to CSV
     */
    public function export(Collection $collection, CsvImportable $model): string
    {
        $fields = $model->getCsvFields();
        $headers = array_keys($fields);
        
        // Create CSV content
        $output = fopen('php://temp', 'r+');
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write data
        foreach ($collection as $item) {
            $row = [];
            
            // Use model's custom export method if available
            if (method_exists($item, 'toCsvArray')) {
                $csvData = $item->toCsvArray();
                foreach ($headers as $header) {
                    $row[] = $csvData[$header] ?? '';
                }
            } else {
                // Default export logic
                foreach ($headers as $header) {
                    $value = $item->$header ?? '';
                    
                    // Handle relationships
                    if (isset($fields[$header]['relationship'])) {
                        $relationship = $fields[$header]['relationship'];
                        $value = $item->$relationship->name ?? '';
                    }
                    
                    $row[] = $value;
                }
            }
            
            fputcsv($output, $row);
        }
        
        // Get CSV content
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
    
    /**
     * Export single model to CSV row
     */
    public function exportRow($model, CsvImportable $template): array
    {
        $fields = $template->getCsvFields();
        $row = [];
        
        // Use model's custom export method if available
        if (method_exists($model, 'toCsvArray')) {
            $csvData = $model->toCsvArray();
            foreach (array_keys($fields) as $header) {
                $row[] = $csvData[$header] ?? '';
            }
        } else {
            // Default export logic
            foreach ($fields as $field => $config) {
                $value = $model->$field ?? '';
                
                // Handle relationships
                if (isset($config['relationship'])) {
                    $relationship = $config['relationship'];
                    $value = $model->$relationship->name ?? '';
                }
                
                $row[] = $value;
            }
        }
        
        return $row;
    }
    
    /**
     * Generate CSV from array data
     */
    public function generateCsv(array $data, array $headers = null): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Write headers if provided
        if ($headers !== null) {
            fputcsv($output, $headers);
        } elseif (!empty($data)) {
            // Use first row keys as headers
            fputcsv($output, array_keys(reset($data)));
        }
        
        // Write data
        foreach ($data as $row) {
            fputcsv($output, is_array($row) ? $row : (array) $row);
        }
        
        // Get CSV content
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }
}