<?php

namespace TromsFylkestrafikk\RagnarokRuter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class RagnarokRuterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ragnarok_ruter.php', 'ragnarok_ruter');

        $this->publishConfig();

        // $this->loadViewsFrom(__DIR__.'/resources/views', 'ragnarok_Ruter');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->registerRoutes();
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
    * Get route group configuration array.
    *
    * @return array
    */
    private function routeConfiguration()
    {
        return [
            'namespace'  => "TromsFylkestrafikk\RagnarokRuter\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ragnarok_ruter.php' => config_path('ragnarok_ruter.php'),
            ], 'config');
        }
    }
}