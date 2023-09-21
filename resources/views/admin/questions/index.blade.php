@extends('game-api::layout')
@section('body')
    <div class="mt-2 px-12 flex flex-row justify-center">
        <div class="bg-slate-300 px-6 py-8">
            <h1>{{ $trivia['title'] }}</h1>
            <form method="POST" action="">
                <input type="hidden" name="trivia_id" value="{{ $trivia['id'] }}">
                <div class="flex flex-col">
                    <label for="question">Question:</label>
                    <input class="border border-slate-400" type="text" name="question" id="question">
                </div>
                <div class="flex mt-4">
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold"  type="submit">Submit</button>
                </div>
            </form>
        </div>
        <div class="bg-slate-200 px-6 py-8">
            <div>Current Questions</div>
            <hr>
            @foreach($questions as $question)
                <div>
                    <h2>{{ $question['id'] }} | {{ $question['question'] }}</h2>
                    <div>
                        @foreach($question->answers as $answer)
                            <div>{{ $answer['answer'] }} | {{ $answer['is_correct'] }}</div>
                        @endforeach
                        <form method="POST" action="/trv/answer">
                            <input type="hidden" name="question_id" value="{{ $question['id'] }}">
                            <div class="flex flex-col">
                                <label for="answer">Answer:</label>
                                <input class="border border-slate-400" type="text" name="answer" id="answer">
                            </div>
                            <div class="flex flex-col">
                                <label for="is_correct">Correct:</label>
                                <select class="border border-slate-400" name="is_correct" id="is_correct">
                                    <option value="0">False</option>
                                    <option value="1">True</option>
                                </select>
                            </div>
                            <div class="flex mt-4">
                                <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold"  type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection