<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait ChecksPermissions
{
    /**
     * Check if current user (admin or user) has a specific permission.
     */
    protected function checkPermission(string $permissionSlug): bool
    {
        // Admin guard (Admin model) - Super admins have full access by default
        // If they have a role, check permissions. If no role, allow full access.
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if (!$admin) {
                return false;
            }
            
            // If admin has no role, give full access (legacy super admin behavior)
            if (!$admin->role_id || !$admin->role) {
                return true; // Super admin has full access
            }
            
            // If admin has a role, check permissions
            return $admin->hasPermission($permissionSlug);
        }

        // Web guard (User model)
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if (!$user || !$user->role) {
                return false;
            }
            return $user->hasPermission($permissionSlug);
        }

        return false;
    }

    /**
     * Abort if user doesn't have permission.
     */
    protected function requirePermission(string $permissionSlug): void
    {
        if (!$this->checkPermission($permissionSlug)) {
            abort(403, 'You do not have permission to perform this action.');
        }
    }
}
