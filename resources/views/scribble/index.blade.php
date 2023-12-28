@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <form action="/scrabble" method="POST" class="bg-slate-200 py-2 px-4">
            {{ csrf_field() }}
            <div class="flex flex-col">
                <label>Game Title: </label>
                <input type="text" name="name" id="name" value="" placeholder="Game Name">
            </div>
            <div class="flex flex-col">
                <label>Players: </label>
                <input type="text" name="players" id="players" value="" placeholder="Players/Teams">
            </div>
            <div class="py-2">
                <button type="submit" class="bg-lime-600 text-slate-200 px-4 py-2">Create Game</button>
            </div>
        </form>
    </div>
@endsection