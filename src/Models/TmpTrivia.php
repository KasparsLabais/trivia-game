<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\GameApi\Models\User;

class TmpTrivia extends Model
{
    protected $table = 'trv_tmp_trivia';
    protected $fillable = ['title', 'description', 'category_id', 'difficulty', 'private', 'question_count'];


    public function questions()
    {
        return $this->hasMany(TmpQuestions::class);
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