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
    Route::get('/' . config('settings.prefix') . '/trivia', 'TriviaController@index');
    Route::post('/' . config('settings.prefix') . '/trivia', 'TriviaController@startGame');

    //Route::post('/' . config('settings.prefix') . '/trivia/{id}', 'TriviaController@startGame');

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

    //Route to get available games
    Route::get('/admin/' . config('settings.prefix') . '/games', 'Controller@getGames');
});