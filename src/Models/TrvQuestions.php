<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
class TrvQuestions extends Model
{

    protected $table = 'trv_questions';
    protected $fillable = [
        'trivia_id',
        'question',
        'order_nr',
        'question_type',
        'original_question_id'
    ];

    public function answers()
    {
        return $this->hasMany(TrvAnswers::class, 'question_id', 'id');
    }

}