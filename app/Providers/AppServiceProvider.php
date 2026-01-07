<?php

namespace App\Providers;

use App\Enums\UserRole;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        // Force HTTPS only when explicitly enabled (or in production).
        $forceHttps = filter_var(
            env('FORCE_HTTPS', app()->environment('production')),
            FILTER_VALIDATE_BOOL
        );

        if ($forceHttps) {
            URL::forceScheme('https');
        }

        Gate::define('view-admin-roles', function ($user) {
            if (!$user) return false;
            
            $role = is_object($user->role) && enum_exists(get_class($user->role)) 
                ? $user->role->value 
                : $user->role;

            return in_array($role, ['super_admin', 'admin']);
        });

        // Alias for controller authorization
        Gate::define('manage-roles', function ($user) {
            // Because we cast `role` to Enum in User model, accessing $user->role returns the Enum instance.
            // We use the same loose check logic as view-admin-roles.
            $roleVal = $user->role instanceof \UnitEnum ? $user->role->value : $user->role;
            return in_array($roleVal, ['super_admin']);
        });
    }
}
