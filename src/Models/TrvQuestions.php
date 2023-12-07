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
        'question_type'
    ];

    public function answers()
    {
        return $this->hasMany(Answers::class, 'question_id', 'id');
    }

}