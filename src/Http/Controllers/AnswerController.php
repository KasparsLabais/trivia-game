<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use PartyGames\TriviaGame\Models\Answers;
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
        $originalAnswer = Answers::find($answer->original_answer_id);

        //get uploaded file type
        $fileType = $request->file('answer-image')->getClientOriginalExtension();
        //dd($fileType);
        $imageFileTypes = ['jpg', 'jpeg', 'png', 'gif'];


        if (in_array($fileType, $imageFileTypes)) {
            $img = Image::make($request->file('answer-image'));
            $img->resize(700, null, function($constrains) {
                $constrains->aspectRatio();
            })->encode('jpg', 80);

            $imageName = Str::random(75) . '.jpg';
            Storage::put('answers/' . $imageName, $img);

            $path = '/answers/' . $imageName;
            $fileUrlType = 'image';

        } else {
            //upload video file
            $videoFileTypes = ['mp4', 'mov', 'avi', 'wmv', 'flv', 'webm'];

            if (in_array($fileType, $videoFileTypes)) {
                $videoName = Str::random(75) . '.' . $fileType;
                $video = Storage::put('answers', $request->file('answer-image'));

                $path = '/' . $video;//'/answers/' . $request->file('answer-image')->getClientOriginalName();
                $fileUrlType = 'video';
            } else {
                //upload audio file
                $audioFileTypes = ['mp3', 'wav', 'ogg', 'm4a'];
                if (in_array($fileType, $audioFileTypes)) {
                    $audioName = Str::random(75) . '.' . $fileType;
                    Storage::put('answers/' . $audioName, $request->file('answer-image'));

                    $path = '/answers/' . $audioName;
                    $fileUrlType = 'audio';
                } else {
                    //upload document file
                    $documentFileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
                    if (in_array($fileType, $documentFileTypes)) {
                        /*
                        $documentName = Str::random(75) . '.' . $fileType;
                        Storage::put('answers/' . $documentName, $request->file('answer-image'));

                        $path = '/answers/' . $documentName;
                        $fileUrlType = 'document';
                        */
                    } else {
                        /*
                        //upload other file
                        $otherName = Str::random(75) . '.' . $fileType;
                        Storage::put('answers/' . $otherName, $request->file('answer-image'));

                        $path = '/answers/' . $otherName;
                        $fileUrlType = 'other';
                        */
                    }
                }
            }
        }



        $answer->file_url = $path;
        $answer->file_url_type = $fileUrlType;
        $answer->save();

        $originalAnswer->file_url = $path;
        $originalAnswer->file_type = $fileUrlType;
        $originalAnswer->save();

        return redirect()->back();
    }


}