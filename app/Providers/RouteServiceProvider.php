<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = null;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapPlayerRecordRoutes();
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapToolRoutes();
    }

    /**
     * Define the routes for all tools.
     *
     * Mostly same configuration as web routes
     * ! No ! Prefix so that it is possible to define routes with server/world
     *
     * @return void
     */
    protected function mapToolRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->name('tools.')
             ->group(base_path('routes/tools.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
             ->middleware('api')
             ->name('api.')
             ->namespace($this->namespace."\API")
             ->group(base_path('routes/api.php'));
    }

    /**
     * Define the routes for usage as player record ingame
     *
     * Mostly same configuration as web routes
     * ! No ! Prefix so that it is possible to define routes with server/world
     *
     * @return void
     */
    protected function mapPlayerRecordRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->prefix("in")
             ->name('playerRecord.')
             ->group(base_path('routes/playerRecord.php'));
    }
}
