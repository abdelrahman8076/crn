<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    /**
     * Show Login Page
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Handle Login
     * Allows both Admin model and User model (with admin access permission) to login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // First, try to authenticate as Admin (Admin model)
        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            session(['user_type' => 'admin']);
            return redirect()->route('admin.dashboard');
        }

        // If Admin authentication fails, try User model (if user has admin access permission)
        if (Auth::guard('web')->attempt($credentials, $request->remember)) {
            $user = Auth::guard('web')->user();
            
            // Check if user has admin access permission or is Manager/Sales role
            if ($user && (
                $user->hasPermission('access-admin') || 
                in_array($user->role?->name, ['Manager', 'Sales'])
            )) {
                // Store that this user is accessing admin panel
                session(['user_type' => 'user_admin']);
                session(['admin_user_id' => $user->id]);
                return redirect()->route('admin.dashboard');
            } else {
                // User doesn't have permission, logout and show error
                Auth::guard('web')->logout();
                return back()->withErrors([
                    'email' => __('You do not have permission to access the admin panel. Please contact administrator.')
                ]);
            }
        }

        return back()->withErrors(['email' => __('Invalid credentials')]);
    }

    /**
     * Show Register Page
     */
    public function showRegisterForm()
    {
        return view('admin.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:admins,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $admin = Admin::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('admin')->login($admin);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout Admin
     */
    public function logout()
    {
        // Check which guard is active
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        
        // Clear admin session data
        session()->forget(['user_type', 'admin_user_id']);
        
        return redirect()->route('admin.login');
    }
}
