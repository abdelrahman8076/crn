<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthorizationService
{
    /**
     * Get the currently authenticated user (Admin or User)
     */
    public function getCurrentUser(): Admin|User|null
    {
        return Auth::guard('admin')->user() ?? Auth::guard('web')->user();
    }

    /**
     * Check if current user has permission
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasPermission($permission);
    }

    /**
     * Check if current user has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->hasAnyPermission($permissions);
    }

    /**
     * Check if current user can manage another user/admin
     */
    public function canManage(Admin|User $target): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        return $user->canManage($target);
    }

    /**
     * Filter query to only show records the current user can manage
     */
    public function filterManageable($query, string $modelType = 'user')
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return $query->whereRaw('0 = 1'); // No access
        }
        
        // If has manage_all permission, show all
        if ($user->hasPermission($modelType . 's.manage_all')) {
            return $query;
        }
        
        // Filter by hierarchy
        $manageableModels = $user->getManageableModels($modelType);
        $ids = $manageableModels->pluck('id')->toArray();
        
        if (empty($ids)) {
            return $query->whereRaw('0 = 1'); // No access
        }
        
        return $query->whereIn('id', $ids);
    }

    /**
     * Check if current user can view a specific resource
     */
    public function canView($resource, string $permissionPrefix = 'view'): bool
    {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            return false;
        }
        
        // Get resource type
        $resourceType = strtolower(class_basename($resource));
        
        // Check specific permission
        if ($user->hasPermission($resourceType . '.' . $permissionPrefix)) {
            return true;
        }
        
        // Check manage permission
        if ($user->hasPermission($resourceType . '.manage_all')) {
            return true;
        }
        
        // Check if user can manage the resource owner
        if (isset($resource->user_id)) {
            $owner = User::find($resource->user_id);
            if ($owner && $user->canManage($owner)) {
                return true;
            }
        }
        
        return false;
    }
}
