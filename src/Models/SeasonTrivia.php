<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class SeasonTrivia extends Model
{
    protected $table = 'season_trivias';

    protected $fillable = ['season_id', 'trivia_id', 'order_nr'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function trivia()
    {
        return $this->belongsTo(Trivia::class, 'trivia_id', 'id');
    }
}
