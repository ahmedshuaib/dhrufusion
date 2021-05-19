<?php

namespace TFMSoftware\DhruFusion;

use Illuminate\Support\ServiceProvider;

class TFMDhruServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__ . '/Http/Controllers' => 'Dhru/Http/Controllers',
            __DIR__ . '/dhru/index.php' => 'public/api/dhru/index.php',
            __DIR__ . '/Models' => 'Dhru/Models',
            __DIR__ . '/routes' => 'Dhru/routes',
        ]);
    }
}

