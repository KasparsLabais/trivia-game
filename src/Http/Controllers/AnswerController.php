<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use PartyGames\TriviaGame\Models\TrvAnswers;
class AnswerController
{

    public function index()
    {
        return view('trivia-game::index');
    }

    public function create(Request $request)
    {
        $answer = TrvAnswers::create([
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
        $answer = TrvAnswers::find($id);
        return new JsonResponse([
            'success' => true,
            'message' => 'Answer retrieved successfully',
            'data' => $answer
        ]);
    }

    public function submitAnswerImage($id, Request $request)
    {
        $answer = TrvAnswers::find($id);

        $img = Image::make($request->file('answer-image'));
        $img->resize(700, null, function($constrains) {
            $constrains->aspectRatio();
        })->encode('jpg', 80);

        $imageName = Str::random(75) . '.jpg';
        Storage::put('answers/' . $imageName, $img);

        $path = '/answers/' . $imageName; //'/' . $request->file('avatar')->store('avatars');
        ///| file_url      | varchar(255)    | YES  |     | NULL    |                |
        //| file_url_type | varchar(255)    | YES  |     | NULL    |                |

        $answer->file_url = $path;
        $answer->file_url_type = 'image';
        $answer->save();

        return redirect()->back();
    }


}