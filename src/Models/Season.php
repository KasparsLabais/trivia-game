<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Models\GameInstances;
use PartyGames\GameApi\Models\PlayerInstances;
use PartyGames\GameApi\Models\TmpUsers;
use PartyGames\GameApi\Models\User;

class Season extends Model
{
    protected $table = 'seasons';

    protected $fillable = [
        'user_id', 'name', 'description', 'prize', 'coin_pot', 'code',
        'status', 'winner_user_id', 'winner_name', 'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function trivias()
    {
        return $this->hasMany(SeasonTrivia::class)->orderBy('order_nr');
    }

    public function games()
    {
        return $this->hasMany(SeasonGame::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /** A short, URL-friendly, unique share code for the public leaderboard. */
    public static function makeCode(): string
    {
        do {
            $code = strtolower(Str::random(8));
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /** Game-instance ids counted toward this season. */
    public function gameInstanceIds(): array
    {
        return $this->games()->pluck('game_instance_id')->all();
    }

    /**
     * Season-long standings: every player's points summed across all the
     * season's games, ranked. Registered players aggregate across games by
     * their account; guests aggregate by their (session) id.
     */
    public function leaderboard(): array
    {
        $ids = $this->gameInstanceIds();
        if (empty($ids)) {
            return [];
        }

        $rows = PlayerInstances::selectRaw('user_id, user_type, SUM(points) as total, COUNT(*) as games_played')
            ->whereIn('game_instance_id', $ids)
            ->groupBy('user_id', 'user_type')
            ->get();

        $playerIds = $rows->where('user_type', 'player')->pluck('user_id')->all();
        $guestIds  = $rows->where('user_type', '!=', 'player')->pluck('user_id')->all();

        $users = User::whereIn('id', $playerIds)->with('border')->get()->keyBy('id');
        $tmps  = TmpUsers::whereIn('tmp_user_id', $guestIds)->get()->keyBy('tmp_user_id');

        $out = [];
        foreach ($rows as $r) {
            if ($r->user_type == 'player') {
                $u = $users->get($r->user_id);
                $username = $u->username ?? 'Player';
                $avatar   = ($u && $u->avatar) ? $u->avatar : '/images/default-avatar.jpg';
                $border   = ($u && $u->border) ? $u->border->color : '';
            } else {
                $t = $tmps->get($r->user_id);
                $username = $t->username ?? 'Guest';
                $avatar   = '/images/default-avatar.jpg';
                $border   = '';
            }

            $out[] = [
                'user_id'      => $r->user_id,
                'username'     => $username,
                'avatar'       => $avatar,
                'border'       => $border,
                'points'       => (int) $r->total,
                'games_played' => (int) $r->games_played,
                'is_guest'     => $r->user_type != 'player',
            ];
        }

        usort($out, fn ($a, $b) => $b['points'] <=> $a['points']);

        return $out;
    }

    /**
     * How the coin pot is divided at the finish: the top three *registered*
     * finishers (guests can't hold coins), each with their podium share. Used
     * both to preview the split while the season runs and to pay it out on
     * completion. Returns [] when there is no pot.
     */
    public function coinPodium(): array
    {
        $shares = GameApi::coinPotShares($this->coin_pot);
        if (empty($shares)) {
            return [];
        }

        $registered = array_values(array_filter($this->leaderboard(), fn ($r) => !$r['is_guest']));

        $out = [];
        foreach ($shares as $i => $amount) {
            if (!isset($registered[$i])) {
                break;
            }
            $out[] = [
                'rank'     => $i + 1,
                'user_id'  => $registered[$i]['user_id'],
                'username' => $registered[$i]['username'],
                'avatar'   => $registered[$i]['avatar'],
                'border'   => $registered[$i]['border'],
                'amount'   => $amount,
            ];
        }

        return $out;
    }

    /**
     * Each game played under the season, with its individual winner
     * (the top-scoring player of that single game).
     */
    public function events(): array
    {
        $out = [];
        foreach ($this->games()->orderBy('id')->get() as $sg) {
            $gi = GameInstances::find($sg->game_instance_id);
            if (!$gi) {
                continue;
            }

            $trivia = $sg->trivia_id ? Trivia::find($sg->trivia_id) : null;

            $top = PlayerInstances::where('game_instance_id', $gi->id)->orderBy('points', 'desc')->first();
            $winnerName = '';
            $winnerPoints = 0;
            if ($top) {
                $winnerPoints = (int) $top->points;
                if ($top->user_type == 'player') {
                    $u = User::find($top->user_id);
                    $winnerName = $u->username ?? 'Player';
                } else {
                    $t = TmpUsers::where('tmp_user_id', $top->user_id)->first();
                    $winnerName = $t->username ?? 'Guest';
                }
            }

            $out[] = [
                'id'            => $sg->id,
                'token'         => $gi->token,
                'title'         => $trivia->title ?? $gi->title,
                'status'        => $gi->status,
                'player_count'  => $gi->playerInstances()->count(),
                'winner_name'   => $winnerPoints > 0 ? $winnerName : '',
                'winner_points' => $winnerPoints,
                'created_at'    => $gi->created_at,
            ];
        }

        return $out;
    }
}
