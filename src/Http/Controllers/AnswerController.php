<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use PartyGames\TriviaGame\Models\Answers;
class AnswerController
{

    public function index()
    {
        return view('trivia-game::index');
    }

    public function create(Request $request)
    {
        $answer = Answers::create([
            'answer' => $request->get('answer'),
            'question_id' => $request->get('question_id'),
            'is_correct' => $request->get('is_correct'),
        ]);

        if ($request->get('responseType') == 'json') {
            return new JsonResponse([
                'success' => true,
                'message' => 'Answer created successfully',
                'data' => $answer
            ]);
        }

        return redirect()->back();
    }

    public function show($id) {
        $answer = Answers::find($id);
        return new JsonResponse([
            'success' => true,
            'message' => 'Answer retrieved successfully',
            'data' => $answer
        ]);
    }


}