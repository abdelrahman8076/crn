<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Deal;
use App\Models\Admin;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user is in 'admins' table (full access)
        $isSuperAdmin = Admin::where('email', $user->email)->exists();

        // Base queries
        $clientQuery = Client::query();
        $leadQuery   = Lead::query();
        $dealQuery   = Deal::query()->whereHas('lead', function($q) {});

        if (!$isSuperAdmin) {
            // Role-based filtering
            switch ($user->role?->name) {
                case 'Manager':
                    $teamIds = $user->team->pluck('id')->toArray(); // assuming User model has team() relation
                    $teamIds[] = $user->id;

                    $clientQuery->whereIn('assigned_to', $teamIds);
                    $leadQuery->whereIn('assigned_to', $teamIds);
                    $dealQuery->whereHas('lead', fn($q) => $q->whereIn('assigned_to', $teamIds));
                    break;

                case 'Sales':
                    $clientQuery->where('assigned_to', $user->id);
                    $leadQuery->where('assigned_to', $user->id);
                    $dealQuery->whereHas('lead', fn($q) => $q->where('assigned_to', $user->id));
                    break;

                default:
                    // Other users see nothing
                    $clientQuery->whereRaw('0 = 1');
                    $leadQuery->whereRaw('0 = 1');
                    $dealQuery->whereRaw('0 = 1');
            }
        }

        $data = [
            'totalUsers'   => User::count(),
            'totalClients' => $clientQuery->count(),
            'totalLeads'   => $leadQuery->count(),
            'totalDeals'   => $dealQuery->count(),
        ];

        return view('admin.dashboard', compact('data'));
    }
}
