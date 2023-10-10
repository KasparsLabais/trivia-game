<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class SubmittedAnswers extends Model
{
    protected $table = 'trv_submitted_answers';
    protected $fillable = [
        'game_instance_id',
        'question_id',
        'answer_id',
        'user_id'
    ];
}