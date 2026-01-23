<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AllowAdminOrUser
{
    public function handle($request, Closure $next)
    {
        // If admin guard is logged in → allow
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // If web guard user is logged in and has admin access permission → allow
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Check if user has admin access permission or is Manager/Sales role
            if ($user && (
                $user->hasPermission('access-admin') || 
                in_array($user->role?->name, ['Manager', 'Sales'])
            )) {
                return $next($request);
            }
        }

        // Otherwise redirect to admin login
        return redirect()->route('admin.login')->with('error', 'Unauthorized access. Please login to continue.');
    }
}
