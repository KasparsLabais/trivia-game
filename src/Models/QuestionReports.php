<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Questions;

class QuestionReports extends Model
{
    protected $table = 'question_reports';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'question_id', 'reason', 'status'];

    public function questions()
    {
        return $this->belongsTo(Questions::class, 'question_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}