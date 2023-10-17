<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Questions;
use PartyGames\GameApi\Models\User;

class Trivia extends Model
{

    protected $table = 'trv_trivia';

    protected $fillable = [
        'title',
        'category_id',
        'difficulty',
        'type',
        'user_id'
    ];

    public function questions()
    {
        return $this->hasMany(Questions::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}