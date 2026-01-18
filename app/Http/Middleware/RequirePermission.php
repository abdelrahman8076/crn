<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user();
        
        if (!$user) {
            abort(403, 'Unauthorized.');
        }
        
        if (!$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
}
