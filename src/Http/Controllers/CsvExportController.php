<?php

namespace SirDannyMunn\CsvImport\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use SirDannyMunn\CsvImport\Services\ExportService;
use SirDannyMunn\CsvImport\Contracts\CsvImportable;
use Exception;

class CsvExportController extends Controller
{
    protected ExportService $exportService;
    
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

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
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
    protected function getPermissionKey(string $model, string $action = 'export'): string
    {
        // Map model identifiers to permission keys
        $permissionMap = [
            'users' => 'laundryos.export.customers',
            'customers' => 'laundryos.export.customers',
            'products' => 'laundryos.export.products',
            'orders' => 'laundryos.export.orders',
        ];
        
        return $permissionMap[$model] ?? 'laundryos.' . $action . '.' . $model;
    }

    /**
     * Export data to CSV
     */
    public function export(Request $request, string $model = 'users')
    {
        $modelClass = $this->getModelClass($model);
        
        if (!$modelClass) {
            return response()->json([
                'status' => 'error',
                'message' => "Model '{$model}' not found or does not support CSV export"
            ], 404);
        }
        
        // Check permission
        $permission = $this->getPermissionKey($model);
        if (!ns()->allowedTo($permission)) {
            return response()->json([
                'status' => 'error',
                'message' => __('You do not have permission to export :model.', ['model' => $model]),
            ], 403);
        }

        try {
            // Build query with filters
            $query = $modelClass::query();
            
            // Apply model-specific filters
            $query = $this->applyFilters($query, $request, $modelClass);
            
            // Get the data
            $data = $query->get();
            
            // Export to CSV using the correct method name
            $csv = $this->exportService->export($data, new $modelClass());
            
            $filename = $model . '_export_' . date('Y-m-d_His') . '.csv';
            
            return response($csv, 200)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
                
        } catch (Exception $e) {
            Log::error('CSV export error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('Export failed: :error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }

    /**
     * Apply filters to the query based on model type
     */
    protected function applyFilters($query, Request $request, string $modelClass)
    {
        // Apply custom filters if method exists
        if (method_exists($modelClass, 'applyCsvExportFilters')) {
            return $modelClass::applyCsvExportFilters($query, $request);
        }
        
        // Apply default filters based on model type
        $model = Str::lower(class_basename($modelClass));
        
        switch ($model) {
            case 'user':
                // Role filter
                if ($request->has('role') && $request->role) {
                    $query->whereHas('role', function($q) use ($request) {
                        $q->where('namespace', $request->role);
                    });
                }
                
                // Active status filter
                if ($request->has('active') && $request->active !== '') {
                    $query->where('active', $request->active);
                }
                
                // Date range filters
                if ($request->has('created_from') && $request->created_from) {
                    $query->where('created_at', '>=', $request->created_from . ' 00:00:00');
                }
                
                if ($request->has('created_to') && $request->created_to) {
                    $query->where('created_at', '<=', $request->created_to . ' 23:59:59');
                }
                break;
                
            case 'product':
                // Category filter
                if ($request->has('category_id') && $request->category_id) {
                    $query->where('category_id', $request->category_id);
                }
                
                // Status filter
                if ($request->has('status') && $request->status) {
                    $query->where('status', $request->status);
                }
                
                // Price range filters
                if ($request->has('price_min') && $request->price_min) {
                    $query->where('price', '>=', $request->price_min);
                }
                
                if ($request->has('price_max') && $request->price_max) {
                    $query->where('price', '<=', $request->price_max);
                }
                break;
                
            case 'order':
                // Status filter
                if ($request->has('payment_status') && $request->payment_status) {
                    $query->where('payment_status', $request->payment_status);
                }
                
                // Date range filters
                if ($request->has('date_from') && $request->date_from) {
                    $query->where('created_at', '>=', $request->date_from . ' 00:00:00');
                }
                
                if ($request->has('date_to') && $request->date_to) {
                    $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
                }
                
                // Customer filter
                if ($request->has('customer_id') && $request->customer_id) {
                    $query->where('customer_id', $request->customer_id);
                }
                break;
        }
        
        // Apply common filters
        if ($request->has('search') && $request->search) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm, $modelClass) {
                // Get searchable fields from model or use defaults
                $searchableFields = [];
                if (method_exists($modelClass, 'getCsvSearchableFields')) {
                    $searchableFields = $modelClass::getCsvSearchableFields();
                } else {
                    // Default searchable fields
                    $searchableFields = ['name', 'email', 'username', 'title', 'description'];
                }
                
                foreach ($searchableFields as $field) {
                    if (Schema::hasColumn($q->getModel()->getTable(), $field)) {
                        $q->orWhere($field, 'like', $searchTerm);
                    }
                }
            });
        }
        
        // Apply sorting
        if ($request->has('sort_by') && $request->sort_by) {
            $direction = $request->has('sort_direction') ? $request->sort_direction : 'asc';
            $query->orderBy($request->sort_by, $direction);
        } else {
            // Default sorting
            $query->latest();
        }
        
        // Apply limit if specified
        if ($request->has('limit') && $request->limit) {
            $query->limit($request->limit);
        }
        
        return $query;
    }

    /**
     * Get export statistics
     */
    public function stats(Request $request, string $model = 'users')
    {
        $modelClass = $this->getModelClass($model);
        
        if (!$modelClass) {
            return response()->json([
                'status' => 'error',
                'message' => "Model '{$model}' not found"
            ], 404);
        }
        
        // Check permission
        $permission = $this->getPermissionKey($model);
        if (!ns()->allowedTo($permission)) {
            return response()->json([
                'status' => 'error',
                'message' => __('You do not have permission to view export statistics.'),
            ], 403);
        }

        try {
            // Build query with filters
            $query = $modelClass::query();
            $query = $this->applyFilters($query, $request, $modelClass);
            
            // Get count
            $count = $query->count();
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'count' => $count,
                    'model' => $model,
                    'filters_applied' => $request->except(['sort_by', 'sort_direction', 'limit'])
                ]
            ]);
            
        } catch (Exception $e) {
            Log::error('Export stats error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to get statistics: :error', ['error' => $e->getMessage()]),
            ], 500);
        }
    }
}