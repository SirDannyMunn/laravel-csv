<?php

namespace SirDannyMunn\CsvImport\Helpers;

use Illuminate\Support\Facades\Auth;

class NsHelper
{
    /**
     * Check if user has permission
     * This provides a fallback implementation when the main app doesn't have ns()->allowedTo()
     * 
     * @param string $permission
     * @return bool
     */
    public function allowedTo(string $permission): bool
    {
        // Check if the main app has CoreService with allowedTo method
        if (class_exists('\App\Services\CoreService') && app()->bound('App\Services\CoreService')) {
            $coreService = app('App\Services\CoreService');
            if (method_exists($coreService, 'allowedTo')) {
                return $coreService->allowedTo($permission);
            }
        }

        // Fallback to checking user permissions directly
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Check if user has the permission through roles or direct assignment
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }
        
        // Check if user has can() method (Laravel's authorization)
        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        // Final fallback - check if user is authenticated (very basic)
        // In production, you should implement proper permission checking
        return Auth::check();
    }

    /**
     * Get the current tenant context
     * 
     * @return mixed
     */
    public function tenant()
    {
        // Check if main app has tenant context through CoreService
        if (class_exists('\App\Services\CoreService') && app()->bound('App\Services\CoreService')) {
            $coreService = app('App\Services\CoreService');
            if (method_exists($coreService, 'tenant')) {
                return $coreService->tenant();
            }
        }
        
        // Check if main app has TenantService
        if (class_exists('\App\Services\TenantService') && app()->bound('App\Services\TenantService')) {
            return app('App\Services\TenantService')->tenant();
        }

        // Return null if no tenant system exists
        return null;
    }
    
    /**
     * Get authenticated user
     * 
     * @return mixed
     */
    public function user()
    {
        return Auth::user();
    }
    
    /**
     * Get user ID
     * 
     * @return int|null
     */
    public function userId()
    {
        return Auth::id();
    }
}