<?php

namespace Partygames\TriviaGame\Http\Controllers;

class TriviaController
{

    public function index() {
        return view('trivia-game::trivia.index');
    }

    public function create() {
        return view('trivia-game::trivia.create');
    }
}