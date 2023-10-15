<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Questions;
class Trivia extends Model
{

    protected $table = 'trv_trivia';

    protected $fillable = [
        'title',
        'category_id',
        'difficulty',
        'type',
    ];

    public function questions()
    {
        return $this->hasMany(Questions::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }

}