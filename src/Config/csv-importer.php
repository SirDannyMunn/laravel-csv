<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CSV Importer Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for the CSV import/export package
    |
    */

    'allowed_models' => [
        'customers' => [
            'class' => \App\Models\Customer::class,
            'chunk_size' => 100,
            'timeout' => 300,
            'permissions' => [
                'import' => 'nexopos.import.customers',
                'export' => 'nexopos.export.customers'
            ]
        ],
        'products' => [
            'class' => \App\Models\Product::class,
            'chunk_size' => 50,
            'timeout' => 600,
            'permissions' => [
                'import' => 'nexopos.import.products',
                'export' => 'nexopos.export.products'
            ]
        ],
        'orders' => [
            'class' => \App\Models\Order::class,
            'chunk_size' => 50,
            'timeout' => 600,
            'permissions' => [
                'import' => 'nexopos.import.orders',
                'export' => 'nexopos.export.orders'
            ]
        ],
        // Add more models as needed
    ],

    'defaults' => [
        'chunk_size' => 100,
        'max_file_size' => 10240, // 10MB in KB
        'allowed_extensions' => ['csv', 'txt'],
        'encoding' => 'UTF-8',
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\'
    ],

    'queue' => [
        'connection' => env('CSV_IMPORT_QUEUE_CONNECTION', 'database'),
        'queue' => env('CSV_IMPORT_QUEUE_NAME', 'csv-imports'),
        'timeout' => 600,
        'tries' => 3,
        'backoff' => [10, 30, 60] // Retry after 10s, 30s, 60s
    ],

    'storage' => [
        'disk' => env('CSV_IMPORT_STORAGE_DISK', 'local'),
        'path' => 'csv-imports',
        'temp_path' => 'csv-imports/temp',
        'keep_files_for_days' => 7 // Clean up old files after 7 days
    ],
    
    'validation' => [
        'stop_on_first_error' => false,
        'max_errors_to_display' => 100,
        'validate_headers' => true
    ],
    
    'notifications' => [
        'enabled' => true,
        'channels' => ['database'],
        'notify_on_completion' => true,
        'notify_on_error' => true
    ]
];