<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Tiers;
use PartyGames\TriviaGame\Models\Season;
use PartyGames\TriviaGame\Models\SeasonGame;
use PartyGames\TriviaGame\Models\SeasonTrivia;
use PartyGames\TriviaGame\Models\Trivia;

/**
 * Seasons — a Tier-3 (Pro) host feature. A season groups several of the host's
 * quizzes into a recurring event (e.g. a weekly pub quiz) and keeps a running
 * leaderboard across every game played under it, with a prize for the overall
 * top player. Each game still has its own individual winner.
 */
class SeasonController
{
    /** My Seasons — the host's list, plus their current plan (gates the UI). */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->to('/login?redirect=/' . config('settings.prefix') . '/seasons');
        }

        $seasons = Season::where('user_id', Auth::user()->id)
            ->withCount(['trivias', 'games'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('trivia-game::pages.seasons.index')->with([
            'seasons' => $seasons,
            'plan'    => Tiers::forUser(Auth::user()),
        ]);
    }

    /** Create a new season (Pro only). */
    public function store(Request $request)
    {
        if (!$this->ensurePro()) {
            return redirect()->to('/premium');
        }

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'prize'       => 'nullable|string|max:255',
            'coin_pot'    => 'nullable|integer|min:0|max:1000000',
        ]);

        $season = Season::create([
            'user_id'     => Auth::user()->id,
            'name'        => $request->get('name'),
            'description' => $request->get('description'),
            'prize'       => $request->get('prize'),
            'coin_pot'    => (int) $request->get('coin_pot', 0),
            'code'        => Season::makeCode(),
            'status'      => 'active',
        ]);

        return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
    }

    /** Season detail: line-up manager, games played, live standings. */
    public function show($id)
    {
        $season = $this->ownedSeason($id);
        if (!$season) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons');
        }

        // Quizzes already in the line-up (with their trivia), ordered.
        $lineup = $season->trivias()->with('trivia')->get()
            ->filter(fn ($st) => $st->trivia !== null)
            ->values();
        $lineupTriviaIds = $lineup->pluck('trivia_id')->all();

        // The host's own quizzes that can still be added to the line-up.
        $availableTrivias = Trivia::where('user_id', Auth::user()->id)
            ->whereNotIn('id', $lineupTriviaIds)
            ->orderBy('title')
            ->get();

        return view('trivia-game::pages.seasons.show')->with([
            'season'           => $season,
            'lineup'           => $lineup,
            'availableTrivias' => $availableTrivias,
            'events'           => $season->events(),
            'leaderboard'      => $season->leaderboard(),
            'coinPodium'       => $season->coinPodium(),
            'publicUrl'        => url('/season/' . $season->code),
        ]);
    }

    /** Update the prize / coin pot on a running season. */
    public function setPot($id, Request $request)
    {
        $season = $this->ownedSeason($id);
        if (!$season || $season->status !== 'active') {
            return redirect()->to('/' . config('settings.prefix') . '/seasons/' . ($season->id ?? ''));
        }

        $request->validate([
            'prize'    => 'nullable|string|max:255',
            'coin_pot' => 'nullable|integer|min:0|max:1000000',
        ]);

        $season->prize = $request->get('prize');
        $season->coin_pot = (int) $request->get('coin_pot', 0);
        $season->save();

        return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
    }

    /** Add one of the host's quizzes to the season line-up. */
    public function addTrivia($id, Request $request)
    {
        $season = $this->ownedSeason($id);
        if (!$season) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons');
        }

        $trivia = Trivia::where('id', $request->get('trivia_id'))
            ->where('user_id', Auth::user()->id)
            ->first();

        if ($trivia) {
            $nextOrder = ($season->trivias()->max('order_nr') ?? 0) + 1;
            SeasonTrivia::firstOrCreate(
                ['season_id' => $season->id, 'trivia_id' => $trivia->id],
                ['order_nr' => $nextOrder]
            );
        }

        return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
    }

    /** Remove a quiz from the line-up (does not touch games already played). */
    public function removeTrivia($id, $triviaId)
    {
        $season = $this->ownedSeason($id);
        if (!$season) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons');
        }

        SeasonTrivia::where('season_id', $season->id)->where('trivia_id', $triviaId)->delete();

        return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
    }

    /**
     * Launch a game for one of the line-up quizzes. Creates a normal game
     * instance tagged with the season, so the existing game flow is untouched,
     * and records it so its points roll into the season leaderboard.
     */
    public function launch($id, Request $request)
    {
        $season = $this->ownedSeason($id);
        if (!$season) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons');
        }

        if ($season->status !== 'active') {
            return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
        }

        // The quiz must be part of this season's line-up.
        $inLineup = SeasonTrivia::where('season_id', $season->id)
            ->where('trivia_id', $request->get('trivia_id'))->exists();
        $trivia = Trivia::where('id', $request->get('trivia_id'))->first();

        if (!$inLineup || !$trivia) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
        }

        $remoteData = [
            'trivia_id'    => $trivia->id,
            'is_temporary' => 0,
            'season_id'    => $season->id,
        ];

        $response = GameApi::createGameInstance(config('settings.token'), $trivia->title, $remoteData);
        if ($response['status'] == false) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
        }

        SeasonGame::create([
            'season_id'        => $season->id,
            'game_instance_id' => $response['gameInstance']->id,
            'trivia_id'        => $trivia->id,
        ]);

        // Straight to the game-master control for the new game.
        return redirect()->to('/' . config('settings.prefix') . '/trivia/' . $response['gameInstance']->token);
    }

    /** Close the season and snapshot the overall winner (for the prize). */
    public function complete($id)
    {
        $season = $this->ownedSeason($id);
        if (!$season) {
            return redirect()->to('/' . config('settings.prefix') . '/seasons');
        }

        $leaderboard = $season->leaderboard();
        $winner = $leaderboard[0] ?? null;

        // Pay out the coin pot across the top-three registered finishers
        // (50/30/20), keyed to the season so it can't double-pay.
        foreach ($season->coinPodium() as $slot) {
            GameApi::awardCoins(
                $slot['user_id'],
                $slot['amount'],
                'season_win',
                $season->id,
                'season',
                $season->name . ' — podium #' . $slot['rank']
            );
        }

        $season->status = 'completed';
        $season->completed_at = now();
        if ($winner) {
            $season->winner_user_id = $winner['user_id'];
            $season->winner_name = $winner['username'];
        }
        $season->save();

        return redirect()->to('/' . config('settings.prefix') . '/seasons/' . $season->id);
    }

    /** Public, read-only standings — shareable, guest-accessible (pub screen). */
    public function publicLeaderboard($code)
    {
        $season = Season::where('code', $code)->first();
        if (!$season) {
            return redirect()->to('/');
        }

        return view('trivia-game::pages.seasons.public')->with([
            'season'      => $season,
            'leaderboard' => $season->leaderboard(),
            'events'      => $season->events(),
        ]);
    }

    /* ---- helpers ---- */

    private function ensurePro(): bool
    {
        return Auth::check() && Tiers::allows(Auth::user(), 'seasons');
    }

    /** The season if it exists and belongs to the current user, else null. */
    private function ownedSeason($id): ?Season
    {
        if (!Auth::check()) {
            return null;
        }
        return Season::where('id', $id)->where('user_id', Auth::user()->id)->first();
    }
}
