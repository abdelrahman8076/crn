<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'group',
        'description',
    ];

    /**
     * Roles that have this permission
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Models (Admin/User) that have this permission directly assigned
     */
    public function models(): MorphMany
    {
        return $this->morphMany(ModelPermission::class, 'model');
    }
}
