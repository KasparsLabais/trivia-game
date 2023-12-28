<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class ScrabblePlayers extends Model
{

    protected $table = 'scrabble_players';
    protected $fillable = [
        'name', 'game_id', 'points'
    ];

}