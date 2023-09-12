<?php

namespace Partygames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use Partygames\TriviaGame\Models\Questions;
class Trivia extends Model
{

    protected $table = 'trv_trivia';

    protected $fillable = [
        'title',
        'category',
        'difficulty',
        'type',
    ];

    public function questions()
    {
        return $this->hasMany(Questions::class);
    }

}