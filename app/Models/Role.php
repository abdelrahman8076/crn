<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Users with this role (legacy relationship)
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Permissions assigned to this role
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Models (Admin/User) that have this role assigned
     */
    public function models(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_roles');
    }

    /**
     * Assign a permission to this role
     */
    public function givePermission($permission): void
    {
        $permissionId = is_string($permission) 
            ? Permission::where('name', $permission)->firstOrFail()->id 
            : $permission;
        
        $this->permissions()->syncWithoutDetaching([$permissionId]);
    }

    /**
     * Remove a permission from this role
     */
    public function revokePermission($permission): void
    {
        $permissionId = is_string($permission) 
            ? Permission::where('name', $permission)->firstOrFail()->id 
            : $permission;
        
        $this->permissions()->detach($permissionId);
    }

    /**
     * Sync permissions (replace all permissions)
     */
    public function syncPermissions(array $permissionIds): void
    {
        $this->permissions()->sync($permissionIds);
    }
}
