<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
   public function boot()
{
    // Check if user has a role
    Blade::directive('role', function ($role) {
        return "<?php if(auth()->check() && auth()->user()->role?->name == {$role}): ?>";
    });

    // Check if user does NOT have a role
    Blade::directive('notrole', function ($role) {
        return "<?php if(auth()->check() && auth()->user()->role?->name != {$role}): ?>";
    });

    Blade::directive('endrole', function () {
        return "<?php endif; ?>";
    });

    // Check if user has a permission (works for both admin and web guards)
    Blade::directive('permission', function ($permission) {
        return "<?php 
            \$hasPermission = false;
            if (auth()->guard('admin')->check()) {
                \$admin = auth()->guard('admin')->user();
                \$hasPermission = !\$admin->role_id || !\$admin->role ? true : \$admin->hasPermission({$permission});
            } elseif (auth()->guard('web')->check()) {
                \$user = auth()->guard('web')->user();
                \$hasPermission = \$user && \$user->role && \$user->hasPermission({$permission});
            }
            if (\$hasPermission): ?>";
    });

    // Check if user does NOT have a permission
    Blade::directive('notpermission', function ($permission) {
        return "<?php 
            \$hasPermission = false;
            if (auth()->guard('admin')->check()) {
                \$admin = auth()->guard('admin')->user();
                \$hasPermission = !\$admin->role_id || !\$admin->role ? true : \$admin->hasPermission({$permission});
            } elseif (auth()->guard('web')->check()) {
                \$user = auth()->guard('web')->user();
                \$hasPermission = \$user && \$user->role && \$user->hasPermission({$permission});
            }
            if (!\$hasPermission): ?>";
    });

    Blade::directive('endpermission', function () {
        return "<?php endif; ?>";
    });
}
}
