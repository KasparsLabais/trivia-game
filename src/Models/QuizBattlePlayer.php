<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\GameApi\Models\User;

class QuizBattlePlayer extends Model
{
    protected $table = 'trv_quiz_battle_players';

    protected $fillable = ['battle_id', 'user_id', 'answers'];

    protected $casts = [
        'answers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
