<?php

use SirDannyMunn\CsvImport\Helpers\NsHelper;

if (!function_exists('ns')) {
    /**
     * Get the NS helper instance for permission checking
     * This provides compatibility when the host application doesn't have ns() defined
     * 
     * @return NsHelper|\App\Services\CoreService
     */
    function ns()
    {
        // Check if the main app has CoreService
        if (class_exists('\App\Services\CoreService') && app()->bound('App\Services\CoreService')) {
            return app('App\Services\CoreService');
        }
        
        // Otherwise use our helper
        return app(NsHelper::class);
    }
}