<?php

use Illuminate\Support\Facades\Route;

//dd(config('settings.prefix'));
Route::group(['middleware' => ['web']], function() {
    Route::get('/trv/test', function () {
        return 'Hello World';
    });

    Route::get('/' . config('settings.prefix') . '/test', 'Controller@index');
    Route::get('/' . config('settings.prefix') . '/register', 'Controller@register');


    //create trivia route
    Route::get('/trivia', 'TriviaController@index');
    Route::post('/' . config('settings.prefix') . '/trivia', 'TriviaController@createGame');

    Route::get('/' . config('settings.prefix') . '/management', 'TriviaController@management');
    Route::post('/' . config('settings.prefix') . '/management/trivia', 'TriviaController@createTrivia');
    Route::get('/' . config('settings.prefix') . '/management/trivia/{id}', 'TriviaController@editTrivia');
    Route::post('/' . config('settings.prefix') . '/management/trivia/{id}', 'TriviaController@updateTrivia');

    Route::post('/' . config('settings.prefix') . '/management/trivia/{id}/question', 'TriviaController@createTriviaQuestion');
    Route::post('/'. config('settings.prefix')  . '/management/trivia/{id}/question/{questionId}/answer', 'TriviaController@createTriviaAnswer');
    Route::put('/' . config('settings.prefix') . '/management/trivia/{id}/question/{questionId}/order', 'TriviaController@updateQuestionOrder');

    Route::post('/' . config('settings.prefix') . '/csv-upload/trivia', 'TriviaController@csvUpload');
    Route::post('/' . config('settings.prefix') . '/api-single-upload/trivia', 'TriviaController@apiSingleAnswerQuestionCreator');
    Route::post('/' . config('settings.prefix') . '/management/api-trivia', 'TriviaController@createTriviaFromApi');

    Route::get('/' . config('settings.prefix') . '/reports/{reportType}', 'ReportsController@index');

    Route::post('/' . config('settings.prefix') . '/start', 'TriviaController@startGame');
    Route::post('/' . config('settings.prefix') . '/end', 'TriviaController@endGame');

    Route::post('/' . config('settings.prefix') . '/trivia/rating', 'TriviaController@rateTrivia');
    Route::post('/trivia/random', 'TriviaController@createRandomTrivia');

    Route::get('/' . config('settings.prefix') . '/trivia/{token}', 'TriviaController@game');

    Route::get('/trivia/{token}/processing', 'TriviaController@processingTmpTriviaGame');
    Route::get('/' . config('settings.prefix') . '/trivia/{token}/question', 'TriviaController@getQuestion');
    Route::post('/' . config('settings.prefix') . '/trivia/{token}/answer', 'TriviaController@submitAnswer');
    Route::post('/' . config('settings.prefix') . '/trivia/{token}/next', 'TriviaController@nextQuestion');
    Route::get('/' . config('settings.prefix') . '/trivia/{token}/correct', 'TriviaController@correctAnswer');
    Route::get('/' . config('settings.prefix') . '/trivia/{token}/results', 'TriviaController@results');
    Route::get('/' . config('settings.prefix') . '/trivia/{token}/leaderboard', 'TriviaController@leaderboard');

    Route::post('/' . config('settings.prefix') . '/trivia/{token}/accessibility', 'TriviaController@changeAccessibility');
    //Route::post('/' . config('settings.prefix') . '/trivia/{id}', 'TriviaController@startGame');

    Route::post('/trivia/{token}/add-points', 'TriviaController@addPoints');
    Route::post('/trivia/{token}/remove-points', 'TriviaController@removePoints');


    //Routes to create a new game
    Route::get('/admin/' . config('settings.prefix') . '/trivia', 'TriviaController@adminIndex');
    Route::post('/admin/' . config('settings.prefix') . '/trivia', 'TriviaController@create');
    Route::get('/admin/' . config('settings.prefix') . '/trivia/{id}', 'TriviaController@show');

    //Route to create a question for a game
    Route::get('/admin/' . config('settings.prefix') . '/question', 'QuestionController@index');
    Route::post('/admin/' . config('settings.prefix') . '/question', 'QuestionController@create');
    Route::get('/admin/' . config('settings.prefix') . '/question/{id}', 'QuestionController@show');

    //Route to create an answer for a question
    Route::get('/admin/' . config('settings.prefix') . '/answer', 'AnswerController@index');
    Route::post('/admin/' . config('settings.prefix') . '/answer', 'AnswerController@create');
    Route::get('/admin/' . config('settings.prefix') . '/answer/{id}', 'AnswerController@getAnswer');
    Route::post('/admin/' . config('settings.prefix') . '/answer/{id}', 'AnswerController@submitAnswer');
    Route::post('/admin/' . config('settings.prefix') . '/answer-image/{id}', 'AnswerController@submitAnswerImage');

    //Route to get available games
    Route::get('/admin/' . config('settings.prefix') . '/games', 'Controller@getGames');
});