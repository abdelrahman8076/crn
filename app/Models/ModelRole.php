<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelRole extends Model
{
    protected $fillable = [
        'model_id',
        'model_type',
        'role_id',
    ];

    /**
     * Get the parent model (Admin or User)
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The role assigned to this model
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
