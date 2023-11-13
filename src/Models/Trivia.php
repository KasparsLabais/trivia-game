<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Questions;
use PartyGames\GameApi\Models\User;
use PartyGames\TriviaGame\Models\Ratings;

class Trivia extends Model
{

    protected $table = 'trv_trivia';

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'difficulty',
        'type',
        'user_id',
        'private',
        'is_active'
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


    //add function to get average rating and count of users given rating to trivia
    public function getRating()
    {
        $rating = $this->hasMany(Ratings::class, 'trivia_id', 'id')->avg('rating');
        $count = $this->hasMany(Ratings::class, 'trivia_id', 'id')->count();
        return [
            'rating' => round($rating, 0),
            'count' => $count
        ];
    }
}