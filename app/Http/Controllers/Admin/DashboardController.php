<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Deal;
use Illuminate\Database\Eloquent\Builder;

class DashboardController extends Controller
{
    use \App\Traits\ChecksPermissions;

    public function index()
    {
        $this->requirePermission('view-dashboard');
        
        $user = $this->getLoggedUser();
        if (!$user) {
            return redirect('/login')->withErrors('Session expired, please login again.');
        }

        $isSuperAdmin = Auth::guard('admin')->check();
        $roleName = $user->role?->name;

        // --- 1. Define the User Scope ---
        $targetUserIds = [];
        if ($isSuperAdmin) {
            $targetUserIds = User::pluck('id')->toArray();
        } elseif ($roleName === 'Manager') {
            $teamIds = $user->salesTeam()->pluck('id')->toArray() ?? [];
            $targetUserIds = array_merge([$user->id], $teamIds);
        } elseif ($roleName === 'Sales') {
            $targetUserIds = [$user->id];
        }

        // --- 2. Calculate Real Numbers using Relationship Joins ---
        // This ensures "Total Deals" reflects reality based on the Lead -> Client path
        if ($isSuperAdmin) {
            $totalClients = Client::count();
            $totalLeads   = Lead::count();
            $totalDeals   = Deal::count();
        } else {
            // Scope Clients
            $totalClients = Client::where(function (Builder $query) use ($roleName, $user, $targetUserIds) {
                if ($roleName === 'Manager') {
                    $query->whereIn('assigned_to_manager', $targetUserIds);
                } else {
                    $query->where('assigned_to_sale', $user->id);
                }
            })->count();

            // Scope Leads based on Client assignment
            $totalLeads = Lead::whereHas('client', function (Builder $query) use ($roleName, $user, $targetUserIds) {
                if ($roleName === 'Manager') {
                    $query->whereIn('assigned_to_manager', $targetUserIds);
                } else {
                    $query->where('assigned_to_sale', $user->id);
                }
            })->count();

            // Scope Deals based on Lead -> Client assignment
            $totalDeals = Deal::whereHas('client', function (Builder $query) use ($roleName, $user, $targetUserIds) {
                if ($roleName === 'Manager') {
                    $query->whereIn('assigned_to_manager', $targetUserIds);
                } else {
                    $query->where('assigned_to_sale', $user->id);
                }
            })->count();
        }

        // --- 3. Targets Data for Tables ---
        $allTargetsData = User::whereIn('id', $targetUserIds)
            ->with(['targets' => fn($q) => $q->where('is_active', true), 'role'])
            ->get()
            ->map(fn($u) => $this->formatUserData($u));

        // --- 4. Super Admin Hierarchy View ---
        $managersData = [];
        if ($isSuperAdmin) {
            $managersData = User::whereHas('role', fn($q) => $q->where('name', 'Manager'))
                ->with([
                    'targets' => fn($q) => $q->where('is_active', true),
                    'salesTeam' => fn($q) => $q->with([
                        'targets' => fn($q) => $q->where('is_active', true),
                        'role'
                    ])
                ])
                ->get()
                ->map(fn($m) => $this->formatManagerWithTeam($m))
                ->toArray();
        }

        // --- 5. Manager-Specific Team View ---
        $managerViewData = null;
        if (!$isSuperAdmin && $roleName === 'Manager') {
            $managerModel = User::with([
                'targets' => fn($q) => $q->where('is_active', true),
                'salesTeam' => fn($q) => $q->with([
                    'targets' => fn($q) => $q->where('is_active', true),
                    'role'
                ])
            ])->find($user->id);

            if ($managerModel) {
                $managerViewData = $this->formatManagerWithTeam($managerModel);
            }
        }
        $myTarget = $allTargetsData->where('id', $user->id)->first();

        $data = [
            'totalUsers'   => (int) User::count(),
            'totalClients' => (int) $totalClients,
            'totalLeads'   => (int) $totalLeads,
            'totalDeals'   => (int) $totalDeals,
            'allTargets'   => $allTargetsData,
            'myTarget'     => $myTarget, // Pass it explicitly here
        ];

        return view('admin.dashboard', compact('data', 'isSuperAdmin', 'managersData', 'managerViewData'));
    }

   private function formatManagerWithTeam($manager)
{
    $active = $manager->targets->first();
    $reached = $active ? ($active->target_total - $active->target_remaining) : 0;
    
    return [
        'id'           => $manager->id,
        'manager_name' => $manager->name,
        'role'         => $manager->role?->name ?? 'Manager', // Added for table consistency
        'target_total' => $active->target_total ?? 0,
        'reached'      => max(0, $reached),
        'progress'     => $active->progress ?? 0,
        'remaining'    => $active->target_remaining ?? 0,
        // --- NEW SUBTARGET FIELDS ---
        'subtarget_total'     => $active->subtarget_total ?? 0,
        'subtarget_remaining' => $active->subtarget_remaining ?? 0,
        // ----------------------------
        'period'       => $active->period ?? 'N/A',
        'team'         => $manager->salesTeam->map(fn($s) => $this->formatUserData($s))->toArray(),
    ];
}

private function formatUserData($u)
{
    $active = $u->targets->first();
    // Use target_total - target_remaining for reached amount
    $reached = $active ? ($active->target_total - $active->target_remaining) : 0;

    return [
        'id'           => $u->id,
        'user_name'    => $u->name,
        'role'         => $u->role?->name ?? 'N/A',
        'target_total' => (float)($active->target_total ?? 0),
        'reached'      => (float)max(0, $reached),
        'progress'     => (float)($active->progress ?? 0),
        'remaining'    => (float)($active->target_remaining ?? 0),
        'subtarget_total' => (float)($active->subtarget_total ?? 0), // Needed for Manager Chart
        'period'       => $active->period ?? 'N/A',
    ];

}

    private function getLoggedUser()
    {
        return Auth::guard('admin')->user() ?? Auth::guard('web')->user();
    }
}