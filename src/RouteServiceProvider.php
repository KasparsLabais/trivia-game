<?php

namespace PartyGames\TriviaGame;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
class RouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'PartyGames\TriviaGame\Http\Controllers';

    public function map()
    {
        Route::namespace($this->namespace)->group(__DIR__ . '/../routes/web.php');
    }

/*
    public function map() {
        Route::namespace('Partygames\TriviaGame\Http\Controllers')->group(__DIR__.'/../routes/web.php');
    }
    */
}