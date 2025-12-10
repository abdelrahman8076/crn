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
    $user = $this->getLoggedUser();

    if (!$user) {
        return redirect('/login')->withErrors('Session expired, please login again.');
    }

    $isSuperAdmin = Admin::where('email', $user->email)->exists();

    // Base client query
    $clientQuery = Client::query();

    if (!$isSuperAdmin) {
        switch ($user->role?->name) {
            case 'Manager':
            //    $teamIds = $user->team?->pluck('id')->toArray() ?? [];
                $teamIds[] = $user->id;
                $clientQuery->whereIn('assigned_to_manager', $teamIds);
                break;

            case 'Sales':
                $clientQuery->where('assigned_to_sale', $user->id);
                break;

            default:
                $clientQuery->whereRaw('0 = 1');
        }
    }

    // Leads & Deals based on filtered clients
    $leadQuery = $isSuperAdmin 
        ? Lead::query() 
        : Lead::whereIn('client_id', $clientQuery->pluck('id')->toArray());

    $dealQuery = $isSuperAdmin 
        ? Deal::query() 
        : Deal::whereIn('lead_id', $leadQuery->pluck('id')->toArray());

     //   dd($leadQuery , $dealQuery);

    $data = [
        'totalUsers'   => (int) User::count(),
        'totalClients' => (int) $clientQuery->count(),
        'totalLeads'   => (int) $leadQuery->count(),
        'totalDeals'   => (int) $dealQuery->count(),
    ];

    return view('admin.dashboard', compact('data'));
}



    private function getLoggedUser()
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user();
        }

        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user();
        }

        return null;
    }
}
