<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Prevent lazy loading in dev to catch N+1 early; log slow queries >500ms
        Model::preventLazyLoading(!app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());

        if (!app()->isProduction()) {
            DB::whenQueryingForLongerThan(500, function ($connection, $query) {
                logger()->warning('Slow query', ['sql' => $query->sql, 'time' => $query->time]);
            });
        }

        // FIX 403: Super admin bypass - grant all abilities to super_admin regardless of Shield permissions
        // This checks both spatie role AND users.role column (for cPanel fresh seed where permissions may not be generated yet)
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
            if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                return true;
            }
            // Also check raw column value (covers case where spatie role not yet assigned but column is super_admin)
            if (isset($user->role) && $user->role === 'super_admin') {
                return true;
            }
            return null; // fallback to normal policy checks
        });
    }
}
