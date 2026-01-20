<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        // Allow Admin guard (Admin model) - full access
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // Allow User guard (User model) if they have admin access permission
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            
            // Check if user has admin access permission
            if ($user && $user->hasPermission('access-admin')) {
                return $next($request);
            }
        }

        abort(403, 'Access denied. Admin access required.');
    }
}
