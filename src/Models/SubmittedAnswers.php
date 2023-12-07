<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Answers;
use PartyGames\TriviaGame\Models\TrvQuestions;
use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\User;

class SubmittedAnswers extends Model
{
    protected $table = 'trv_submitted_answers';
    protected $fillable = [
        'game_instance_id',
        'question_id',
        'answer_id',
        'user_id',
        'answer_custom_input'
    ];

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class, 'game_instance_id', 'id');
    }

    public function question()
    {
        return $this->belongsTo(TrvQuestions::class, 'question_id', 'id');
    }

    public function answer()
    {
        return $this->belongsTo(Answers::class, 'answer_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}