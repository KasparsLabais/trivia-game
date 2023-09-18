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
                        <option value="general">General</option>
                        <option value="books">Books</option>
                        <option value="film">Film</option>
                        <option value="music">Music</option>
                        <option value="musicals">Musicals</option>
                        <option value="television">Television</option>
                        <option value="video games">Video Games</option>
                        <option value="animals">Animals</option>
                        <option value="anime">Anime</option>
                        <option value="cartoons">Cartoons</option>
                        <option value="comics">Comics</option>
                        <option value="gadgets">Gadgets</option>
                        <option value="celebrities">Celebrities</option>
                        <option value="vehicles">Vehicles</option>
                        <option value="places">Places</option>
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
        <div>
            <div>this will be all available trivias</div>
        </div>
    </div>
@endsection