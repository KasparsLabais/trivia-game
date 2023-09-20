<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
class Answers extends Model
{

        protected $table = 'trv_answers';
        protected $fillable = [
            'question_id',
            'answer',
            'is_correct'
        ];
}