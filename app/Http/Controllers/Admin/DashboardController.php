<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Deal;

class DashboardController extends Controller
{
    public function index()
    {
        $user = $this->getLoggedUser();
        if (!$user) {
            return redirect('/login')->withErrors('Session expired, please login again.');
        }

        $isSuperAdmin = Auth::guard('admin')->check();
        
        // --- 1. Fix Scoping Logic (Stay in Eloquent) ---
        $clientQuery = Client::query();
        $targetUserIds = [];

        if (!$isSuperAdmin) {
            // Load role to prevent property-on-null errors
            $roleName = $user->role?->name;

            if ($roleName === 'Manager') {
                // Get manager's sales team ids safely
                $teamIds = $user->salesTeam()->pluck('id')->toArray() ?? [];
                // include manager themself
                $teamIds[] = $user->id;
                $clientQuery->whereIn('assigned_to_manager', $teamIds);
                $targetUserIds = $teamIds;
            } elseif ($roleName === 'Sales') {
                $clientQuery->where('assigned_to_sale', $user->id);
                $targetUserIds = [$user->id];
            } else {
                $clientQuery->whereRaw('1 = 0'); // No access
            }
        } else {
            $targetUserIds = User::pluck('id')->toArray();
        }

        // --- 2. Lead and Deal Queries (Avoid mixing Query Builder) ---
        $clientIds = $clientQuery->pluck('id')->toArray();
        $leadQuery = Lead::whereIn('client_id', $clientIds);
        
        $leadIds = $leadQuery->pluck('id')->toArray();
        $dealQuery = Deal::whereIn('lead_id', $leadIds);

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
        if (!$isSuperAdmin && optional($user->role)->name === 'Manager') {
            // Re-fetch with relationships to avoid "Builder::with" errors on existing instances
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

        $data = [
            'totalUsers'   => (int) User::count(),
            'totalClients' => (int) count($clientIds),
            'totalLeads'   => (int) count($leadIds),
            'totalDeals'   => (int) $dealQuery->count(),
            'allTargets'   => $allTargetsData,
        ];

        return view('admin.dashboard', compact('data', 'isSuperAdmin', 'managersData', 'managerViewData'));
    }

    /**
     * Helper: Formats a Manager and their entire Team
     */
    private function formatManagerWithTeam($manager)
    {
        $active = $manager->targets->first();
        return [
            'id'           => $manager->id,
            'manager_name' => $manager->name,
            'target_total' => $active->target_total ?? 0,
            'reached'      => $active ? ($active->target_total - $active->target_remaining) : 0,
            'progress'     => $active->progress ?? 0,
            'remaining'    => $active->target_remaining ?? 0,
            'period'       => $active->period ?? 'N/A',
            'team'         => $manager->salesTeam->map(fn($s) => $this->formatUserData($s))->toArray(),
        ];
    }

    /**
     * Helper: Formats a single user (Sales or Manager) consistently
     */
    private function formatUserData($u)
    {
        $active = $u->targets->first();
        return [
            'id'           => $u->id,
            'user_name'    => $u->name,
            'role'         => $u->role?->name ?? 'N/A',
            'target_total' => $active->target_total ?? 0,
            'reached'      => $active ? ($active->target_total - $active->target_remaining) : 0,
            'progress'     => $active->progress ?? 0,
            'remaining'    => $active->target_remaining ?? 0,
            'period'       => $active->period ?? 'N/A',
        ];
    }

    private function getLoggedUser()
    {
        return Auth::guard('admin')->user() ?? Auth::guard('web')->user();
    }
}