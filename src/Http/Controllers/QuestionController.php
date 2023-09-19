<?php

namespace Partygames\TriviaGame\Http\Controllers;

use Partygames\TriviaGame\Models\Questions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use PartyGames\TriviaGame\Models\Trivia;

class QuestionController
{

    public function index(Request $request)
    {
        $trivia = Trivia::where('id', $request->get('trivia_id'))->first();
        $questions = Questions::where('trivia_id', $request->get('trivia_id'))->get();

        return view('trivia-game::questions.index')->with(['trivia' => $trivia, 'questions' => $questions]);
    }

    public function create(Request $request)
    {
        $question = Questions::create([
            'question' => $request->get('question'),
            'trivia_id' => $request->get('trivia_id'),
        ]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Question created successfully',
            'data' => $question
        ]);
    }

    //create function to get single question in json format
    public function show($id)
    {
        $question = Questions::find($id);
        return new JsonResponse([
            'success' => true,
            'message' => 'Question retrieved successfully',
            'data' => $question
        ]);
    }

}