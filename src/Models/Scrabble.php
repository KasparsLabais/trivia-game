<?php


namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class Scrabble extends Model {
    protected $table = 'scrabble';

    protected $fillable = [
        'name', 'token'
    ];

    public function players()
    {
        return $this->hasMany(ScrabblePlayers::class, 'game_id', 'id');
    }
}