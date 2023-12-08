<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use PartyGames\TriviaGame\Models\QuestionReports;
use PartyGames\TriviaGame\Models\AnswerReports;

class ReportsController
{

    public function index($reportType = 'questions')
    {
        switch ($reportType) {
            case 'questions':
                $reports = QuestionReports::paginate();
                break;
            case 'answers':
                $reports = AnswerReports::paginate();
                break;
            default:
                $reports = [];
                break;
        }

        return view("trivia-game::pages.reports.{$reportType}")->with([
            'reports' => $reports
        ]);
    }

    public function submitReport()
    {

    }


}