<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireAnyPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user();
        
        if (!$user) {
            abort(403, 'Unauthorized.');
        }
        
        if (!$user->hasAnyPermission($permissions)) {
            abort(403, 'You do not have permission to access this resource.');
        }
        
        return $next($request);
    }
}
