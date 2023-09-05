<?php

namespace TromsFylkestrafikk\RagnarokRuter;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use TromsFylkestrafikk\RagnarokRuter\Services\RuterAuthToken;
use TromsFylkestrafikk\RagnarokRuter\Services\RuterTransactions;

class RagnarokRuterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // $this->loadViewsFrom(__DIR__.'/resources/views', 'ragnarok_Ruter');
        // $this->registerRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/ragnarok_ruter.php', 'ragnarok_ruter');
        $this->app->singleton(RuterAuthToken::class, function () {
            return new RuterAuthToken(config('ragnarok_ruter'));
        });
        $this->app->singleton(RuterTransactions::class, function () {
            return new RuterTransactions(config('ragnarok_ruter'));
        });
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    protected function registerRoutes()
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
    protected function routeConfiguration()
    {
        return [
            'namespace'  => "TromsFylkestrafikk\RagnarokRuter\Http\Controllers",
            'middleware' => 'api',
            'prefix'     => 'api'
        ];
    }

    /**
     * Publish Config
     *
     * @return void
     */
    protected function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/ragnarok_ruter.php' => config_path('ragnarok_ruter.php'),
            ], 'config');
        }
    }
}
