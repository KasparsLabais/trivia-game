<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use PartyGames\GameApi\GameApi;

class Controller
{

    public function index()
    {
        return view('trivia-game::index');
    }

    public function register()
    {
        $params = [
            'version' => config('settings.version'),
            'start_point_url' => config('settings.start_point_url'),
            'description' => config('settings.description'),
            'creators_name' => config('settings.creators_name'),
            'creators_url' => config('settings.creators_url'),
        ];
        $response = GameApi::registerGame(config('settings.token'), $params);
        return view('trivia-game::pages.register')->with(['response' => $response]);
    }

    public function create()
    {
        //return view('trivia-game::create');
    }

}