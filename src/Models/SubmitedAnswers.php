<?php

namespace Partygames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class SubmitedAnswers extends Model
{
    protected $table = 'trv_submited_answers';
    protected $fillable = [
        'game_instance_id',
        'question_id',
        'answer_id',
        'user_id'
    ];
}