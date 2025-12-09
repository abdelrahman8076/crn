<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasAccessFilter
{
    /**
     * Filter a query based on the logged-in user's access.
     *
     * @param  Builder  $query
     * @param  string  $userColumn  Column that stores assigned user ID, default 'assigned_to'
     * @return Builder
     */


public function filterAccess(Builder $query): Builder
{
    // Get current user from either guard
    $admin = Auth::guard('admin')->user();
    $user = $admin ?? Auth::guard('web')->user();

    // Admin → full access
    if ($admin) {
        return $query;
    }

    if (!$user) {
        // No user → return empty query
        return $query->whereRaw('0 = 1');
    }

    // Determine column to filter by
    if ($user->role?->name === 'Manager') {
        $userColumn = 'assigned_to_manager';
    } elseif ($user->role?->name === 'Sales') {
        $userColumn = 'assigned_to_user';
    } else {
        // Other roles → no access
        return $query->whereRaw('0 = 1');
    }

    return $query->where($userColumn, $user->id);
}

    public function abortIfNoAccess()
    {
        $admin = Auth::guard('admin')->user();
        $user = $admin ?? Auth::guard('web')->user();

        // Admin guard → allowed
        if ($admin) {
            return;
        }

        // Web users with valid roles → allowed
        if ($user && in_array($user->role?->name, ['Manager', 'Sales', 'Admin'])) {
            return;
        }

        abort(403, 'Unauthorized access.');
    }

    protected function canAccess($model, string $ownerColumn = 'assigned_to'): bool
    {
        // Admin has full access
        if (Auth::guard('admin')->check()) {
            return true;
        }

        // Run filtered query to check if record belongs to allowed IDs
        $query = $model::query()->where('id', $model->id);

        if (method_exists($this, 'filterAccess')) {
            $query = $this->filterAccess($query, $ownerColumn);
        }

        return $query->exists();
    }

}
