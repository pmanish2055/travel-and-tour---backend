<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
    }
}
