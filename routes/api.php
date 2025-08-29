<?php

use Illuminate\Support\Facades\Route;
use SirDannyMunn\CsvImport\Http\Controllers\CsvImportController;
use SirDannyMunn\CsvImport\Http\Controllers\CsvExportController;

/*
|--------------------------------------------------------------------------
| CSV Import/Export API Routes
|--------------------------------------------------------------------------
|
| These routes handle CSV import and export operations.
| All routes are prefixed with 'api/csv' by default.
|
*/

Route::prefix('api/csv')->middleware(['auth:sanctum', 'verified'])->group(function () {
    
    // Import routes
    Route::prefix('import')->group(function () {
        // Upload and start import
        Route::post('{model}', [CsvImportController::class, 'import'])
            ->name('csv.import');
        
        // Download template for model
        Route::get('{model}/template', [CsvImportController::class, 'template'])
            ->name('csv.import.template');
        
        // Get or set column mapping
        Route::match(['get', 'post'], '{model}/mapping', [CsvImportController::class, 'mapping'])
            ->name('csv.import.mapping');
        
        // Preview import with validation
        Route::post('{model}/preview', [CsvImportController::class, 'preview'])
            ->name('csv.import.preview');
        
        // Check import job progress
        Route::get('progress/{jobId}', [CsvImportController::class, 'progress'])
            ->name('csv.import.progress');
    });
    
    // Export routes
    Route::prefix('export')->group(function () {
        // Start export job
        Route::post('{model}', [CsvExportController::class, 'export'])
            ->name('csv.export');
        
        // Download exported file
        Route::get('download/{exportId}', [CsvExportController::class, 'download'])
            ->name('csv.export.download');
        
        // Check export job status
        Route::get('status/{exportId}', [CsvExportController::class, 'status'])
            ->name('csv.export.status');
    });
    
    // Utility routes
    Route::prefix('utility')->group(function () {
        // Get available models for import/export
        Route::get('models', [CsvImportController::class, 'availableModels'])
            ->name('csv.models');
        
        // Get model fields configuration
        Route::get('fields/{model}', [CsvImportController::class, 'modelFields'])
            ->name('csv.model.fields');
        
        // Cancel running job
        Route::post('cancel/{jobId}', [CsvImportController::class, 'cancelJob'])
            ->name('csv.cancel');
    });
});

// Alternative route configuration for systems without Sanctum
if (!class_exists('\Laravel\Sanctum\Sanctum')) {
    Route::prefix('api/csv')->middleware(['auth', 'web'])->group(function () {
        // Duplicate routes for non-Sanctum systems
        Route::prefix('import')->group(function () {
            Route::post('{model}', [CsvImportController::class, 'import']);
            Route::get('{model}/template', [CsvImportController::class, 'template']);
            Route::match(['get', 'post'], '{model}/mapping', [CsvImportController::class, 'mapping']);
            Route::post('{model}/preview', [CsvImportController::class, 'preview']);
            Route::get('progress/{jobId}', [CsvImportController::class, 'progress']);
        });
        
        Route::prefix('export')->group(function () {
            Route::post('{model}', [CsvExportController::class, 'export']);
            Route::get('download/{exportId}', [CsvExportController::class, 'download']);
            Route::get('status/{exportId}', [CsvExportController::class, 'status']);
        });
    });
}