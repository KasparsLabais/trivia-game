<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\User;
use PartyGames\TriviaGame\Models\Trivia;


class OpenTrivias extends Model
{
    protected $table = 'trv_open_trivias';
    protected $fillable = [
        'user_id',
        'trivia_id',
        'game_instance_id',
        'status',
        'closed_at',
        'is_temporary'
    ];

    public function trivia()
    {
        if($this->is_temporary == true) {
            return $this->belongsTo(TmpTrivia::class, 'trivia_id', 'id');
        }
        return $this->belongsTo(Trivia::class, 'trivia_id', 'id');
    }

    public function tmpTrivia()
    {
        return $this->belongsTo(TmpTrivia::class, 'trivia_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class, 'game_instance_id', 'id');
    }
}