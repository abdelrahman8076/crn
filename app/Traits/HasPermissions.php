<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use App\Models\ModelRole;
use App\Models\ModelPermission;
use App\Models\Position;
use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Get all roles assigned to this model (Admin/User)
     */
    public function modelRoles()
    {
        return $this->morphMany(ModelRole::class, 'model');
    }

    /**
     * Get all direct permissions assigned to this model
     */
    public function modelPermissions()
    {
        return $this->morphMany(ModelPermission::class, 'model');
    }

    /**
     * Get the position of this model
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Assign a role to this model
     */
    public function assignRole($role): void
    {
        $roleId = is_string($role) ? Role::where('name', $role)->firstOrFail()->id : $role;
        
        $this->modelRoles()->firstOrCreate([
            'role_id' => $roleId,
        ]);
    }

    /**
     * Remove a role from this model
     */
    public function removeRole($role): void
    {
        $roleId = is_string($role) ? Role::where('name', $role)->firstOrFail()->id : $role;
        
        $this->modelRoles()->where('role_id', $roleId)->delete();
    }

    /**
     * Sync roles (replace all roles with the given ones)
     */
    public function syncRoles(array $roleIds): void
    {
        $this->modelRoles()->delete();
        
        foreach ($roleIds as $roleId) {
            $this->modelRoles()->create(['role_id' => $roleId]);
        }
    }

    /**
     * Grant a permission directly to this model
     */
    public function grantPermission($permission, bool $granted = true): void
    {
        $permissionId = is_string($permission) 
            ? Permission::where('name', $permission)->firstOrFail()->id 
            : $permission;
        
        $this->modelPermissions()->updateOrCreate(
            ['permission_id' => $permissionId],
            ['granted' => $granted]
        );
    }

    /**
     * Revoke a permission from this model
     */
    public function revokePermission($permission): void
    {
        $permissionId = is_string($permission) 
            ? Permission::where('name', $permission)->firstOrFail()->id 
            : $permission;
        
        $this->modelPermissions()->where('permission_id', $permissionId)->delete();
    }

    /**
     * Sync permissions (replace all direct permissions)
     */
    public function syncPermissions(array $permissions, bool $granted = true): void
    {
        $this->modelPermissions()->delete();
        
        foreach ($permissions as $permissionId) {
            $this->modelPermissions()->create([
                'permission_id' => $permissionId,
                'granted' => $granted,
            ]);
        }
    }

    /**
     * Get all roles assigned to this model
     */
    public function getRoles(): Collection
    {
        return $this->modelRoles()->with('role')->get()->pluck('role');
    }

    /**
     * Get all permissions this model has (from roles + direct)
     */
    public function getAllPermissions(): Collection
    {
        $permissions = collect();
        
        // Get permissions from roles
        foreach ($this->getRoles() as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        
        // Get direct permissions (overrides role permissions)
        $directPermissions = $this->modelPermissions()
            ->with('permission')
            ->get()
            ->mapWithKeys(function ($modelPermission) {
                return [$modelPermission->permission->name => $modelPermission->granted];
            });
        
        // Apply direct permission overrides
        foreach ($directPermissions as $permissionName => $granted) {
            if ($granted) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $permissions = $permissions->reject(function ($p) use ($permissionName) {
                        return $p->name === $permissionName;
                    });
                    $permissions->push($permission);
                }
            } else {
                // Remove permission if explicitly denied
                $permissions = $permissions->reject(function ($p) use ($permissionName) {
                    return $p->name === $permissionName;
                });
            }
        }
        
        return $permissions->unique('id');
    }

    /**
     * Check if model has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        // Check direct permissions first (they override role permissions)
        $directPermission = $this->modelPermissions()
            ->whereHas('permission', function ($query) use ($permission) {
                $query->where('name', $permission);
            })
            ->first();
        
        if ($directPermission) {
            return $directPermission->granted;
        }
        
        // Check role permissions
        foreach ($this->getRoles() as $role) {
            if ($role->permissions()->where('name', $permission)->exists()) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if model has any of the given permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if model has all of the given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Check if model has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->getRoles()->contains('name', $role);
    }

    /**
     * Check if model has any of the given roles
     */
    public function hasAnyRole(array $roles): bool
    {
        $roleNames = $this->getRoles()->pluck('name')->toArray();
        
        foreach ($roles as $role) {
            if (in_array($role, $roleNames)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Check if this model can manage another model based on hierarchy
     */
    public function canManage($otherModel): bool
    {
        // If has manage_all permission, can manage anyone
        if ($this->hasPermission('users.manage_all') || $this->hasPermission('admins.manage_all')) {
            return true;
        }
        
        // Must have position to check hierarchy
        if (!$this->position || !$otherModel->position) {
            return false;
        }
        
        // Can manage if other model's position is below this model's position
        return $this->position->isAbove($otherModel->position);
    }

    /**
     * Get all models (Admin/User) that this model can manage based on hierarchy
     */
    public function getManageableModels(string $modelType = 'user')
    {
        if (!$this->position) {
            return collect();
        }
        
        $subordinatePositions = $this->position->getSubordinatePositions();
        $positionIds = $subordinatePositions->pluck('id');
        
        if ($modelType === 'admin') {
            return \App\Models\Admin::whereIn('position_id', $positionIds)->get();
        }
        
        return \App\Models\User::whereIn('position_id', $positionIds)->get();
    }
}
