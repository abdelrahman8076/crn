<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();
        
        if (!$user) {
            return redirect('/login')->withErrors('Authentication failed.');
        }
        
        // Check if user has access-user-side permission
        $hasUserSideAccess = $user->hasPermission('access-user-side');
        $hasAdminAccess = $user->hasPermission('access-admin') || 
                         in_array($user->role?->name, ['Manager', 'Sales', 'Admin']);
        
        // If user has user-side access, allow them to access regular dashboard
        if ($hasUserSideAccess) {
            return redirect()->route('dashboard');
        }
        
        // If user only has admin access (no user-side), redirect to admin dashboard
        if ($hasAdminAccess) {
            session(['user_type' => 'user_admin']);
            return redirect('admin/dashboard');
        }

        // No permissions - logout and show error
        Auth::guard('web')->logout();
        return redirect('/login')->withErrors('You do not have permission to access this system. Please contact administrator.');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
