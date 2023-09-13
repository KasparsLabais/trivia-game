<?php

namespace Partygames\TriviaGame\Http\Controllers;

use Partygames\TriviaGame\Models\Questions;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QuestionController
{

    public function index()
    {
        return view('trivia-game::index');
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