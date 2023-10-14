<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Trivia;
class Categories extends Model
{
    protected $table = 'trv_categories';
    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active'
    ];

    public function trivia()
    {
        return $this->hasMany(Trivia::class, 'category_id', 'id');
    }
}