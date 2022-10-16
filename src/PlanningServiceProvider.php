<?php

namespace Spork\Planning;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;
use Spork\Core\Spork;

class PlanningServiceProvider extends RouteServiceProvider
{
    public function boot()
    {
    }

    public function register()
    {
        Spork::addFeature('planning', 'ViewBoardsIcon', '/planning/kanban', 'tool');
        if (config('spork.planning.enabled')) {
            Route::middleware($this->app->make('config')->get('spork.planning.middleware', ['auth:sanctum']))
                ->prefix('api/planning')
                ->group(__DIR__.'/../routes/web.php');
        }
    }
}
