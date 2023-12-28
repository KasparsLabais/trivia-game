@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col justify-center">
        @foreach($players as $player)
            <div class="flex flex-row py-4">
                <div class="flex flex-row justify-center w-2/6">
                    <p class="text-yellow-500 text-6xl">{{ $player->name }}</p>
                    <p class="text-slate-200 text-8xl px-4">{{ $player->points }}</p>
                </div>
                <div class="flex flex-row w-4/6">

                    <form action="/scrabble/{{ $game->token }}" method="POST" class="flex flex-col px-4 py-2">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="player_id" id="player_id" value="{{ $player->id }}">
                        <input type="number" name="points" id="points" class="px-2 py-2 text-2xl">
                        <button type="submit" class="text-slate-200 bg-lime-600 px-2 py-4 font-bold text-lg">Add</button>
                    </form>


                    <form action="/scrabble/{{ $game->token }}" method="POST" class="flex flex-col px-4 py-2">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="player_id" id="player_id" value="{{ $player->id }}">
                        <input type="number" name="points" id="points" class="px-2 py-2 text-2xl">
                        <button type="submit" class="text-slate-200 bg-rose-600 px-2 py-4 font-bold text-lg">Delete</button>
                    </form>

                </div>
            </div>
        @endforeach
        </div>
    </div>
@endsection