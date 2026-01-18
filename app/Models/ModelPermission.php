<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelPermission extends Model
{
    protected $fillable = [
        'model_id',
        'model_type',
        'permission_id',
        'granted',
    ];

    protected $casts = [
        'granted' => 'boolean',
    ];

    /**
     * Get the parent model (Admin or User)
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The permission assigned to this model
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }
}
