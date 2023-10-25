<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Models\Game;
use PartyGames\TriviaGame\Models\Categories;
use PartyGames\TriviaGame\Models\Questions;
use PartyGames\TriviaGame\Models\SubmittedAnswers;
use PartyGames\TriviaGame\Models\Trivia;
use PartyGames\TriviaGame\Models\Answers;
use PartyGames\TriviaGame\Models\Ratings;

use Illuminate\Support\Facades\Auth;

class TriviaController
{

    public function index()
    {
        $categories = Categories::where('is_active', 1)->orderBy('name')->get();
        if(Auth::check()) {
            $usersTrivias = Trivia::where('user_id', Auth::user()->id)->get();
        } else {
            $usersTrivias = [];
        }
        return view('trivia-game::pages.index')->with(['categories' => $categories, 'usersTrivias' => $usersTrivias]);
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
        $remoteData = json_decode($response['gameInstance']['remote_data'], true);

        if ($gameInstance['status'] == 'created') {
            $trivia = Trivia::find($remoteData['trivia_id']);
            return view('trivia-game::game.start')->with(['gameInstance' => $gameInstance, 'trivia' => $trivia]);
        }

        if ($gameInstance['status'] == 'completed') {
            return redirect()->to("/trv/trivia/{$gameToken}/results");
            //return view('trivia-game::game.results')->with(['gameInstance' => $gameInstance]);
        }

        $question = Questions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
        $answeredUsers = SubmittedAnswers::where('game_instance_id', $gameInstance['id'])->where('question_id', $question['id'])->get();

        $returnObject = [
            'gameInstance' => $gameInstance,
            'remoteData' => $remoteData,
            'answeredUsers' => $answeredUsers,
            'playerInstance' => $playerInstance['playerInstance']
        ];
        return view('trivia-game::game.play')->with($returnObject);
    }

    public function adminIndex()
    {
        $allTrivia = Trivia::all();
        $categories = Categories::where('is_active', 1)->get();

        return view('trivia-game::admin.trivia.index')->with(['allTrivia' => $allTrivia, 'categories' => $categories]);
    }

    public function create(Request $request)
    {
        $trivia = Trivia::create([
            'title' => $request->title,
            'category_id' => $request->category,
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

        if ($gameInstance['gameInstance']['user_id'] != Auth::user()->id) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You are not the owner of this game instance',
                'data' => []
            ]);
        }

        if ($gameInstance['gameInstance']['status'] != 'created') {
            return new JsonResponse([
                'success' => false,
                'message' => 'Game instance is not in correct status',
                'data' => []
            ]);
        }

        if($gameInstance['gameInstance']->playerInstances->count() == 0) {
            return new JsonResponse([
                'success' => false,
                'message' => "You can't start game without players",
                'data' => []
            ]);
        }

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
        $question = Questions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
        $haveAnswered = SubmittedAnswers::where('game_instance_id', $data['gameInstance']['id'])
            ->where('question_id', $question['id'])
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
            'question_id' => $question['id'],
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
            GameApi::changeGameInstanceStatus($token, 'completed');
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

    public function correctAnswer($token, Request $request)
    {
        $data = GameApi::getGameInstance($token);
        if ($data['gameInstance']['user_id'] != Auth::user()->id) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You are not the owner of this game instance',
                'data' => []
            ]);
        }

        $remoteData = json_decode($data['gameInstance']['remote_data'], true);

        $currentQuestion = $remoteData['current_question'];

        $question = Questions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $currentQuestion)->first();
        $correctAnswer = Answers::where('question_id', $question['id'])->where('is_correct', 1)->first();

        return new JsonResponse([
            'success' => true,
            'message' => 'Correct answer fetched successfully',
            'data' => [
                'answer' => $correctAnswer,
            ]
        ]);
    }

    public function results($token)
    {
        $gameInstance = GameApi::getGameInstance($token);
        $winners = GameApi::getWinners($gameInstance['gameInstance']['id']);

        return view('trivia-game::game.results')->with(['gameInstance' => $gameInstance['gameInstance'], 'winners' => $winners['response']]);
    }


    /* all individual user functions */
    public function management()
    {
        $usersTrivias = Trivia::where('user_id', Auth::user()->id)->get();
        $categories = Categories::where('is_active', 1)->get();

        return view('trivia-game::pages.management')->with(['trivias' => $usersTrivias, 'categories' => $categories]);
    }

    public function createTrivia(Request $request)
    {
        $newTrivia = Trivia::create([
            'title' => $request->title,
            'category_id' => $request->category,
            'description' => $request->description,
            'difficulty' => $request->difficulty,
            'type' => 'boolean', //just for now
            'user_id' => Auth::user()->id,
        ]);

        return redirect()->to('/trv/management/trivia/' . $newTrivia['id']);
    }

    public function editTrivia($id)
    {
        if (!Auth::check()) {
            return redirect()->to('/trv/trivia');
        }

        $trivia = Trivia::where('id', $id)->first();

        if (Auth::user()->user_id == $trivia->user_id) {
            return redirect()->to('/trv/trivia');
        }

        $questions = Questions::where('trivia_id', $id)->orderBy('order_nr')->get();
        $categories = Categories::where('is_active', 1)->get();

        return view('trivia-game::pages.edit')->with(['trivia' => $trivia, 'questions' => $questions, 'categories' => $categories]);
    }

    public function updateTrivia($id, Request $request)
    {
        $trivia = Trivia::where('id', $id)->first();
        $trivia->title = $request->title;
        $trivia->description = $request->description;
        $trivia->category_id = $request->category;
        $trivia->difficulty = $request->difficulty;
        $trivia->is_active = (int)$request->is_active;
        $trivia->save();

        return redirect()->back();
    }

    public function createQuestion($id, Request $request)
    {
        //get last order_nr for this trivia
        $lastOrder = Questions::where('trivia_id', $id)->orderBy('order_nr', 'desc')->first();
        $question = Questions::create([
            'trivia_id' => $id,
            'question' => $request->question,
            'order_nr' => !isset($lastOrder['order_nr']) ? 1 : $lastOrder['order_nr']+1,
        ]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Question created successfully',
            'payload' => $question
        ]);
    }

    public function createAnswer($id, $questionId, Request $request)
    {
        $answer = Answers::create([
            'question_id' => $questionId,
            'answer' => $request->answer,
            'is_correct' => $request->is_correct,
        ]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Answer created successfully',
            'payload' => $answer
        ]);
    }

    public function updateQuestionOrder($id, $questionId, Request $request)
    {
        $question = Questions::where('id', $questionId)->first();
        $question->order_nr = $request->order_nr;
        $question->save();

        return new JsonResponse([
            'success' => true,
            'message' => 'Question order updated successfully',
            'payload' => $question
        ]);
    }

    public function csvUpload(Request $request)
    {
        $csvFile = $request->file('trivia-csv');
        $csvData = file_get_contents($csvFile);

        $rows = array_map("str_getcsv", explode("\n", $csvData));
        $header = array_shift($rows);

        if (count($header) < 2) {
            return redirect()->back()->with([
                'success' => false,
                'message' => 'CSV file is not in correct format',
                'payload' => NULL
            ]);
        }

        $csv = array();
        foreach ($rows as $row) {
            $csv[] = [
                'question' => $row[0],
                'answers' => []
            ];
            for ($i = 1; $i < count($row); $i++) {
                $csv[count($csv) - 1]['answers'][] = ['answer' => $row[$i], 'is_correct' => ($i == 1) ? 1 : 0];
            }
        }

        $trivia = Trivia::create([
            'title' => $header[0],
            'category_id' => 1,
            'description' => $header[1],
            'difficulty' => 'easy',
            'type' => 'boolean', //just for now
            'user_id' => Auth::user()->id,
        ]);

        foreach ($csv as $key => $question) {

            if (is_null($question['question'])) {
                continue;
            }

            $newQuestion = Questions::create([
                'trivia_id' => $trivia->id,
                'question' => $question['question'],
                'order_nr' => $key + 1,
            ]);

            //randomize answers order for each question
            shuffle($question['answers']);
            foreach ($question['answers'] as $answer) {
                Answers::create([
                    'question_id' => $newQuestion->id,
                    'answer' => $answer['answer'],
                    'is_correct' => $answer['is_correct'],
                ]);
            }
        }

        return redirect()->back();
    }

    public function rateTrivia(Request $request)
    {

        if (!Auth::check()) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You are not logged in',
                'data' => NULL
            ]);
        }

        $trivia = Trivia::where('id', $request->get('trivia_id'))->first();
        if (!$trivia) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Trivia not found',
                'data' => NULL
            ]);
        }

        //check if user has already rated this trivia
        $rating = Ratings::where('trivia_id', $trivia->id)->where('user_id', Auth::user()->id)->first();
        if($rating) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You have already rated this trivia',
                'data' => NULL
            ]);
        }

        //check if rating is between 1 and 5
        if($request->get('rating') < 1 || $request->get('rating') > 5) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Rating must be between 1 and 5',
                'data' => NULL
            ]);
        }

        //rate a trivia
        $rating = Ratings::create([
            'trivia_id' => $trivia->id,
            'user_id' => Auth::user()->id,
            'rating' => $request->get('rating')
        ]);

        return new JsonResponse([
            'success' => true,
            'message' => 'Trivia rated successfully',
            'data' => $rating
        ]);
    }
}