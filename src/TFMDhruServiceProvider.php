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
        $router = $this->app['router'];
        $router->aliasMiddleware('dhru.auth', \TFMSoftware\DhruFusion\Http\Middleware\DhruAuth::class);
        $this->publishes([
            __DIR__ . '/dhru/index.php' => 'public/api/index.php',
            __DIR__ . '/Http/Controllers/DhruController.php' => 'Dhru/Controllers/DhruController.php',
            __DIR__ . '/Models' => 'Dhru/Models',
        ], 'dhru-fusion');
    }
}

