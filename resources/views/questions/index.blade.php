@extends('game-api::layout')
@section('body')
    <div class="mt-2 px-12 flex flex-row justify-center">
        <div class="bg-slate-300 px-6 py-8">
            <h1>{{ $trivia['title'] }}</h1>
            <form method="POST" action="">
            </form>
        </div>
        <div class="bg-slate-200 px-6 py-8">
            <div>Available Trivia's</div>
            <table>
                <thead>
                <tr>
                    <td class="py-2 px-2">Title</td>
                    <td class="py-2 px-2">Category</td>
                    <td class="py-2 px-2">Difficulty</td>
                    <td class="py-2 px-2">Type</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                @foreach($questions as $question)
                    <tr>
                        <td>{{ $question['id'] }}</td>
                        <td>{{ $question['question'] }}</td>
                        <td class="text-center"><a href="/answers/{{ $trivia['id'] }}">Edit</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection