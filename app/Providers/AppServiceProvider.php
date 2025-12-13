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
}
}
