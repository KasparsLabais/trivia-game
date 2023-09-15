<?php

use Illuminate\Support\Facades\Route;

//dd(config('settings.prefix'));
Route::get('/trv/test', function () {
    return 'Hello World';
});

Route::get('/' . config('settings.prefix') . '/test', 'Controller@index');
Route::get('/' . config('settings.prefix') . '/register', 'Controller@register');

//Routes to create a new game
Route::get('/' . config('settings.prefix') . '/trivia', 'TriviaController@index');
Route::post('/' . config('settings.prefix') . '/trivia', 'TriviaController@create');
Route::get('/' . config('settings.prefix') . '/trivia/{id}', 'TriviaController@show');

//Route to create a question for a game
Route::get('/' . config('settings.prefix') . '/question', 'QuestionController@index');
Route::post('/' . config('settings.prefix') . '/question', 'QuestionController@create');
Route::get('/' . config('settings.prefix') . '/question/{id}', 'QuestionController@show');

//Route to create an answer for a question
Route::get('/' . config('settings.prefix') . '/answer', 'AmswerController@index');
Route::post('/' . config('settings.prefix') . '/answer', 'AnswerController@create');
Route::get('/' . config('settings.prefix') . '/answer/{id}', 'AnswerController@getAnswer');
Route::post('/' . config('settings.prefix') . '/answer/{id}', 'AnswerController@submitAnswer');

//Route to get available games
Route::get('/' . config('settings.prefix') . '/games', 'Controller@getGames');


