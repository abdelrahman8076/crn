<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Admin guard (Admin model) - Super admins have full access by default
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if (!$admin) {
                abort(403, 'You do not have permission to perform this action.');
            }
            
            // If admin has no role, give full access (legacy super admin behavior)
            if (!$admin->role_id || !$admin->role) {
                return $next($request); // Super admin has full access
            }
            
            // If admin has a role, check permissions
            if ($admin->hasPermission($permission)) {
                return $next($request);
            }
        }

        // Web guard (User model) - check if user has permission
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user && $user->role && $user->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to perform this action.');
    }
}
