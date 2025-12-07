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


    public function filterAccess(Builder $query, string $userColumn = 'assigned_to'): Builder
    {
        // Get current user from either admin or web guard
        $admin = Auth::guard('admin')->user();
        $user = $admin ?? Auth::guard('web')->user();

        // Full access for Admin guard or admin in admins table
        if ($admin ) {
            return $query;
        }

        // Base allowed list
        $allowedUserIds = [$user->id];

        // Manager → include team members
        if ($user->role?->name === 'Manager' && method_exists($user, 'team')) {
            $teamIds = $user->team()->pluck('id')->toArray();
            $allowedUserIds = array_merge($allowedUserIds, $teamIds);
        }

        return $query->whereIn($userColumn, $allowedUserIds);
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
