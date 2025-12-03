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
        $user = Auth::user();

        // If user is admin in 'admins' table -> full access
        if ($user?->guard === 'admin' || \App\Models\Admin::where('user_id', $user->id)->exists()) {
            return $query;
        }

        // For normal users: Sales and Managers
        $allowedUserIds = [$user->id];

        // If manager, include team members
        if ($user->role?->name === 'Manager' && method_exists($user, 'team')) {
            $teamIds = $user->team()->pluck('id')->toArray();
            $allowedUserIds = array_merge($allowedUserIds, $teamIds);
        }

        return $query->whereIn($userColumn, $allowedUserIds);
    }
}
