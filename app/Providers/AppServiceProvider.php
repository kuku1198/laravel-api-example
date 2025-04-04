<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        if (env('APP_DEBUG')) {
            DB::listen(function ($query) {
                Log::channel('database')->info(
                    "Query Time: {$query->time}ms | SQL: {$query->sql} | Bindings: " . implode(", ", $query->bindings)
                );
            });
        }
    }
}
