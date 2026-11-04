<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PartyGames\TriviaGame\Models\QuizBattle;
use PartyGames\TriviaGame\Models\QuizBattlePlayer;
use PartyGames\TriviaGame\Models\TmpQuestions;

/**
 * Trivia Battle: a Random Quiz played together by friends, with no game master.
 *
 * Flow: create (lobby) -> friends join via the invite link -> the creator
 * starts it -> everyone plays the same timed questions -> final standings.
 *
 * Sync without a host or socket messages: when the battle starts the server
 * fixes `starts_at`, and question N's window is derived from it
 * (time_limit to answer, then reveal_seconds showing the answer). Each page
 * polls /state for players, lock-ins and reveals, using the server clock.
 * Correct answers are only included for questions whose time is up.
 */
class QuizBattleController
{
    use PicksAutoQuestions;

    private const TIME_LIMIT = 20;
    private const REVEAL_SECONDS = 5;
    private const COUNTDOWN_SECONDS = 5;
    // Answers that arrive just after the clock runs out (network lag) still count.
    private const GRACE_MS = 1500;

    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->to('/login?redirect=/trivia');
        }

        $settings = $this->quizSettings($request);
        $tmpTrivia = $this->buildQuiz($settings, 'Trivia Battle');
        if (!$tmpTrivia) {
            Session::flash('errorMessage', 'No questions found for that category and difficulty — try another combination.');
            return redirect()->to('/trivia');
        }

        $battle = QuizBattle::create([
            'code' => $this->newCode(),
            'tmp_trivia_id' => $tmpTrivia->id,
            'host_user_id' => Auth::id(),
            'status' => 'lobby',
            'settings' => $settings,
            'time_limit' => self::TIME_LIMIT,
            'reveal_seconds' => self::REVEAL_SECONDS,
        ]);
        QuizBattlePlayer::create(['battle_id' => $battle->id, 'user_id' => Auth::id(), 'answers' => []]);

        return redirect()->to('/trivia/battle/' . $battle->code);
    }

    public function show($code)
    {
        if (!Auth::check()) {
            return redirect()->to('/login?redirect=/trivia/battle/' . $code);
        }
        $battle = QuizBattle::where('code', $code)->firstOrFail();
        $battle->load('tmpTrivia');

        return view('trivia-game::game.battle')->with([
            'battle' => $battle,
            'questions' => $this->publicQuestions($battle->tmp_trivia_id),
            'joinUrl' => url('/trivia/battle/' . $battle->code),
        ]);
    }

    public function join($code)
    {
        $battle = QuizBattle::where('code', $code)->firstOrFail();
        if ($this->player($battle)) {
            return new JsonResponse(['success' => true]);
        }
        if ($battle->status !== 'lobby') {
            return new JsonResponse(['success' => false, 'message' => 'This battle has already started.'], 409);
        }
        if ($battle->players()->count() >= QuizBattle::MAX_PLAYERS) {
            return new JsonResponse(['success' => false, 'message' => 'This battle is full.'], 409);
        }
        QuizBattlePlayer::firstOrCreate(['battle_id' => $battle->id, 'user_id' => Auth::id()], ['answers' => []]);

        return new JsonResponse(['success' => true]);
    }

    // Only the creator can start, and only from the lobby.
    public function start($code)
    {
        $battle = QuizBattle::where('code', $code)->firstOrFail();
        if ((int) $battle->host_user_id !== (int) Auth::id()) {
            return new JsonResponse(['success' => false, 'message' => 'Only the battle creator can start it.'], 403);
        }
        if ($battle->status !== 'lobby') {
            return new JsonResponse(['success' => true]);
        }
        if ($battle->players()->count() < 2) {
            return new JsonResponse(['success' => false, 'message' => 'Invite at least one friend before starting.'], 422);
        }
        $battle->update(['status' => 'running', 'starts_at' => now()->addSeconds(self::COUNTDOWN_SECONDS)]);

        return new JsonResponse(['success' => true]);
    }

    public function state($code)
    {
        $battle = QuizBattle::where('code', $code)->firstOrFail();
        $nowMs = (int) floor(microtime(true) * 1000);
        $total = TmpQuestions::where('tmp_trivia_id', $battle->tmp_trivia_id)->count();

        // Which questions have had their reveal (their answering time is over).
        $revealedUpTo = 0;
        if ($battle->status !== 'lobby' && $battle->starts_at) {
            for ($n = 1; $n <= $total; $n++) {
                if ($nowMs >= $battle->questionStartMs($n) + $battle->time_limit * 1000) {
                    $revealedUpTo = $n;
                }
            }
            if ($battle->status === 'running' && $nowMs >= $battle->questionStartMs($total) + $battle->slotSeconds() * 1000) {
                $battle->update(['status' => 'finished']);
            }
        }

        $players = $battle->players()->with('user')->orderBy('id')->get();
        $me = $players->firstWhere('user_id', Auth::id());

        $reveals = [];
        if ($revealedUpTo) {
            $tmpQuestions = TmpQuestions::where('tmp_trivia_id', $battle->tmp_trivia_id)->where('order_nr', '<=', $revealedUpTo)->get();
            foreach ($tmpQuestions as $q) {
                $correct = $this->correctAnswers($q);
                $reveals[$q->order_nr] = [
                    'correct_answer_ids' => $correct->pluck('id')->values(),
                    'correct_answer_text' => $correct->pluck('answer')->implode(' / '),
                    'results' => $players->mapWithKeys(fn ($p) => [$p->user_id => (bool) (($p->answers[$q->order_nr]['correct'] ?? false))]),
                ];
            }
        }

        return new JsonResponse(['success' => true, 'data' => [
            'status' => $battle->status,
            'server_now' => $nowMs,
            'starts_at' => $battle->starts_at ? $battle->questionStartMs(1) : null,
            'time_limit' => $battle->time_limit,
            'reveal_seconds' => $battle->reveal_seconds,
            'total' => $total,
            'host_user_id' => $battle->host_user_id,
            'me' => Auth::id(),
            'is_player' => (bool) $me,
            'players' => $players->map(function ($p) use ($revealedUpTo) {
                $answers = $p->answers ?: [];
                return [
                    'user_id' => $p->user_id,
                    'username' => optional($p->user)->username ?? 'Player',
                    'avatar' => optional($p->user)->avatar ?: '/images/default-avatar.jpg',
                    // Score only counts revealed questions, so it can't give answers away early.
                    'score' => collect($answers)->filter(fn ($a, $n) => $n <= $revealedUpTo && !empty($a['correct']))->count(),
                    'answered' => array_map('intval', array_keys($answers)),
                ];
            })->values(),
            'my_answers' => $me ? collect($me->answers ?: [])->map(fn ($a) => $a['given'])->all() : [],
            'reveals' => (object) $reveals,
        ]]);
    }

    // Lock in an answer for the question that's open right now. First answer counts.
    public function answer($code, Request $request)
    {
        $battle = QuizBattle::where('code', $code)->firstOrFail();
        $player = $this->player($battle);
        if (!$player || $battle->status !== 'running') {
            return new JsonResponse(['success' => false, 'message' => 'Not playing in this battle.'], 403);
        }

        $orderNr = (int) $request->get('order_nr');
        $nowMs = (int) floor(microtime(true) * 1000);
        $opens = $battle->questionStartMs($orderNr);
        if ($nowMs < $opens || $nowMs > $opens + $battle->time_limit * 1000 + self::GRACE_MS) {
            return new JsonResponse(['success' => false, 'message' => 'This question is closed.'], 422);
        }

        $tmpQuestion = TmpQuestions::where('tmp_trivia_id', $battle->tmp_trivia_id)->where('order_nr', $orderNr)->first();
        if (!$tmpQuestion) {
            return new JsonResponse(['success' => false, 'message' => 'Question not found.'], 404);
        }

        [$given, $isCorrect] = $this->grade($tmpQuestion, $this->correctAnswers($tmpQuestion), $request->get('answer_id'), $request->get('answer_text'));
        if ($given === null) {
            return new JsonResponse(['success' => false, 'message' => 'Empty answer.'], 422);
        }

        // Row lock so a double click can't record two answers.
        DB::transaction(function () use ($player, $orderNr, $given, $isCorrect) {
            $fresh = QuizBattlePlayer::lockForUpdate()->find($player->id);
            $answers = $fresh->answers ?: [];
            if (!isset($answers[$orderNr])) {
                $answers[$orderNr] = ['given' => $given, 'correct' => $isCorrect];
                $fresh->update(['answers' => $answers]);
            }
        });

        // Correctness is only revealed (via /state) when the time is up.
        return new JsonResponse(['success' => true, 'data' => ['locked' => true]]);
    }

    private function player(QuizBattle $battle): ?QuizBattlePlayer
    {
        return Auth::check() ? $battle->players()->where('user_id', Auth::id())->first() : null;
    }

    private function newCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (QuizBattle::where('code', $code)->exists());
        return $code;
    }
}
