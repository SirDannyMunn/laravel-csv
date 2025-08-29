<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CSV Import/Export Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file defines the models that support CSV import/export
    | functionality and their specific settings.
    |
    */

    /**
     * Registered models for CSV import/export
     * 
     * Each model should implement the CsvImportable interface
     * Format: 'identifier' => 'Model\\Class\\Name'
     */
    'models' => [
        'users' => \App\Models\User::class,
        'customers' => \App\Models\Customer::class,
        'products' => \App\Models\Product::class,
        'orders' => \App\Models\Order::class,
        'order_products' => \App\Models\OrderProduct::class,
        'categories' => \App\Models\ProductCategory::class,
        'coupons' => \App\Models\Coupon::class,
        'expenses' => \App\Models\Expense::class,
        'expense_categories' => \App\Models\ExpenseCategory::class,
        'providers' => \App\Models\Provider::class,
        'procurements' => \App\Models\Procurement::class,
        'units' => \App\Models\Unit::class,
        'taxes' => \App\Models\Tax::class,
        'rewards' => \App\Models\RewardSystem::class,
        'registers' => \App\Models\Register::class,
    ],

    /**
     * Default settings for CSV processing
     */
    'defaults' => [
        'chunk_size' => 1000,           // Number of rows to process at once
        'max_file_size' => 10240,       // Maximum file size in KB (10MB)
        'allowed_extensions' => ['csv', 'txt'],
        'encoding' => 'UTF-8',
        'delimiter' => ',',
        'enclosure' => '"',
        'escape_char' => '\\',
        'skip_empty_rows' => true,
    ],

    /**
     * Import settings
     */
    'import' => [
        'validate_headers' => true,      // Validate CSV headers match expected fields
        'strict_validation' => false,    // Stop import on first validation error
        'create_missing_relations' => false, // Auto-create missing related records
        'update_existing' => true,       // Update existing records based on unique fields
        'log_imports' => true,           // Log import activities
        'send_notifications' => true,    // Send notifications after import
    ],

    /**
     * Export settings
     */
    'export' => [
        'include_headers' => true,       // Include column headers in export
        'include_timestamps' => true,    // Include created_at and updated_at
        'include_deleted' => false,      // Include soft-deleted records
        'date_format' => 'Y-m-d H:i:s', // Date format for exports
        'boolean_format' => 'yes_no',   // Format for boolean values: yes_no, true_false, 1_0
        'null_value' => '',              // How to represent NULL values
        'chunk_size' => 1000,            // Number of records to fetch at once
        'memory_limit' => '256M',        // Memory limit for export operations
    ],

    /**
     * Permission mappings for models
     * Format: 'model_identifier' => ['import' => 'permission.key', 'export' => 'permission.key']
     */
    'permissions' => [
        'users' => [
            'import' => 'laundryos.import.customers',
            'export' => 'laundryos.export.customers',
        ],
        'customers' => [
            'import' => 'laundryos.import.customers',
            'export' => 'laundryos.export.customers',
        ],
        'products' => [
            'import' => 'laundryos.import.products',
            'export' => 'laundryos.export.products',
        ],
        'orders' => [
            'import' => 'laundryos.import.orders',
            'export' => 'laundryos.export.orders',
        ],
        'categories' => [
            'import' => 'laundryos.import.categories',
            'export' => 'laundryos.export.categories',
        ],
        'expenses' => [
            'import' => 'laundryos.import.expenses',
            'export' => 'laundryos.export.expenses',
        ],
    ],

    /**
     * Model-specific configurations
     * Override default settings for specific models
     */
    'model_configs' => [
        'users' => [
            'unique_field' => 'email',           // Field to identify existing records
            'searchable_fields' => ['name', 'email', 'username'],
            'excluded_fields' => ['password', 'remember_token'],
            'required_fields' => ['name', 'email'],
            'date_fields' => ['birth_date', 'created_at', 'updated_at'],
            'boolean_fields' => ['active'],
            'cast_fields' => [
                'active' => 'boolean',
                'birth_date' => 'date',
            ],
        ],
        'products' => [
            'unique_field' => 'barcode',
            'searchable_fields' => ['name', 'barcode', 'sku'],
            'excluded_fields' => [],
            'required_fields' => ['name', 'sale_price'],
            'date_fields' => ['created_at', 'updated_at'],
            'boolean_fields' => ['status', 'stock_management'],
            'cast_fields' => [
                'sale_price' => 'float',
                'purchase_price' => 'float',
                'quantity' => 'integer',
                'status' => 'boolean',
            ],
        ],
        'orders' => [
            'unique_field' => 'code',
            'searchable_fields' => ['code', 'title'],
            'excluded_fields' => [],
            'required_fields' => ['code', 'customer_id'],
            'date_fields' => ['created_at', 'updated_at'],
            'boolean_fields' => [],
            'cast_fields' => [
                'total' => 'float',
                'discount' => 'float',
                'tax_value' => 'float',
            ],
        ],
    ],

    /**
     * Notification settings
     */
    'notifications' => [
        'channels' => ['database', 'mail'], // Notification channels to use
        'recipients' => [
            'import_complete' => 'user',     // Who receives import completion notifications
            'export_complete' => 'user',     // Who receives export completion notifications
            'import_failed' => 'admin',      // Who receives import failure notifications
        ],
    ],

    /**
     * Queue settings for large imports/exports
     */
    'queue' => [
        'enabled' => env('CSV_QUEUE_ENABLED', false),
        'connection' => env('CSV_QUEUE_CONNECTION', 'default'),
        'queue_name' => env('CSV_QUEUE_NAME', 'csv-processing'),
        'chunk_size' => 1000,  // Records per job
    ],

    /**
     * Storage settings
     */
    'storage' => [
        'disk' => env('CSV_STORAGE_DISK', 'local'),
        'path' => 'csv-imports',
        'keep_files' => false,  // Keep uploaded files after processing
        'cleanup_after_days' => 7, // Delete old files after X days
    ],
];