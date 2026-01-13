<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Target extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'target_total',
        'target_remaining',
        'period',
        'type',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'target_total' => 'integer',
        'target_remaining' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the target.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include active targets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Calculate progress percentage.
     */
    public function getProgressAttribute(): int
    {
        if ($this->target_total <= 0) return 0;
        
        $reached = $this->target_total - $this->target_remaining;
        return (int) (($reached / $this->target_total) * 100);
    }
}