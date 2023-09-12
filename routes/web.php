<?php

use Illuminate\Support\Facades\Route;

Route::get('/' . config('prefix') . '/test', 'BaseController@index');
Route::get('/' . config('prefix') . '/register', 'BaseController@register');

//Routes to create a new game
Route::get('/' . config('prefix') . '/create', 'BaseController@create');
//Route to create a question for a game
Route::get('/' . config('prefix') . '/create/question', 'BaseController@createQuestion');
//Route to create an answer for a question
Route::get('/' . config('prefix') . '/create/answer', 'BaseController@createAnswer');

//Route to get available games
Route::get('/' . config('prefix') . '/games', 'BaseController@getGames');
//Route to get a specific game
Route::get('/' . config('prefix') . '/game/{id}', 'BaseController@getGame');
//Route to get a specific question
Route::get('/' . config('prefix') . '/question/{id}', 'BaseController@getQuestion');
//Route to get a specific answer
Route::get('/' . config('prefix') . '/answer/{id}', 'BaseController@getAnswer');
//Route to submit answer to a question
Route::post('/' . config('prefix') . '/answer/{id}', 'BaseController@submitAnswer');