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


    public function filterAccess(Builder $query, string $modelType = 'client'): Builder
    {
        $admin = Auth::guard('admin')->user();
        $user = $admin ?? Auth::guard('web')->user();

        if ($admin) {
            return $query; // Admin sees all
        }

        if (!$user) {
            return $query->whereRaw('0 = 1'); // No access
        }

        // Determine allowed client IDs
        $allowedClientIds = [];
        if ($user->role?->name === 'Manager') {
            $allowedClientIds = [$user->id];
            if (property_exists($user, 'team')) {
                $allowedClientIds = array_merge($allowedClientIds, $user->team?->pluck('id')->toArray() ?? []);
            }
        } elseif ($user->role?->name === 'Sales') {
            $allowedClientIds = [$user->id];
        } else {
            return $query->whereRaw('0 = 1');
        }

        // Apply hierarchical filtering
        switch ($modelType) {
            case 'client':
                if ($user->role?->name === 'Manager') {
                    return $query->whereIn('assigned_to_manager', $allowedClientIds);
                }
                return $query->where('assigned_to_sale', $user->id);

            case 'lead':
                return $query->whereHas('client', fn($q) => $this->filterAccess($q, 'client'));

            case 'deal':
                return $query->whereHas('lead.client', fn($q) => $this->filterAccess($q, 'client'));
            case 'task':
                return $query->where('assigned_to', $user->id);



            default:
                return $query->whereRaw('0 = 1');
        }
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
    private function getAccessibleClients($model = \App\Models\Client::class)
    {
        // Start query on given model
        $query = $model::query();

        // Apply correct role-based access
        return $this->filterAccess($query)->get();
    }


}
