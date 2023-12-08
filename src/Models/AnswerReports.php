<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\TriviaGame\Models\Answers;

class AnswerReports extends Model
{
    protected $table = 'answer_reports';
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'answer_id', 'reason', 'status'];

    public function answers()
    {
        return $this->belongsTo(Answers::class, 'answer_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}