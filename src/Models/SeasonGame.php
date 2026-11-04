<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\GameApi\Models\GameInstances;

class SeasonGame extends Model
{
    protected $table = 'season_games';

    protected $fillable = ['season_id', 'game_instance_id', 'trivia_id'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class, 'game_instance_id', 'id');
    }
}
