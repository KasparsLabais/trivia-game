<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class TmpQuestions extends Model
{

    protected $table = 'trv_tmp_questions';
    protected $fillable = ['question', 'order_nr', 'original_question_id', 'tmp_trivia_id', 'question_type'];

    public function answers()
    {
        return $this->hasMany(Answers::class, 'question_id', 'original_question_id');
    }


}