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

        // ✅ Web guard → Manager, Sales role OR user with admin access permission
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            if ($user && (
                in_array($user->role?->name, ['Manager', 'Sales']) ||
                $user->hasPermission('access-admin')
            )) {
                return $next($request);
            }
        }

        // ❌ Otherwise deny
        abort(403, 'Unauthorized access.');
    }
}
