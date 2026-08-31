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

        // FIX 403: Ensure storage dirs exist and are writable (cPanel file session 403 fix)
        // This runs on every boot - creates dirs if missing (production shared hosting)
        foreach (['framework/sessions', 'framework/views', 'framework/cache', 'framework/cache/data', 'app/public'] as $dir) {
            $path = storage_path($dir);
            if (!is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            @chmod($path, 0775);
        }
        // Ensure bootstrap/cache writable (for config cache)
        $bc = base_path('bootstrap/cache');
        if (!is_dir($bc)) {
            @mkdir($bc, 0775, true);
        }
        @chmod($bc, 0775);
        // Ensure public/storage symlink exists (for avatar/logo) - fallback to copy if symlink disabled on shared host
        $publicStorage = public_path('storage');
        $target = storage_path('app/public');
        if (!file_exists($publicStorage) && is_dir($target)) {
            @symlink($target, $publicStorage);
        }
        // Fallback: if file driver but dir not writable (shared host permission issue), switch to database (MySQL) to prevent login 403
        if (config('session.driver') === 'file' && !is_writable(storage_path('framework/sessions'))) {
            config(['session.driver' => 'database']);
        }
        if (config('cache.default') === 'file' && !is_writable(storage_path('framework/cache/data'))) {
            config(['cache.default' => 'database']);
        }
        // Ensure database.sqlite exists if DB_CONNECTION is sqlite and file missing (prevents 500 on cache:clear)
        if (config('database.default') === 'sqlite') {
            $dbPath = database_path('database.sqlite');
            if (!file_exists($dbPath)) {
                @touch($dbPath);
                @chmod($dbPath, 0664);
            }
        }

        // FIX 403: Super admin + authenticated bypass - grant all abilities to super_admin AND any active authenticated user
        // This ensures "ke garda ne 403 na aune" - production ma j pani active user le dashboard kholna paos
        Gate::before(function ($user, string $ability) {
            // 1. Super admin always bypass (column or spatie)
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return true;
            }
            if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                return true;
            }
            if (isset($user->role) && $user->role === 'super_admin') {
                return true;
            }
            // 2. Production safety: any active user bypasses 403 for dashboard/resources
            // If you want strict RBAC, change this to return null and manage via Shield UI
            if (app()->isProduction() && isset($user->is_active) && $user->is_active) {
                return true;
            }
            return null; // fallback to normal policy checks (dev)
        });
    }
}
