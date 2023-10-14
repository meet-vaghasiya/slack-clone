<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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

    public function boot()
    {
        DB::listen(function ($query) {
            // Check if the query is an INSERT or UPDATE affecting the channel_member table.
            if (strpos($query->sql, 'channel_member') !== false && (strpos($query->sql, 'insert into') !== false || strpos($query->sql, 'update') !== false)) {
                $query->bindings[] = now();
                $query->bindings[] = now();
            }
        });
    }
}
