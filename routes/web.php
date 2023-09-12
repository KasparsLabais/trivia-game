<?php

use Illuminate\Support\Facades\Route;

Route::get('/' . config('prefix') . '/test', 'Controller@index');
Route::get('/' . config('prefix') . '/register', 'Controller@register');

//Routes to create a new game
Route::get('/' . config('prefix') . '/trivia', 'TriviaController@index');
Route::post('/' . config('prefix') . '/trivia', 'TriviaController@create');
Route::get('/' . config('prefix') . '/trivia/{id}', 'TriviaController@getTrivia');

//Route to create a question for a game
Route::get('/' . config('prefix') . '/question', 'QuestionController@index');
Route::post('/' . config('prefix') . '/question', 'QuestionController@create');
Route::get('/' . config('prefix') . '/question/{id}', 'QuestionController@getQuestion');

//Route to create an answer for a question
Route::get('/' . config('prefix') . '/answer', 'AmswerController@index');
Route::post('/' . config('prefix') . '/answer', 'AnswerController@create');
Route::get('/' . config('prefix') . '/answer/{id}', 'AnswerController@getAnswer');
Route::post('/' . config('prefix') . '/answer/{id}', 'AnswerController@submitAnswer');

//Route to get available games
Route::get('/' . config('prefix') . '/games', 'Controller@getGames');


