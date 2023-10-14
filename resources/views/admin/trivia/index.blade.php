@extends('game-api::layout')
@section('body')
    <div class="mt-2 px-12 flex flex-row justify-center">
        <div class="bg-slate-300 px-6 py-8">
            <form method="POST" action="">
                <div class="flex flex-col">
                    <label for="title">Trivia Title:</label>
                    <input class="border border-slate-400" type="text" name="title" id="title">
                </div>
                <div class="flex flex-col">
                    <label for="category">Category:</label>
                    <select class="border border-slate-400" name="category" id="category">
                        @foreach($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="difficulty">Difficulty:</label>
                    <select class="border border-slate-400" name="difficulty" id="difficulty">
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="type">Type:</label>
                    <select class="border border-slate-400" name="type" id="type">
                        <option value="multiple">Multiple Choice</option>
                        <option value="boolean">True / False</option>
                    </select>
                </div>
                <div class="flex mt-4">
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" type="submit">Submit</button>
                </div>
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
                        <td>Play</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allTrivia as $trivia)
                        <tr>
                            <td>{{ $trivia['title'] }}</td>
                            <td>{{ $trivia['category'] }}</td>
                            <td>{{ $trivia['difficulty'] }}</td>
                            <td class="text-center">{{ $trivia['type'] }}</td>
                            <td class="text-center"><a href="/trv/question?trivia_id={{ $trivia['id'] }}">Edit</a></td>
                            <td class="text-center"><a href="/trv/trivia/{{ $trivia['id'] }}">Play</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection