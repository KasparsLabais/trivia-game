<?php

namespace Partygames\TriviaGame;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
class RouteServiceProvider extends ServiceProvider
{

    public function map() {
        Route::middleware('web')
                    ->namespace('Partygames\TriviaGame\Controllers')
                    ->group(__DIR__.'/routes/web.php');
    }

}