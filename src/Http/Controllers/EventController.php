<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PartyGames\GameApi\GameApi;
use PartyGames\GameApi\Tiers;
use PartyGames\TriviaGame\Models\Event;
use PartyGames\TriviaGame\Models\Trivia;

/**
 * Events — a Tier-3 (Pro) host feature. An event is a branded one-off game
 * with a custom winner trophy: a customisable display (name, logo, accent
 * colour) and an uploadable badge that the winner keeps permanently on their
 * account (minted through the shared achievements system, kind = 'event').
 */
class EventController
{
    /** My Events — the host's list, plus their current plan (gates the UI). */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->to('/login?redirect=/' . config('settings.prefix') . '/events');
        }

        $events = Event::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();

        return view('trivia-game::pages.events.index')->with([
            'events' => $events,
            'plan'   => Tiers::forUser(Auth::user()),
            'trivias' => Trivia::where('user_id', Auth::user()->id)->orderBy('title')->get(),
        ]);
    }

    /** Create a branded event (Pro only). */
    public function store(Request $request)
    {
        if (!$this->ensurePro()) {
            return redirect()->to('/premium');
        }

        $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'trivia_id'    => 'nullable|integer',
            'accent_color' => 'nullable|string|max:9',
            'badge_name'   => 'nullable|string|max:255',
            'coin_pot'     => 'nullable|integer|min:0|max:1000000',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
            'badge'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
        ]);

        // Only the host's own quizzes may back an event.
        $triviaId = null;
        if ($request->get('trivia_id')) {
            $trivia = Trivia::where('id', $request->get('trivia_id'))->where('user_id', Auth::user()->id)->first();
            $triviaId = $trivia?->id;
        }

        $event = Event::create([
            'user_id'      => Auth::user()->id,
            'name'         => $request->get('name'),
            'description'  => $request->get('description'),
            'trivia_id'    => $triviaId,
            'accent_color' => $request->get('accent_color') ?: '#E62727',
            'badge_name'   => $request->get('badge_name'),
            'coin_pot'     => (int) $request->get('coin_pot', 0),
            'status'       => 'draft',
        ]);

        if ($request->hasFile('logo')) {
            $event->logo_path = $this->storeAsset($request->file('logo'), $event->id, 'logo');
        }
        if ($request->hasFile('badge')) {
            $event->badge_path = $this->storeAsset($request->file('badge'), $event->id, 'badge');
        }
        $event->save();

        return redirect()->to('/' . config('settings.prefix') . '/events/' . $event->id);
    }

    /** Event detail: branding preview, launch, and the award-winner panel. */
    public function show($id)
    {
        $event = $this->ownedEvent($id);
        if (!$event) {
            return redirect()->to('/' . config('settings.prefix') . '/events');
        }

        return view('trivia-game::pages.events.show')->with([
            'event'      => $event,
            'trivias'    => Trivia::where('user_id', Auth::user()->id)->orderBy('title')->get(),
            'candidates' => $event->candidates(),
            'coinPodium' => $event->coinPodium(),
        ]);
    }

    /** Edit branding / badge before the event is completed. */
    public function update($id, Request $request)
    {
        $event = $this->ownedEvent($id);
        if (!$event || $event->status === 'completed') {
            return redirect()->to('/' . config('settings.prefix') . '/events/' . ($event->id ?? ''));
        }

        $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string|max:1000',
            'trivia_id'    => 'nullable|integer',
            'accent_color' => 'nullable|string|max:9',
            'badge_name'   => 'nullable|string|max:255',
            'coin_pot'     => 'nullable|integer|min:0|max:1000000',
            'logo'         => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
            'badge'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:8192',
        ]);

        $event->name = $request->get('name');
        $event->description = $request->get('description');
        $event->accent_color = $request->get('accent_color') ?: '#E62727';
        $event->badge_name = $request->get('badge_name');
        $event->coin_pot = (int) $request->get('coin_pot', 0);

        // The quiz can only change before the game is launched.
        if (!$event->game_instance_id && $request->get('trivia_id')) {
            $trivia = Trivia::where('id', $request->get('trivia_id'))->where('user_id', Auth::user()->id)->first();
            $event->trivia_id = $trivia?->id;
        }

        if ($request->hasFile('logo')) {
            $event->logo_path = $this->storeAsset($request->file('logo'), $event->id, 'logo');
        }
        if ($request->hasFile('badge')) {
            $event->badge_path = $this->storeAsset($request->file('badge'), $event->id, 'badge');
        }
        $event->save();

        return redirect()->to('/' . config('settings.prefix') . '/events/' . $event->id);
    }

    /** Launch the branded game, tagged with the event. */
    public function launch($id)
    {
        $event = $this->ownedEvent($id);
        if (!$event) {
            return redirect()->to('/' . config('settings.prefix') . '/events');
        }

        $trivia = $event->trivia_id ? Trivia::find($event->trivia_id) : null;
        if (!$trivia || $event->game_instance_id) {
            return redirect()->to('/' . config('settings.prefix') . '/events/' . $event->id);
        }

        $remoteData = [
            'trivia_id'    => $trivia->id,
            'is_temporary' => 0,
            'event_id'     => $event->id,
        ];

        $response = GameApi::createGameInstance(config('settings.token'), $event->name, $remoteData);
        if ($response['status'] == false) {
            return redirect()->to('/' . config('settings.prefix') . '/events/' . $event->id);
        }

        $event->game_instance_id = $response['gameInstance']->id;
        $event->status = 'live';
        $event->save();

        return redirect()->to('/' . config('settings.prefix') . '/trivia/' . $response['gameInstance']->token);
    }

    /**
     * Award the winner trophy. Records the event winner and, if a badge image
     * was uploaded, mints it as a permanent account badge for the chosen
     * player via the shared achievements system (kind = 'event').
     */
    public function award($id, Request $request)
    {
        $event = $this->ownedEvent($id);
        if (!$event || !$event->game_instance_id) {
            return redirect()->to('/' . config('settings.prefix') . '/events/' . ($event->id ?? ''));
        }

        $winnerId = $request->get('winner_user_id');

        // The winner must be a registered player of this event's game.
        $candidate = collect($event->candidates())->firstWhere('user_id', $winnerId);
        if (!$candidate) {
            return redirect()->to('/' . config('settings.prefix') . '/events/' . $event->id);
        }

        // Mint / reuse the permanent badge and award it to the winner.
        if ($event->badge_path) {
            if (!$event->achievement_id) {
                $achievement = \App\Models\Achievement::create([
                    'name'        => $event->badge_name ?: ($event->name . ' Champion'),
                    'description' => 'Winner of ' . $event->name,
                    'kind'        => 'event',
                    'metric'      => '',   // not metric-based — evaluate() skips it
                    'threshold'   => 0,
                    'color'       => 'pop',
                    'icon'        => $event->badge_path,
                    'is_active'   => 1,
                ]);
                $event->achievement_id = $achievement->id;
            }

            $winner = \App\Models\User::find($winnerId);
            if ($winner && !$winner->achievements()->where('achievements.id', $event->achievement_id)->exists()) {
                $winner->achievements()->attach($event->achievement_id, ['earned_at' => now()]);
            }
        }

        // Pay out the coin pot across the top-three scorers (50/30/20). This is
        // keyed to the event so it can't double-pay; coins go to the scoreboard
        // leaders, independent of who the host crowns for the trophy.
        foreach ($event->coinPodium() as $slot) {
            GameApi::awardCoins(
                $slot['user_id'],
                $slot['amount'],
                'event_win',
                $event->id,
                'event',
                $event->name . ' — podium #' . $slot['rank']
            );
        }

        $event->winner_user_id = $winnerId;
        $event->winner_name = $candidate['username'];
        $event->awarded_at = now();
        $event->status = 'completed';
        $event->save();

        return redirect()->to('/' . config('settings.prefix') . '/events/' . $event->id);
    }

    /* ---- helpers ---- */

    private function ensurePro(): bool
    {
        return Auth::check() && Tiers::allows(Auth::user(), 'events');
    }

    private function ownedEvent($id): ?Event
    {
        if (!Auth::check()) {
            return null;
        }
        return Event::where('id', $id)->where('user_id', Auth::user()->id)->first();
    }

    /** Move an uploaded image into the event's permanent asset folder. */
    private function storeAsset($file, int $eventId, string $kind): string
    {
        $dir = public_path('event-assets/' . $eventId);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $name = $kind . '-' . Str::random(8) . '.' . $ext;
        $file->move($dir, $name);

        return '/event-assets/' . $eventId . '/' . $name;
    }
}
