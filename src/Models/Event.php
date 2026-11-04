<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\PlayerInstances;
use PartyGames\GameApi\Models\User;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'user_id', 'name', 'description', 'trivia_id', 'game_instance_id',
        'accent_color', 'logo_path', 'badge_name', 'coin_pot', 'badge_path',
        'achievement_id', 'status', 'winner_user_id', 'winner_name', 'awarded_at',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function trivia()
    {
        return $this->belongsTo(Trivia::class, 'trivia_id', 'id');
    }

    public function gameInstance()
    {
        return $this->belongsTo(GameInstances::class, 'game_instance_id', 'id');
    }

    /**
     * Registered players of this event's game, ranked — the pool the host
     * awards the winner trophy from (guests can't hold a permanent badge).
     */
    public function candidates(): array
    {
        if (!$this->game_instance_id) {
            return [];
        }

        $rows = PlayerInstances::where('game_instance_id', $this->game_instance_id)
            ->where('user_type', 'player')
            ->orderBy('points', 'desc')
            ->get();

        $userIds = $rows->pluck('user_id')->all();
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');

        $out = [];
        foreach ($rows as $r) {
            $u = $users->get($r->user_id);
            $out[] = [
                'user_id'  => $r->user_id,
                'username' => $u->username ?? 'Player',
                'avatar'   => ($u && $u->avatar) ? $u->avatar : '/images/default-avatar.jpg',
                'points'   => (int) $r->points,
            ];
        }

        return $out;
    }

    /**
     * How the coin pot is divided: the top three scorers of the event's game,
     * each with their podium share (50/30/20). Independent of who the host
     * crowns for the trophy — coins follow the scoreboard. Returns [] when
     * there is no pot.
     */
    public function coinPodium(): array
    {
        $shares = GameApi::coinPotShares($this->coin_pot);
        if (empty($shares)) {
            return [];
        }

        $candidates = $this->candidates();

        $out = [];
        foreach ($shares as $i => $amount) {
            if (!isset($candidates[$i])) {
                break;
            }
            $out[] = [
                'rank'     => $i + 1,
                'user_id'  => $candidates[$i]['user_id'],
                'username' => $candidates[$i]['username'],
                'avatar'   => $candidates[$i]['avatar'],
                'amount'   => $amount,
            ];
        }

        return $out;
    }
}
