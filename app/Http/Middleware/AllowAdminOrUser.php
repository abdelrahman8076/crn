<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AllowAdminOrUser
{
    public function handle($request, Closure $next)
    {

        // If admin is logged in → allow
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // If normal user is logged in → allow
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        // Otherwise redirect to login
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
