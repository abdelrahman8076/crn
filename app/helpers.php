<?php
use Illuminate\Support\Facades\Auth;

if (! function_exists('isSales')) {
    function isSales(): bool
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user()->role?->name === 'Sales';
        }

        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->role?->name === 'Sales';
        }

        return false;
    }
}

if (!function_exists('canAccessAdmin')) {
    /**
     * Check if current user can access admin panel
     * 
     * @return bool
     */
    function canAccessAdmin()
    {
        // Admin guard (Admin model) - full access
        if (Auth::guard('admin')->check()) {
            return true;
        }

        // Web guard (User model) - check permissions
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($user && (
                $user->hasPermission('access-admin') || 
                in_array($user->role?->name, ['Manager', 'Sales', 'Admin'])
            )) {
                return true;
            }
        }

        return false;
    }
}
