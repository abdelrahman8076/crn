<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin_or_user' => \App\Http\Middleware\AllowAdminOrUser::class,
            'set_locale' => \App\Http\Middleware\SetLocale::class,
            'admin_only' => \App\Http\Middleware\AdminOnly::class,
            'admin_or_manager' => \App\Http\Middleware\AllowAdminOrManager::class,



        ]);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
