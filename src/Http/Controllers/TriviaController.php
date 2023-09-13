<?php

namespace Partygames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Partygames\TriviaGame\Models\Trivia;

class TriviaController
{

    public function index()
    {
        return view('trivia-game::trivia.index');
    }

    public function create(Request $request)
    {
        $trivia = Trivia::create([
            'title' => $request->title,
            'category' => $request->category,
            'difficulty' => $request->difficulty,
            'type' => $request->type,
        ]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Trivia created successfully',
            'data' => $trivia
        ]);
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