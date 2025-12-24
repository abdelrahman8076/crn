<?php
use Illuminate\Support\Facades\Auth;

if (! function_exists('isSales')) {
    function isSales(): bool
    {
        if (Auth::guard('admin')->check()) {
            return Auth::guard('admin')->user()->role?->name === 'Sales';
        }

        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->role?->name === 'Sales';
        }

        return false;
    }
}
