<?php

namespace Spork\Planning;

use App\Spork;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class PlanningServiceProvider extends RouteServiceProvider
{
    public function boot()
    {
        Route::middleware($this->app->make('config')->get('spork.planning.middleware', ['auth:sanctum']))
            ->prefix('api/planning')
            ->group(__DIR__ . '/../routes/web.php');
    }

    public function register()
    {
        Spork::addFeature('planning', 'ViewBoardsIcon', '/planning');
    }
}
