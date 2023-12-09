<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
class TrvAnswers extends Model
{

        protected $table = 'trv_answers';
        protected $fillable = [
            'question_id',
            'answer',
            'is_correct',
            'file_url',
            'file_url_type',
            'original_answer_id'
        ];
}