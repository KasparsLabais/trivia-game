<?php

use Illuminate\Support\Facades\Route;

Route::get('/' . config('prefix') . '/test', 'BaseController@index');
Route::get('/' . config('prefix') . '/register', 'BaseController@register');