@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col">
            <div class="bg-slate-300 px-6 py-8">
                <h1 id="question-holder">Waiting For Question...</h1>
            </div>
            <div class="bg-slate-200 px-6 py-8">
                <div class="answer-holder">
                </div>
            </div>
        </div>
    </div>

    <script>
        let triviaID = '{{ $remoteData['trivia_id'] }}';
        let currentQuestion = '{{ $remoteData['current_question'] }}';

        fetch(''

    </script>
@endsection