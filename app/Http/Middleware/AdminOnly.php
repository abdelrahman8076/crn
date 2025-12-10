<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Allow only authenticated admins
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
