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
            __DIR__ . '/Http/Controllers/ApiKeyController.php' => 'Dhru/Http/Controllers/ApiKeyController.php',
            __DIR__ . '/dhru/index.php' => 'public/api/dhru/index.php',
            __DIR__ . '/routes/web.php' => 'Dhru/routes/web.php',
        ]);
    }
}

