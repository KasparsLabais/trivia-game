<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PartyGames\GameApi\Tiers;
use PartyGames\TriviaGame\Models\Answers;
use PartyGames\TriviaGame\Models\Categories;
use PartyGames\TriviaGame\Models\Questions;
use PartyGames\TriviaGame\Models\TmpQuestions;
use PartyGames\TriviaGame\Models\TmpTrivia;

/**
 * Random Quiz: a host-less quiz that plays itself ("auto mode"). Questions are
 * picked from the question bank; each one is timed, the correct answer is
 * revealed when the time runs out, and the final score is shown at the end.
 *
 * Correct answers never reach the browser up front: the page gets questions +
 * options only, and each answer is checked here. Progress lives in the
 * session under "solo_quiz.{tmpTriviaId}".
 */
class SoloQuizController
{
    // Question types that can be graded without a game master (open_input is judged by the host).
    private const AUTO_TYPES = ['options', 'text_input'];

    // Seconds per question.
    private const TIME_LIMIT = 20;

    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->to('/login?redirect=/trivia');
        }

        $category = $request->get('category', 23); // 23 = any category (same convention as the random trivia)
        $difficulty = in_array($request->get('difficulty'), ['easy', 'medium', 'hard']) ? $request->get('difficulty') : 'any';
        $count = (int) ($request->get('question_count') ?: 10);
        $count = max(1, min(50, $count));
        // Respect the plan's per-quiz question cap.
        $maxQuestions = Tiers::limit(Auth::user(), 'max_questions');
        if ($maxQuestions !== null && $count > $maxQuestions) {
            $count = $maxQuestions;
        }

        $questions = $this->pickQuestions($category, $difficulty, $count);
        if (!count($questions)) {
            Session::flash('errorMessage', 'No questions found for that category and difficulty — try another combination.');
            return redirect()->to('/trivia');
        }

        $categoryName = ($category == 23) ? 'Random' : (Categories::where('id', $category)->value('name') ?? 'Random');
        $tmpTrivia = TmpTrivia::create([
            'title' => "{$categoryName} Random Quiz",
            'description' => '',
            'category_id' => $category,
            'difficulty' => $difficulty,
            'question_count' => count($questions),
        ]);

        foreach (array_values($questions) as $i => $question) {
            TmpQuestions::create([
                'question' => $question->question,
                'order_nr' => $i + 1,
                'original_question_id' => $question->id,
                'tmp_trivia_id' => $tmpTrivia->id,
                'question_type' => $question->question_type,
            ]);
        }

        // Keep only the last few quizzes in the session (older ones can't be resumed anyway).
        Session::put('solo_quiz', array_slice(Session::get('solo_quiz', []), -4, null, true));
        Session::put('solo_quiz.' . $tmpTrivia->id, [
            'user_id' => Auth::id(),
            'settings' => ['category' => $category, 'difficulty' => $difficulty, 'question_count' => $count],
            'answers' => [],
        ]);

        return redirect()->to('/trivia/solo/' . $tmpTrivia->id);
    }

    public function show($id)
    {
        $state = $this->state($id);
        if (!$state) {
            return redirect()->to('/trivia');
        }

        $tmpTrivia = TmpTrivia::find($id);
        $tmpQuestions = TmpQuestions::where('tmp_trivia_id', $id)->orderBy('order_nr')->get();
        $answers = Answers::whereIn('question_id', $tmpQuestions->pluck('original_question_id'))->get()->groupBy('question_id');

        // Questions + options only — no correctness flags.
        $questions = $tmpQuestions->map(function ($q) use ($answers) {
            $options = [];
            if ($q->question_type == 'options') {
                $options = $answers->get($q->original_question_id, collect())->shuffle()->map(function ($a) {
                    return ['id' => $a->id, 'answer' => $a->answer];
                })->values();
            }
            return [
                'order_nr' => $q->order_nr,
                'question' => $q->question,
                'question_type' => $q->question_type,
                'answers' => $options,
            ];
        })->values();

        return view('trivia-game::game.solo')->with([
            'tmpTrivia' => $tmpTrivia,
            'questions' => $questions,
            'settings' => $state['settings'],
            'timeLimit' => self::TIME_LIMIT,
        ]);
    }

    // Check one answer (or a timeout, when neither answer_id nor answer_text is sent).
    // Only the first submission per question counts.
    public function answer($id, Request $request)
    {
        $state = $this->state($id);
        if (!$state) {
            return new JsonResponse(['success' => false, 'message' => 'Quiz not found.'], 404);
        }

        $orderNr = (int) $request->get('order_nr');
        $tmpQuestion = TmpQuestions::where('tmp_trivia_id', $id)->where('order_nr', $orderNr)->first();
        if (!$tmpQuestion) {
            return new JsonResponse(['success' => false, 'message' => 'Question not found.'], 404);
        }

        $answers = Answers::where('question_id', $tmpQuestion->original_question_id)->get();
        $correct = $answers->filter(fn ($a) => $this->isCorrect($a))->values();
        // Typed-answer questions may store their single answer without a flag.
        if ($correct->isEmpty() && $tmpQuestion->question_type == 'text_input') {
            $correct = $answers->take(1);
        }

        if (!isset($state['answers'][$orderNr])) {
            $isCorrect = false;
            $given = null;
            if ($tmpQuestion->question_type == 'options' && $request->filled('answer_id')) {
                $given = (int) $request->get('answer_id');
                $isCorrect = $correct->contains(fn ($a) => (int) $a->id === $given);
            } elseif ($tmpQuestion->question_type == 'text_input' && trim((string) $request->get('answer_text')) !== '') {
                $given = trim((string) $request->get('answer_text'));
                $isCorrect = $correct->contains(fn ($a) => mb_strtolower(trim($a->answer)) === mb_strtolower($given));
            }
            $state['answers'][$orderNr] = ['given' => $given, 'correct' => $isCorrect];
            Session::put('solo_quiz.' . $id, $state);
        }

        $score = collect($state['answers'])->where('correct', true)->count();

        return new JsonResponse([
            'success' => true,
            'data' => [
                'correct' => $state['answers'][$orderNr]['correct'],
                'correct_answer_ids' => $correct->pluck('id')->values(),
                'correct_answer_text' => $correct->pluck('answer')->implode(' / '),
                'score' => $score,
                'answered' => count($state['answers']),
            ],
        ]);
    }

    /**
     * Up to $count random bank questions for a category/difficulty that can be
     * auto-graded: an allowed type, and (for multiple choice) at least one
     * answer marked correct.
     */
    private function pickQuestions($category, $difficulty, $count): array
    {
        $candidates = Questions::where(function ($q) use ($category) {
            if ($category != 23) {
                $q->where('category_id', $category);
            }
        })->where(function ($q) use ($difficulty) {
            if ($difficulty != 'any') {
                $q->where('difficulty', $difficulty);
            }
        })->where(function ($q) {
            $q->where('private', 0)->orWhere('user_id', Auth::id());
        })->where(function ($q) {
            $q->where('is_premium', 0)->orWhere('user_id', Auth::id());
        })->whereIn('question_type', self::AUTO_TYPES)->get()->shuffle();

        $answers = Answers::whereIn('question_id', $candidates->pluck('id'))->get()->groupBy('question_id');

        return $candidates->filter(function ($question) use ($answers) {
            $own = $answers->get($question->id, collect());
            if ($question->question_type == 'options') {
                return $own->count() >= 2 && $own->contains(fn ($a) => $this->isCorrect($a));
            }
            return $own->isNotEmpty();
        })->take($count)->values()->all();
    }

    // The bank's flag column is "is_correct" in the app code but "correct" in the
    // original migration; accept either.
    private function isCorrect($answer): bool
    {
        return (bool) ($answer->getAttribute('is_correct') ?? $answer->getAttribute('correct'));
    }

    // This browser's state for a quiz, only if it belongs to the logged-in user.
    private function state($id): ?array
    {
        $state = Session::get('solo_quiz.' . (int) $id);
        if (!$state || !Auth::check() || $state['user_id'] != Auth::id()) {
            return null;
        }
        return $state;
    }
}
