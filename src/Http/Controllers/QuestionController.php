<?php

namespace Partygames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use PartyGames\TriviaGame\Models\Trivia;
use PartyGames\TriviaGame\Models\TrvQuestions;

class QuestionController
{

    public function index(Request $request)
    {
        $trivia = Trivia::where('id', $request->get('trivia_id'))->first();
        $questions = TrvQuestions::where('trivia_id', $request->get('trivia_id'))->get();
//dd($questions[0]->answers);
        return view('trivia-game::questions.index')->with(['trivia' => $trivia, 'questions' => $questions]);
    }

    public function create(Request $request)
    {
        $question = TrvQuestions::create([
            'question' => $request->get('question'),
            'trivia_id' => $request->get('trivia_id'),
        ]);

        if ($request->get('responseType') == 'json') {
            return new JsonResponse([
                'success' => true,
                'message' => 'Question created successfully',
                'data' => $question
            ]);
        }

        return redirect()->back();
    }

    //create function to get single question in json format
    public function show($id)
    {
        $question = TrvQuestions::find($id);
        return new JsonResponse([
            'success' => true,
            'message' => 'Question retrieved successfully',
            'data' => $question
        ]);
    }

}