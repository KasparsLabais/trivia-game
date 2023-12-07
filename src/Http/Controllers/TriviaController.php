<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Models\Game;
use PartyGames\GameApi\Models\TmpUsers;
use PartyGames\TriviaGame\Models\TmpTrivia;
use PartyGames\TriviaGame\Models\TmpQuestions;
use PartyGames\TriviaGame\Models\Categories;
use PartyGames\TriviaGame\Models\TrvQuestions;
use PartyGames\TriviaGame\Models\SubmittedAnswers;
use PartyGames\TriviaGame\Models\Trivia;
use PartyGames\TriviaGame\Models\Answers;
use PartyGames\TriviaGame\Models\Ratings;
use PartyGames\TriviaGame\Models\OpenTrivias;

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

        $openTrivias = OpenTrivias::where('status', 1)->orderBy('created_at', 'desc')->get();
        return view('trivia-game::pages.index')->with(['categories' => $categories, 'usersTrivias' => $usersTrivias, 'openTrivias' => $openTrivias]);
    }

    public function createGame(Request $request)
    {
        $trivia = Trivia::where('id', $request->get('trivia_id'))->where(function($q) {
            $q->where('user_id', Auth::user()->id)->orWhere('is_premium', 0);
        })->first();

        if(!$trivia) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Trivia not found',
                'data' => NULL
            ]);
        }

        $remoteData = [
            'trivia_id' => $trivia->id,
            'is_temporary' => 0,
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

        if(Auth::check()) {
            $userId = Auth::user()->id;
        } else {
            if(Session::get('tmp-user-id') != null) {
                $userId = Session::get('tmp-user-id');
            } else {
                return redirect()->to('/trv/trivia');
            }
        }

        $playerInstance = GameApi::getPlayerInstance($response['gameInstance']['id'], $userId);

        //if game instance is not found redirect to /trv/trivia
        if ($response['status'] == false) {
            return redirect()->route('trv.trivia');
        }

        $gameInstance = $response['gameInstance'];
        $remoteData = json_decode($response['gameInstance']['remote_data'], true);


        if (Auth::check() && Auth::user()->id == $gameInstance['user_id']) {

            if($remoteData['is_temporary'] == 1) {
                $trivia = TmpTrivia::find($remoteData['trivia_id']);
            } else {
                $trivia = Trivia::find($remoteData['trivia_id']);
            }

            $leaderboard = GameApi::getLeaderboard($gameToken);
            $playerInstance = [];

            foreach($gameInstance->playerInstances as $player) {

                $tmpUser = [];

                if($player->user_type == 'player') {
                    $player->load('user');
                    $player->user->load('iconFlair');
                    $playerInstance[$player->user_id] = $player;

                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'username' => $player->user->username,
                        'avatar' => ($player->user->avatar) ?: '/images/default-avatar.jpg',
                        'icon_flair' => $player->user->iconFlair->icon_url,
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                } else {
                    $player->load('tmpUser');
                //    $playerInstance[$player->user_id] = $player;
                    $tmpUser = [
                        'id' => $player->id,
                        'user_id' => $player->user_id,
                        'tmp_user_id' => $player->tmpUser->id,
                        'username' => $player->tmpUser->username,
                        'avatar' => '/images/default-avatar.jpg',
                        'icon_flair' => '',
                        'points' => $player->points,
                        'user_type' => $player->user_type,
                        'remote_data' => json_decode($player->remote_data, true),
                    ];
                }

                $playerInstance[$player->user_id] = $tmpUser;
            }

            $returnObject = [
                'gameInstance' => $gameInstance,
                'playerInstances' => $playerInstance,
                'trivia' => $trivia,
                'questions' => $trivia->questions,
                'leaderboard' => $leaderboard,
            ];

            return view('trivia-game::game.master-control')->with($returnObject);
        }



        if ($gameInstance['status'] == 'created' || $gameInstance['status'] == 'started') {

            // check if this is not temporary trivia game
            if($remoteData['is_temporary'] == 1) {
                $trivia = TmpTrivia::find($remoteData['trivia_id']);
            } else {
                $trivia = Trivia::find($remoteData['trivia_id']);
            }

            //$tmpPlayer =

            if (Auth::check()) {
                $playerInstance['playerInstance']['user'] = Auth::user();
            } else {
                $playerInstance['playerInstance']['user'] = TmpUsers::where('tmp_user_id',Session::get('tmp-user-id'))->first();
            }

            $trivia->load('category');
            return view('trivia-game::game.player-control')->with(['gameInstance' => $gameInstance, 'trivia' => $trivia, 'player' => $playerInstance['playerInstance']]);
        }

        if ($gameInstance['status'] == 'completed') {
            return redirect()->to("/trv/trivia/{$gameToken}/results");
            //return view('trivia-game::game.results')->with(['gameInstance' => $gameInstance]);
        }

        if ($remoteData['is_temporary'] == 1) {
            $question = TmpQuestions::where('tmp_trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
            $answeredUsers = SubmittedAnswers::where('game_instance_id', $gameInstance['id'])->where('question_id', $question['original_question_id'])->get();
        } else {
            $question = TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
            $answeredUsers = SubmittedAnswers::where('game_instance_id', $gameInstance['id'])->where('question_id', $question['id'])->get();
        }

        $returnObject = [
            'totalQuestions' => ($remoteData['is_temporary'] == 1) ? TmpQuestions::where('tmp_trivia_id', $remoteData['trivia_id'])->count() : TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->count(),
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

        if(Auth::check()) {
            $userId = Auth::user()->id;
        } else {
            if(Session::get('tmp-user-id') != null) {
                $userId = Session::get('tmp-user-id');
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'You are not logged in',
                    'data' => NULL
                ]);
            }
        }

        $playerInstance = GameApi::getPlayerInstance($data['gameInstance']['id'], $userId);
        $remoteData = json_decode($data['gameInstance']['remote_data'], true);

        if ($remoteData['is_temporary']) {
            $question = TmpQuestions::where('tmp_trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
        } else {
            $question = TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $remoteData['current_question'])->first();
        }
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

        $settingsResponse = GameApi::getGameInstanceSettings($token);
        $response = [
            'settings' => $settingsResponse['gameInstanceSetting'],
            'question' => $question['question'],
            'question_id' => $remoteData['current_question'],
            'total_questions' => TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->count(),
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
        $isCorrect = 0;

        if(Auth::check()) {
            $userId = Auth::user()->id;
        } else {
            if(Session::get('tmp-user-id') != null) {
                $userId = Session::get('tmp-user-id');
            } else {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'You are not logged in',
                    'data' => NULL
                ]);
            }
        }

        $questionId = $request->get('question_id');
        //first step is to check if user have not already answered this question

        if ($remoteData['is_temporary']) {
            $question = TmpQuestions::where('tmp_trivia_id', $remoteData['trivia_id'])->where('original_question_id', $questionId)->first();
            //$questionId = $question['original_question_id'];
        } else {
            $question = TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->where('id', $questionId)->first();
            //$questionId = $question['id'];
        }

        $haveAnswered = SubmittedAnswers::where('game_instance_id', $data['gameInstance']['id'])
            ->where('question_id', $questionId)
            ->where('user_id', $userId)->first();

        if ($haveAnswered) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You have already answered this question',
                'data' => NULL
            ]);
        }

        if ($question['question_type'] == 'options') {
            SubmittedAnswers::create([
                'game_instance_id' => $data['gameInstance']['id'],
                'question_id' => $questionId,
                'answer_id' => $request->get('answer_id'),
                'user_id' => $userId
            ]);
            $answer = Answers::find($request->get('answer_id'));
            $isCorrect = $answer->is_correct;
        } elseif($question['question_type'] == 'text_input') {
            SubmittedAnswers::create([
                'game_instance_id' => $data['gameInstance']['id'],
                'question_id' => $questionId,
                'answer_custom_input' => $request->get('answer_text'),
                'answer_id' => 0,
                'user_id' => $userId
            ]);
            $answer = Answers::where('question_id', $questionId)->first();

            if(strtolower($answer['answer']) == strtolower($request->get('answer_text'))) {
                $isCorrect = 1;
            }
        }



        $playerInstance = (GameApi::getPlayerInstance($data['gameInstance']['id'], $userId))['playerInstance'];
        $playerRemoteData = json_decode($playerInstance['remote_data'], true);

        if (is_null($playerRemoteData)) {
            $playerRemoteData = [];
        }

        if ($isCorrect) {

            $isCorrect = true;

            $totalPointsGiven = (GameApi::getGameInstanceSettings($token, 'points_per_question') == '') ? 2 : GameApi::getGameInstanceSettings($token, 'points_per_question');
            //$totalPointsGiven = $playerInstance['points'] * $pointsPerQuestion;

            if ($question['question_type'] == 'options') {
                if (GameApi::isFirstAnsweredCorrectlyToQuestion($data['gameInstance']['id'], $questionId, $request->get('answer_id'), $userId)) {
                    $bonusPointsForSpeed = (GameApi::getGameInstanceSettings($token, 'bonus_for_speed') == '') ? 2 : GameApi::getGameInstanceSettings($token, 'bonus_for_speed');
                    $totalPointsGiven = $totalPointsGiven + $bonusPointsForSpeed;
                }
            } elseif($question['question_type'] == 'text_input') {
                if (GameApi::isFirstTextInputCorrectAnswer($data['gameInstance']['id'], $questionId, $answer['answer'], $userId)) {
                    $bonusPointsForSpeed = (GameApi::getGameInstanceSettings($token, 'bonus_for_speed') == '') ? 2 : GameApi::getGameInstanceSettings($token, 'bonus_for_speed');
                    $totalPointsGiven = $totalPointsGiven + $bonusPointsForSpeed;
                }
            }

            GameApi::updatePlayerInstanceScore($playerInstance['id'], $playerInstance['points'] + $totalPointsGiven);
            $playerInstance['points'] = $playerInstance['points'] + $totalPointsGiven;
        } else {
            $isCorrect = 0;
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

        if ($remoteData['is_temporary']) {
            $questionCount = TmpQuestions::where('tmp_trivia_id', $remoteData['trivia_id'])->count();
        } else {
            $questionCount = TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->count();
        }

        //step 1 check if current question isn't last available question
        if ($currentQuestion >= $questionCount) {
            //if it is last question then end game
            GameApi::changeGameInstanceStatus($token, 'completed');

            if ($remoteData['is_temporary']) {
                if ($questionCount > 20) {
                    GameApi::giveUsersGameCurrency($token, $remoteData['trivia_id'], 'tmp_trivia_id');
                }
            } else {
                GameApi::giveUsersGameCurrency($token, $remoteData['trivia_id'], 'trivia_id');
            }

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

        if ($remoteData['is_temporary']) {
            $question = TmpQuestions::where('tmp_trivia_id', $remoteData['trivia_id'])->where('order_nr', $currentQuestion)->first();
            $questionId = $question['original_question_id'];
        } else {
            $question = TrvQuestions::where('trivia_id', $remoteData['trivia_id'])->where('order_nr', $currentQuestion)->first();
            $questionId = $question['id'];
        }

        $correctAnswer = Answers::where('question_id', $questionId)->where('is_correct', 1)->first();
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

        $accessibility = GameApi::getGameInstanceSettings($token, 'accessibility');
        if ($accessibility == 'public') {
            OpenTrivias::where('game_instance_id', $gameInstance['gameInstance']['id'])->update(['status' => 0, 'closed_at' => date('Y-m-d H:i:s')]);
        }

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

        $questions = TrvQuestions::where('trivia_id', $id)->orderBy('order_nr')->get();
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
        $trivia->private = (int)$request->private;
        $trivia->is_premium = (int)$request->is_premium;
        $trivia->save();

        return redirect()->back();
    }

    public function createQuestion($id, Request $request)
    {
        //get last order_nr for this trivia
        $lastOrder = TrvQuestions::where('trivia_id', $id)->orderBy('order_nr', 'desc')->first();
        $question = TrvQuestions::create([
            'trivia_id' => $id,
            'question' => $request->question,
            'order_nr' => !isset($lastOrder['order_nr']) ? 1 : $lastOrder['order_nr']+1,
            'question_type' => $request->question_type
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
        $question = TrvQuestions::where('id', $questionId)->first();
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

            $newQuestion = TrvQuestions::create([
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

    public function changeAccessibility($token, Request $request)
    {

        $game = GameApi::getGameInstance($token);
        $gameInstance = $game['gameInstance'];

        if ($gameInstance['user_id'] != Auth::user()->id) {
            return new JsonResponse([
                'success' => false,
                'message' => 'You are not the owner of this game instance',
                'payload' => []
            ]);
        }

        $remoteData = json_decode($gameInstance['remote_data'], true);

        switch ($request->get('accessibility')) {
            case 'public':
                $remoteData['accessibility'] = 'public';
                $openTrivia = OpenTrivias::firstOrCreate(
                    ['trivia_id' => $remoteData['trivia_id'], 'game_instance_id' => $gameInstance['id'], 'user_id' => Auth::user()->id, 'status' => 1, 'is_temporary' => $remoteData['is_temporary']]
                );

                GameApi::addOrUpdateGameInstanceSetting($token, 'accessibility', 'public');
                break;
            case 'private':
                $openTrivia = OpenTrivias::where('trivia_id', $remoteData['trivia_id'])->where('game_instance_id', $gameInstance['id'])->where('user_id', Auth::user()->id)->where('status', 1)->first();
                if ($openTrivia) {
                    $openTrivia->status = 0;
                    $openTrivia->closed_at = date('Y-m-d H:i:s');
                    $openTrivia->save();
                }
                $remoteData['accessibility'] = 'private';
                GameApi::addOrUpdateGameInstanceSetting($token, 'accessibility', 'private');
                break;
            case 'password':
                $remoteData['accessibility'] = 'password';
                $gameInstance['password'] = $request->get('password');

                GameApi::addOrUpdateGameInstanceSetting($token, 'accessibility', 'password');
                break;
        }

        $gameInstance['remote_data'] = $remoteData;
        GameApi::updateGameInstanceRemoteData($token, $remoteData);

        return new JsonResponse([
            'success' => true,
            'message' => 'Accessibility changed successfully',
            'payload' => [
                'gameInstance' => $gameInstance,
                'newAccessibility' => $request->get('accessibility')
            ]
        ]);
    }

    public function createRandomTrivia(Request $request)
    {

        $title = $request->get('title');
        $description = !is_null($request->get('description')) ? $request->get('description') : '';
        $category = $request->get('category');
        $difficulty = $request->get('difficulty');
        $questionCount = $request->get('question_count');
        //    protected $fillable = ['title', 'category_id', 'difficulty', 'private', 'question_count'];

        $questions = [];
        $allTriviasForProvidedCategory = Trivia::where(function($q) use( $category) {
            if($category != 23) {
                $q->where('category_id', $category);
            }
        })->where(function($q) use ($difficulty){
            if ($difficulty != 'any')
            {
                $q->where('difficulty', $difficulty);
            }
        })->where(function($q) {
            $q->where('private', 0)->orWhere('user_id', Auth::user()->id);
        })->where(function($q) {
            $q->where('is_premium', 0)->orWhere('user_id', Auth::user()->id);
        })->get();

        foreach ($allTriviasForProvidedCategory as $trivia) {
            $tmpQuestions = TrvQuestions::where('trivia_id', $trivia->id)->get();
            foreach ($tmpQuestions as $tmpQuestion) {
                $questions[] = $tmpQuestion;
            }
        }

        //select from array random questions to match question count or less if there is not enough questions, provided by user
        $tmpQuestions = array_rand($questions, ( count($questions) < $questionCount ) ? count($questions) : $questionCount);
        $tmpTrivia = TmpTrivia::create([
            'title' => $title,
            'description' => $description,
            'category_id' => $category,
            'difficulty' => $difficulty,
            'question_count' => count($tmpQuestions)
        ]);

        $tmpOrderNr = 1;
        foreach ($tmpQuestions as $tmpQuestionId) {
            //var_dump($tmpQuestionId);die();
            //find question in $questions object by searching matching key
            $question = $questions[$tmpQuestionId];
            $tmpQuestion = TmpQuestions::create([
                'question' => $question->question,
                'order_nr' => $tmpOrderNr,
                'original_question_id' => $question->id,
                'tmp_trivia_id' => $tmpTrivia->id
            ]);
            $tmpOrderNr++;
        }

        $remoteData = [
            'trivia_id' => $tmpTrivia->id,
            'is_temporary' => 1,
        ];
        $response = GameApi::createGameInstance(config('settings.token'), $tmpTrivia->title, $remoteData);

        if ($response['status'] == false) {
            //TODO: need to setup some action for this case
        }
        return redirect()->to('/trv/trivia/' . $response['gameInstance']['token'] . '/processing');
    }

    public function processingTmpTriviaGame($token)
    {
        $data = GameApi::getGameInstance($token);
        $remoteData = json_decode($data['gameInstance']['remote_data'], true);

        if ($remoteData['is_temporary'] == 0) {
            return redirect()->to('/trv/trivia/' . $token);
        }

        $tmpTrivia = TmpTrivia::find($remoteData['trivia_id']);
        return view('trivia-game::pages.processing')->with(['tmpTrivia' => $tmpTrivia, 'token' => $token, 'gameInstance' => $data['gameInstance'], 'remoteData' => $remoteData]);

    }

    public function createTriviaFromApi(Request $request)
    {
        $category = $request->get('category');
        $openDbCatId = $this->getOpenDBCorrespondingCategoryId($category);

        $trivia = Trivia::create([
            'title' => $request->get('title'),
            'category_id' => $category,
            'description' => $request->get('description'),
            'difficulty' => $request->get('difficulty'),
            'type' => 'boolean', //just for now
            'user_id' => Auth::user()->id,
            'is_active' => 1,
        ]);

        $questions = $this->getOpenTriviaDbResults($openDbCatId, $request->get('difficulty'));
        $orderNr = 1;

        foreach ($questions as $question) {

            //check if question is not already in database
            $questionInDb = TrvQuestions::where('question',html_entity_decode($question['question']))->first();
            if($questionInDb) {
                continue;
            }

            if($orderNr > 30) {
                //for now as we want from results only create quiz with 30 questions
                continue;
            }
            //before inserting question to database convert all special characters to html entities


            $newQuestion = TrvQuestions::create([
                'trivia_id' => $trivia->id,
                'question' => html_entity_decode($question['question']),
                'order_nr' => $orderNr,
            ]);

            $answers[] = [
                'answer' => $question['correct_answer'],
                'is_correct' => 1
            ];

            foreach ($question['incorrect_answers'] as $incorrectAnswer) {
                $answers[] = [
                    'answer' => $incorrectAnswer,
                    'is_correct' => 0
                ];
            }


            shuffle($answers);
            foreach ($answers as $answer) {
                Answers::create([
                    'question_id' => $newQuestion->id,
                    'answer' => html_entity_decode($answer['answer']),
                    'is_correct' => $answer['is_correct'],
                ]);
            }

            $answers = [];
            $orderNr++;
        }

        return redirect()->back();
    }

    private function getOpenDBCorrespondingCategoryId($categoryId)
    {


        switch ($categoryId) {
            case 1: //General Knowledge
                return 9;
                break;
            case 2: //Entertainment: Books
                return 10;
                break;
            case 3: //Entertainment: Film
                return rand(0,1) == 0 ?  11 : 14; //11 - film, 14 - tv
                break;
            case 4: //Entertainment: Music
                return 12;
                break;
            case 5: //Entertainment: Musicals & Theatres
                return 13;
                break;
            case 7: //Entertainment: Video Games
                return 15;
                break;
            case 8: //Entertainment: Anime & Manga
                return 31;
                break;
            case 9: //Entertainment: Cartoon & Animations & Comics
                return rand(0,1) == 0 ? 32 : 29; //32 - cartoon, 29 - comics
                break;
            case 10: //Entertainment: Celebrities
                return 26;
                break;
            case 11: //Animals
                return 27;
                break;
            case 12: //Geography
                return 22;
                break;
            case 13: //History
                return 23;
                break;
            case 14: // Science: Computers & Gadgets
                return rand(0,1) == 0 ? 18 : 30; //18 - science: computers, 30 - science: gadgets
                break;
            case 15: //Science: Mathematics
                return 19;
                break;
            case 16: //Entertainment: Board Games
                return 16;
                break;
            case 17: //Science: Nature
                return 17;
                break;
            case 18: //Sports
                return 21;
                break;
            case 19: //Mythology
                return 20;
                break;
            case 20: //Politics
                return 24;
                break;
            case 21: //Vehicles
                return 28;
                break;
            case 22:
                return 25;
                break;
        }

        return 9; //General Knowledge
    }

    private function getOpenTriviaDbResults($categoryId, $difficulty)
    {
        //https://opentdb.com/api.php?amount=10&category=11&difficulty=easy&type=multiple

        $amount = 50;
        $hasResult = true;

        while($hasResult) {
            $url = 'https://opentdb.com/api.php?amount=' . $amount . '&category=' . $categoryId . '&difficulty=' . $difficulty;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            $jsonResponse = json_decode($response, true);
            if($jsonResponse['response_code'] == 0) {
                $hasResult = false;
                break;
            }
            $amount = $amount - 5;
        }

        $jsonResponse = json_decode($response, true);
        return $jsonResponse['results'];
    }

    public function leaderboard($token) {
        $leaderboard = GameApi::getLeaderboard($token);
        return new JsonResponse([
            'success' => true,
            'message' => 'Leaderboard fetched successfully',
            'data' => $leaderboard
        ]);
        //return view('trivia-game::pages.leaderboard')->with(['leaderboard' => $leaderboard]);
    }
}