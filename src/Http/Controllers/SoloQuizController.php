<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
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
    use PicksAutoQuestions;

    // Seconds per question.
    private const TIME_LIMIT = 20;

    public function create(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->to('/login?redirect=/trivia');
        }

        $settings = $this->quizSettings($request);
        $tmpTrivia = $this->buildQuiz($settings, 'Random Quiz');
        if (!$tmpTrivia) {
            Session::flash('errorMessage', 'No questions found for that category and difficulty — try another combination.');
            return redirect()->to('/trivia');
        }

        // Keep only the last few quizzes in the session (older ones can't be resumed anyway).
        Session::put('solo_quiz', array_slice(Session::get('solo_quiz', []), -4, null, true));
        Session::put('solo_quiz.' . $tmpTrivia->id, [
            'user_id' => Auth::id(),
            'settings' => $settings,
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
        $questions = $this->publicQuestions($id);

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

        $correct = $this->correctAnswers($tmpQuestion);

        if (!isset($state['answers'][$orderNr])) {
            [$given, $isCorrect] = $this->grade($tmpQuestion, $correct, $request->get('answer_id'), $request->get('answer_text'));
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
