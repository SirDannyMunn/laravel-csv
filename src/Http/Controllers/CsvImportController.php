<?php

namespace SirDannyMunn\CsvImport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SirDannyMunn\CsvImport\Services\ImportService;
use SirDannyMunn\CsvImport\Services\ValidationService;
use SirDannyMunn\CsvImport\Services\MappingService;
use SirDannyMunn\CsvImport\Contracts\CsvImportable;
use Exception;

class CsvImportController extends Controller
{
    protected ImportService $importService;
    protected ValidationService $validationService;
    protected MappingService $mappingService;
    
    /**
     * Model registry for supported models
     */
    protected array $modelRegistry = [
        'users' => \App\Models\User::class,
        'products' => \App\Models\Product::class,
        'customers' => \App\Models\Customer::class,
        'orders' => \App\Models\Order::class,
        // Add more models as needed
    ];

    public function __construct(
        ImportService $importService,
        ValidationService $validationService,
        MappingService $mappingService
    ) {
        $this->importService = $importService;
        $this->validationService = $validationService;
        $this->mappingService = $mappingService;
    }

    /**
     * Get model class from identifier
     */
    protected function getModelClass(string $modelIdentifier): ?string
    {
        // Check registry first
        if (isset($this->modelRegistry[$modelIdentifier])) {
            return $this->modelRegistry[$modelIdentifier];
        }
        
        // Try to resolve from namespace
        $className = Str::studly(Str::singular($modelIdentifier));
        $possibleClasses = [
            "App\\Models\\{$className}",
            "App\\{$className}",
        ];
        
        foreach ($possibleClasses as $class) {
            if (class_exists($class) && in_array(CsvImportable::class, class_implements($class))) {
                return $class;
            }
        }
        
        return null;
    }

    /**
     * Get permission key for a model
     */
    protected function getPermissionKey(string $model): string
    {
        // Map model identifiers to permission keys
        $permissionMap = [
            'users' => 'laundryos.import.customers',
            'customers' => 'laundryos.import.customers',
            'products' => 'laundryos.import.products',
            'orders' => 'laundryos.import.orders',
        ];
        
        return $permissionMap[$model] ?? 'laundryos.import.' . $model;
    }

    /**
     * Show the import page
     */
    public function index(string $model = 'users')
    {
        $modelClass = $this->getModelClass($model);
        
        if (!$modelClass) {
            abort(404, "Model '{$model}' not found or does not support CSV import");
        }
        
        // Check permission
        $permission = $this->getPermissionKey($model);
        if (!ns()->allowedTo($permission)) {
            return redirect()->route('ns.dashboard.index')
                ->with('error', __('You do not have permission to import :model.', ['model' => $model]));
        }

        return inertia('CsvImportExport/Index', [
            'model' => $model,
            'title' => Str::title(str_replace('_', ' ', $model)) . ' Import/Export',
            'description' => 'Import and export ' . Str::lower(str_replace('_', ' ', Str::plural($model))) . ' using CSV files.',
            'exportFilters' => $this->getExportFilters($modelClass),
        ]);
    }
    
    /**
     * Get export filters for a model
     */
    protected function getExportFilters(string $modelClass): array
    {
        if (method_exists($modelClass, 'getCsvExportFilters')) {
            return $modelClass::getCsvExportFilters();
        }
        
        // Default filters based on model type
        $model = Str::lower(class_basename($modelClass));
        
        switch ($model) {
            case 'user':
                return [
                    ['name' => 'role', 'label' => 'Role', 'type' => 'select', 'options' => [
                        ['value' => 'admin', 'label' => 'Admin'],
                        ['value' => 'manager', 'label' => 'Manager'],
                        ['value' => 'cashier', 'label' => 'Cashier'],
                    ]],
                    ['name' => 'active', 'label' => 'Status', 'type' => 'select', 'options' => [
                        ['value' => '1', 'label' => 'Active'],
                        ['value' => '0', 'label' => 'Inactive'],
                    ]],
                    ['name' => 'created_from', 'label' => 'Created From', 'type' => 'date'],
                    ['name' => 'created_to', 'label' => 'Created To', 'type' => 'date'],
                ];
            default:
                return [];
        }
    }

    /**
     * Upload and process CSV file
     */
    public function upload(Request $request, string $model = 'users')
    {
        $modelClass = $this->getModelClass($model);
        
        if (!$modelClass) {
            return response()->json([
                'status' => 'error',
                'message' => "Model '{$model}' not found or does not support CSV import"
            ], 404);
        }
        
        // Check permission
        $permission = $this->getPermissionKey($model);
        if (!ns()->allowedTo($permission)) {
            return response()->json([
                'status' => 'error',
                'message' => __('You do not have permission to import :model.', ['model' => $model]),
            ], 403);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $modelInstance = new $modelClass();
            
            // Parse CSV file
            $data = $this->importService->parseCsvFile($file);
            
            // Validate data
            $validation = $this->validationService->validateCsvData($data, $modelInstance);
            
            if (!$validation['valid']) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('Validation failed'),
                    'errors' => $validation['errors'],
                ], 422);
            }
            
            // Import data
            DB::beginTransaction();
            try {
                $result = $this->importService->import($data, $modelInstance);
                DB::commit();
                
                $modelName = Str::plural(Str::lower(class_basename($modelClass)));
                
                return response()->json([
                    'status' => 'success',
                    'message' => __(':count :model imported successfully.', [
                        'count' => $result['imported'],
                        'model' => $modelName
                    ]),
                    'data' => $result,
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (Exception $e) {
            Log::error('CSV import error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('Import failed: :error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * Download CSV template
     */
    public function template(string $model = 'users')
    {
        $modelClass = $this->getModelClass($model);
        
        if (!$modelClass) {
            return response()->json([
                'status' => 'error',
                'message' => "Model '{$model}' not found or does not support CSV import"
            ], 404);
        }
        
        // Check permission
        $permission = $this->getPermissionKey($model);
        if (!ns()->allowedTo($permission)) {
            return redirect()->route('ns.dashboard.index')
                ->with('error', __('You do not have permission to download the template.'));
        }

        $modelInstance = new $modelClass();
        $fields = $modelInstance->getCsvFields();
        
        // Create CSV content
        $csv = [];
        $headers = [];
        $examples = [];
        
        foreach ($fields as $field => $config) {
            $headers[] = $field;
            $examples[] = $config['example'] ?? '';
        }
        
        $csv[] = $headers;
        
        // Add sample row if method exists
        if (method_exists($modelInstance, 'getCsvSampleRow')) {
            $csv[] = $modelInstance->getCsvSampleRow();
        } else {
            $csv[] = $examples;
        }
        
        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);
        
        $filename = $model . '_import_template.csv';
        
        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Preview import data
     */
    public function preview(Request $request, string $model = 'users')
    {
        $modelClass = $this->getModelClass($model);
        
        if (!$modelClass) {
            return response()->json([
                'status' => 'error',
                'message' => "Model '{$model}' not found or does not support CSV import"
            ], 404);
        }
        
        // Check permission
        $permission = $this->getPermissionKey($model);
        if (!ns()->allowedTo($permission)) {
            return response()->json([
                'status' => 'error',
                'message' => __('You do not have permission to preview imports.'),
            ], 403);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        try {
            $file = $request->file('csv_file');
            $modelInstance = new $modelClass();
            
            // Parse CSV file
            $data = $this->importService->parseCsvFile($file);
            
            // Validate data
            $validation = $this->validationService->validateCsvData($data, $modelInstance);
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'preview' => array_slice($data, 0, 5), // Show first 5 rows
                    'total' => count($data),
                    'valid' => $validation['valid'],
                    'errors' => $validation['errors'] ?? [],
                ],
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => __('Preview failed: :error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }
}