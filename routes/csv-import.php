<?php

use Illuminate\Support\Facades\Route;
use SirDannyMunn\CsvImport\Http\Controllers\CsvImportController;
use SirDannyMunn\CsvImport\Http\Controllers\CsvExportController;

/**
 * CSV Import/Export Routes with Model Identifiers
 *
 * These routes support dynamic model handling through the {model} parameter.
 * Example: /dashboard/csv/users/import, /dashboard/csv/products/export
 *
 * All routes are protected with standard authentication middleware.
 * Device authorization is explicitly excluded from these routes.
 */

// Wrap all routes with authentication middleware
Route::middleware(['auth'])->group(function () {
    
    Route::prefix('dashboard/csv/{model}')->group(function () {
        
        // Import Routes
        Route::prefix('import')->group(function () {
            // Display import page
            Route::get('/', [CsvImportController::class, 'index'])
                ->name('ns.csv.import.index');
            
            // Upload and process CSV file
            Route::post('/upload', [CsvImportController::class, 'upload'])
                ->name('ns.csv.import.upload');
            
            // Preview CSV file before import
            Route::post('/preview', [CsvImportController::class, 'preview'])
                ->name('ns.csv.import.preview');
            
            // Download CSV template
            Route::get('/template', [CsvImportController::class, 'template'])
                ->name('ns.csv.import.template');
        });
        
        // Export Routes
        Route::prefix('export')->group(function () {
            // Export data to CSV
            Route::get('/', [CsvExportController::class, 'export'])
                ->name('ns.csv.export.download');
            
            // Get export statistics
            Route::get('/stats', [CsvExportController::class, 'stats'])
                ->name('ns.csv.export.stats');
        });
    });
    
    /**
     * Legacy routes for backward compatibility
     * These redirect to the new model-based routes
     */
    Route::prefix('dashboard')->group(function () {
        // Legacy customer import/export routes
        Route::get('/customers/import', function () {
            return redirect()->route('ns.csv.import.index', ['model' => 'users']);
        })->name('ns.dashboard.customers.import');
        
        Route::get('/customers/export', function () {
            return redirect()->route('ns.csv.export.download', ['model' => 'users']);
        })->name('ns.dashboard.customers.export');
        
        // Convenience route for CSV import/export page
        Route::get('/csv', function () {
            return redirect()->route('ns.csv.import.index', ['model' => 'users']);
        })->name('ns.dashboard.csv');
    });
});