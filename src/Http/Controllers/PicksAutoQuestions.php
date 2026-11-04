<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PartyGames\GameApi\Tiers;
use PartyGames\TriviaGame\Models\Answers;
use PartyGames\TriviaGame\Models\Categories;
use PartyGames\TriviaGame\Models\Questions;
use PartyGames\TriviaGame\Models\TmpQuestions;
use PartyGames\TriviaGame\Models\TmpTrivia;

/**
 * Shared by the host-less quiz modes (Random Quiz solo and Trivia Battles):
 * picking bank questions that can be graded automatically, and grading them.
 */
trait PicksAutoQuestions
{
    // Category / difficulty / length from the Random Quiz modal, sanitised.
    // Category 23 = any category (same convention as the hosted random trivia).
    private function quizSettings(Request $request): array
    {
        $count = max(1, min(50, (int) ($request->get('question_count') ?: 10)));
        // Respect the plan's per-quiz question cap.
        $maxQuestions = Tiers::limit(Auth::user(), 'max_questions');
        if ($maxQuestions !== null && $count > $maxQuestions) {
            $count = $maxQuestions;
        }
        return [
            'category' => (int) $request->get('category', 23),
            'difficulty' => in_array($request->get('difficulty'), ['easy', 'medium', 'hard']) ? $request->get('difficulty') : 'any',
            'question_count' => $count,
        ];
    }

    // Pick questions and store them as a temporary trivia. Null when nothing matched.
    private function buildQuiz(array $settings, string $label): ?TmpTrivia
    {
        $questions = $this->pickQuestions($settings['category'], $settings['difficulty'], $settings['question_count']);
        if (!count($questions)) {
            return null;
        }

        $categoryName = ($settings['category'] == 23) ? 'Random' : (Categories::where('id', $settings['category'])->value('name') ?? 'Random');
        $tmpTrivia = TmpTrivia::create([
            'title' => "{$categoryName} {$label}",
            'description' => '',
            'category_id' => $settings['category'],
            'difficulty' => $settings['difficulty'],
            'question_count' => count($questions),
        ]);

        foreach ($questions as $i => $question) {
            TmpQuestions::create([
                'question' => $question->question,
                'order_nr' => $i + 1,
                'original_question_id' => $question->id,
                'tmp_trivia_id' => $tmpTrivia->id,
                'question_type' => $question->question_type,
            ]);
        }

        return $tmpTrivia;
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
        })->whereIn('question_type', ['options', 'text_input'] /* open_input needs a judge */)->get()->shuffle();

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

    // Correct answers for a quiz question. Typed-answer questions may store their
    // single answer without a flag.
    private function correctAnswers(TmpQuestions $tmpQuestion)
    {
        $answers = Answers::where('question_id', $tmpQuestion->original_question_id)->get();
        $correct = $answers->filter(fn ($a) => $this->isCorrect($a))->values();
        if ($correct->isEmpty() && $tmpQuestion->question_type == 'text_input') {
            $correct = $answers->take(1);
        }
        return $correct;
    }

    // Grade a submission: [given, isCorrect]. Timeouts (nothing sent) are wrong.
    private function grade(TmpQuestions $tmpQuestion, $correct, $answerId, $answerText): array
    {
        if ($tmpQuestion->question_type == 'options' && $answerId !== null && $answerId !== '') {
            $given = (int) $answerId;
            return [$given, $correct->contains(fn ($a) => (int) $a->id === $given)];
        }
        if ($tmpQuestion->question_type == 'text_input' && trim((string) $answerText) !== '') {
            $given = trim((string) $answerText);
            return [$given, $correct->contains(fn ($a) => mb_strtolower(trim($a->answer)) === mb_strtolower($given))];
        }
        return [null, false];
    }

    // Questions + shuffled options for the page — never which one is correct.
    private function publicQuestions($tmpTriviaId)
    {
        $tmpQuestions = TmpQuestions::where('tmp_trivia_id', $tmpTriviaId)->orderBy('order_nr')->get();
        $answers = Answers::whereIn('question_id', $tmpQuestions->pluck('original_question_id'))->get()->groupBy('question_id');

        return $tmpQuestions->map(function ($q) use ($answers) {
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
    }
}
