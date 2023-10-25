<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class Ratings extends Model
{
    protected $table = 'trv_trivia_ratings';
    protected $fillable = [
        'trivia_id',
        'user_id',
        'rating'
    ];
}