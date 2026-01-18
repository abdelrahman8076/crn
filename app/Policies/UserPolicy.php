<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Admin|User $user): bool
    {
        return $user->hasPermission('users.view') || $user->hasPermission('users.manage_all');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Admin|User $user, User $model): bool
    {
        // Can view if has permission and can manage the target user
        if ($user->hasPermission('users.view') || $user->hasPermission('users.manage_all')) {
            return $user->hasPermission('users.manage_all') || $user->canManage($model);
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Admin|User $user): bool
    {
        return $user->hasPermission('users.create') || $user->hasPermission('users.manage_all');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Admin|User $user, User $model): bool
    {
        // Can update if has permission and can manage the target user
        if ($user->hasPermission('users.update') || $user->hasPermission('users.manage_all')) {
            return $user->hasPermission('users.manage_all') || $user->canManage($model);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Admin|User $user, User $model): bool
    {
        // Can delete if has permission and can manage the target user
        if ($user->hasPermission('users.delete') || $user->hasPermission('users.manage_all')) {
            return $user->hasPermission('users.manage_all') || $user->canManage($model);
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Admin|User $user, User $model): bool
    {
        return $user->hasPermission('users.restore') || $user->hasPermission('users.manage_all');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Admin|User $user, User $model): bool
    {
        return $user->hasPermission('users.force_delete') || $user->hasPermission('users.manage_all');
    }
}
