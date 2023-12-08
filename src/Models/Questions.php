<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'question', 'category_id', 'private', 'question_type', 'question_image', 'tags', 'active', 'difficulty'];

    public function answers()
    {
        return $this->hasMany(Answers::class, 'question_id', 'id');
    }


}