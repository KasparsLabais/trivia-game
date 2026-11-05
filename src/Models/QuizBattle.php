<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class QuizBattle extends Model
{
    protected $table = 'trv_quiz_battles';

    protected $fillable = ['code', 'tmp_trivia_id', 'host_user_id', 'status', 'settings', 'time_limit', 'reveal_seconds', 'starts_at'];

    protected $casts = [
        'settings' => 'array',
        'starts_at' => 'datetime',
    ];

    public const MAX_PLAYERS = 8;

    public function players()
    {
        return $this->hasMany(QuizBattlePlayer::class, 'battle_id');
    }

    public function tmpTrivia()
    {
        return $this->belongsTo(TmpTrivia::class, 'tmp_trivia_id');
    }

    // Seconds from one question's start to the next (answering + reveal).
    public function slotSeconds(): int
    {
        return $this->time_limit + $this->reveal_seconds;
    }

    // Start of question $orderNr (1-based) in ms since epoch.
    public function questionStartMs(int $orderNr): int
    {
        return (int) $this->starts_at->getPreciseTimestamp(3) + ($orderNr - 1) * $this->slotSeconds() * 1000;
    }
}
