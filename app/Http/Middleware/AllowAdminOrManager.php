<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AllowAdminOrManager
{
    public function handle($request, Closure $next)
    {
        // ✅ Admin guard → always allowed
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // ✅ Web guard → ONLY Manager role
        if (
            Auth::guard('web')->check() &&
            Auth::guard('web')->user()?->role?->name === 'Manager'
        ) {
            return $next($request);
        }

        // ❌ Otherwise deny
        abort(403, 'Unauthorized access.');
    }
}
