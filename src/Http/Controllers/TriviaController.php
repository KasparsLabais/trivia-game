<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PartyGames\GameApi\GameApi;
use PartyGames\TriviaGame\Models\Trivia;

class TriviaController
{

    public function index()
    {
        $allTrivia = Trivia::all();
        return view('trivia-game::pages.index')->with(['allTrivia' => $allTrivia]);
    }

    public function startGame(Request $request)
    {
        $trivia = Trivia::find($request->get('trivia_id'));

        $response = GameApi::createGameInstance(config('settings.token'), $trivia->title);

        if ($response['status'] == false) {
            return new JsonResponse([
                'success' => false,
                'message' => $response['message'],
                'data' => NULL
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Game instance created successfully',
            'data' => $response['gameInstance']
        ]);
    }


    public function adminIndex()
    {
        $allTrivia = Trivia::all();
        return view('trivia-game::admin.trivia.index')->with(['allTrivia' => $allTrivia]);
    }

    public function create(Request $request)
    {
        $trivia = Trivia::create([
            'title' => $request->title,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'type' => $request->type,
        ]);

        if ($request->get('responseType') == 'json') {
            return new JsonResponse([
                'success' => true,
                'message' => 'Trivia created successfully',
                'data' => $trivia
            ]);
        }

        return redirect()->back();
    }

    public function show($id)
    {
        $trivia = Trivia::find($id);
        return new JsonResponse([
            'success' => true,
            'message' => 'Trivia fetched successfully',
            'data' => $trivia
        ]);
    }

}