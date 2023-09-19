<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
class Questions extends Model
{

    protected $table = 'trv_questions';
    protected $fillable = [
        'trivia_id',
        'question'
    ];

}