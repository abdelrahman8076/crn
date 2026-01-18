<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = [
        'name',
        'description',
        'parent_id',
        'level',
        'sort_order',
    ];

    /**
     * Parent position in hierarchy
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'parent_id');
    }

    /**
     * Child positions in hierarchy
     */
    public function children(): HasMany
    {
        return $this->hasMany(Position::class, 'parent_id')->orderBy('sort_order');
    }

    /**
     * Get all descendants (children, grandchildren, etc.)
     */
    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors (parent, grandparent, etc.)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $position = $this->parent;
        
        while ($position) {
            $ancestors->push($position);
            $position = $position->parent;
        }
        
        return $ancestors;
    }

    /**
     * Check if this position is above another position in hierarchy
     */
    public function isAbove(Position $position): bool
    {
        return $this->isAncestorOf($position);
    }

    /**
     * Check if this position is below another position in hierarchy
     */
    public function isBelow(Position $position): bool
    {
        return $position->isAncestorOf($this);
    }

    /**
     * Check if this position is an ancestor of another position
     */
    public function isAncestorOf(Position $position): bool
    {
        $ancestors = $position->ancestors();
        return $ancestors->contains('id', $this->id);
    }

    /**
     * Get all positions below this one (including self)
     */
    public function getSubordinatePositions()
    {
        $positions = collect([$this]);
        
        foreach ($this->children as $child) {
            $positions = $positions->merge($child->getSubordinatePositions());
        }
        
        return $positions;
    }

    /**
     * Admins with this position
     */
    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class);
    }

    /**
     * Users with this position
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
