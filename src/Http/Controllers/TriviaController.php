<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Models\Game;
use PartyGames\TriviaGame\Models\Questions;
use PartyGames\TriviaGame\Models\SubmittedAnswers;
use PartyGames\TriviaGame\Models\Trivia;
use PartyGames\TriviaGame\Models\Answers;

use Illuminate\Support\Facades\Auth;

class TriviaController
{

    public function index()
    {
        $allTrivia = Trivia::all();
        return view('trivia-game::pages.index')->with(['allTrivia' => $allTrivia]);
    }

    public function createGame(Request $request)
    {
        $trivia = Trivia::find($request->get('trivia_id'));
        $remoteData = [
            'trivia_id' => $trivia->id,
        ];

        $response = GameApi::createGameInstance(config('settings.token'), $trivia->title, $remoteData);

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

    public function game($gameToken)
    {
        $response = GameApi::getGameInstance($gameToken);
        $playerInstance = GameApi::getPlayerInstance($response['gameInstance']['id'], Auth::user()->id);
        //if game instance is not found redirect to /trv/trivia
        if ($response['status'] == false) {
            return redirect()->route('trv.trivia');
        }

        $gameInstance = $response['gameInstance'];

        if ($gameInstance['status'] == 'created') {
            return view('trivia-game::game.start')->with(['gameInstance' => $gameInstance]);
        }

        if ($gameInstance['status'] == 'ended') {
            return view('trivia-game::game.end')->with(['gameInstance' => $gameInstance]);
        }

        $remoteData = json_decode($response['gameInstance']['remote_data'], true);
        return view('trivia-game::game.play')->with(['gameInstance' => $gameInstance, 'remoteData' => $remoteData, 'playerInstance' => $playerInstance['playerInstance']]);
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

    public function startGame(Request $request)
    {
        $gameToken = $request->get('gameToken');
        $gameInstance = GameApi::getGameInstance($gameToken);

        $remoteData = json_decode($gameInstance['gameInstance']['remote_data'], true);
        $remoteData['current_question'] = 1;

        GameApi::updateGameInstanceRemoteData($gameToken, $remoteData);
        $response = GameApi::changeGameInstanceStatus($gameToken, 'started');

        return new JsonResponse([
            'success' => true,
            'message' => 'Game started successfully',
            'data' => $response
        ]);
    }

    public function getQuestion($token)
    {
        //$triviaId = $request->get('triviaId');
        //$currentQuestion = $request->get('currentQuestion');
        $data = GameApi::getGameInstance($token);
        $playerInstance = GameApi::getPlayerInstance($data['gameInstance']['id'], Auth::user()->id);

        $remoteData = json_decode($data['gameInstance']['remote_data'], true);

        $question = Questions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
        $question->load('answers');

        if (isset($playerInstance['playerInstance']['remote_data'])) {
            $playerRemoteData = json_decode($playerInstance['playerInstance']['remote_data'], true);
            if (array_key_exists($remoteData['current_question'], $playerRemoteData)) {
                if ($playerRemoteData[$remoteData['current_question']]['status'] == 'answered') {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'You have already answered this question',
                        'data' => [
                            'question' => $question['question'],
                            'question_id' => $remoteData['current_question']
                        ]
                    ]);
                }
            }
        }

        $response = [
            'question' => $question['question'],
            'question_id' => $remoteData['current_question'],
            'total_questions' => Questions::where('trivia_id', $remoteData['trivia_id'])->count(),
            'answers' => []
        ];

        foreach ($question->answers as $answer) {
            $response['answers'][] = [
                'id' => $answer['id'],
                'answer' => $answer['answer'],
            ];
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Question fetched successfully',
            'data' => $response
        ]);
    }

    public function submitAnswer($token, Request $request)
    {
        $data = GameApi::getGameInstance($token);
        $remoteData = json_decode($data['gameInstance']['remote_data'], true);

        //first step is to check if user have not already answered this question
        $haveAnswered = SubmittedAnswers::where('game_instance_id', $data['gameInstance']['id'])
            ->where('question_id', $remoteData['current_question'])
            ->where('user_id', Auth::user()->id)->first();

        if ($haveAnswered) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You have already answered this question',
                'data' => NULL
            ]);
        }

        SubmittedAnswers::create([
            'game_instance_id' => $data['gameInstance']['id'],
            'question_id' => $remoteData['current_question'],
            'answer_id' => $request->get('answer_id'),
            'user_id' => Auth::user()->id
        ]);

        $answer = Answers::find($request->get('answer_id'));

        $playerInstance = (GameApi::getPlayerInstance($data['gameInstance']['id'], Auth::user()->id))['playerInstance'];
        $playerRemoteData = json_decode($playerInstance['remote_data'], true);

        if (is_null($playerRemoteData)) {
            $playerRemoteData = [];
        }

        if ($answer->is_correct == 1) {
            $isCorrect = true;
            GameApi::updatePlayerInstanceScore($playerInstance['id'], $playerInstance['points'] + 1);
            $playerInstance['points'] = $playerInstance['points'] + 1;
        } else {
            $isCorrect = false;
        }

        $playerRemoteData[$remoteData['current_question']] = [
            'correct' => $isCorrect,
            'status' => 'answered',
            'answer_id' => $request->get('answer_id')
        ];
        /*
        array_push($playerRemoteData, [$remoteData['current_question'] => [
            'correct' => $isCorrect,
            'status' => 'answered',
            'answer_id' => $request->get('answer_id')
        ]]);*/

        $playerInstance['remote_data'] = $playerRemoteData;
        GameApi::updatePlayerInstanceRemoteData($playerInstance['id'], $playerInstance['remote_data']);

        return new JsonResponse([
            'success' => true,
            'message' => 'Answer submitted successfully',
            'data' => [
                'gameInstance' => $data['gameInstance'],
                'playerInstance' => $playerInstance,
                'correct' => $isCorrect
            ]
        ]);
    }

    public function nextQuestion($token, Request $request)
    {
        $data = GameApi::getGameInstance($token);
        $remoteData = json_decode($data['gameInstance']['remote_data'], true);

        $currentQuestion = $remoteData['current_question'];

        //step 1 check if current question isn't last available question
        if ($currentQuestion == Questions::where('trivia_id', $remoteData['trivia_id'])->count()) {
            //if it is last question then end game
            GameApi::changeGameInstanceStatus($token, 'ended');
            return new JsonResponse([
                'success' => true,
                'message' => 'Game ended successfully',
                'data' => [
                    'event' => 'gameOverEvent',
                    'gameInstance' => $data['gameInstance']
                ]
            ]);
        }

        //step 2 update remote data with next question number
        $remoteData['current_question'] = $currentQuestion + 1;
        GameApi::updateGameInstanceRemoteData($token, $remoteData);

        return new JsonResponse([
            'success' => true,
            'message' => 'Next question fetched successfully',
            'data' => [
                'event' => 'nextQuestionEvent',
                'question' => $remoteData['current_question'],
                'gameInstance' => $data['gameInstance']
            ]
        ]);
    }
}