@extends('game-api::layout')
@section('body')
    @include('trivia-game::game.partials.scripts')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col">
            <div class="bg-slate-300 px-6 py-8">
                <h1>{{ $gameInstance['title'] }}</h1>
                <h2>Invitation Url: <span>http://trivia.test/join/{{ $gameInstance['token'] }}</span></h2>
            </div>
            <div class="bg-slate-200 px-6 py-8">
                <h2>Players</h2>
                <div id="player-holder">
                @foreach($gameInstance['playerInstances'] as $player)
                    @if($player['user_id'] == $gameInstance['user_id'])
                        <div class="flex flex-row justify-between">
                            <div>{{ $player->user->username }}</div>
                            <div>Host</div>
                        </div>
                    @else
                        <div class="flex flex-row justify-between">
                            <div>{{ $player->user->username }}</div>
                            <div>Player</div>
                        </div>
                    @endif
                @endforeach
                </div>
            </div>
            <div class="bg-slate-300 px-6 py-8">
                @if(Auth::user()->id == $gameInstance['user_id'])
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="startGame()">Start Game</button>
                @else
                    <h2>Wait for game to start!</h2>
                @endif
            </div>
        </div>
    </div>


    <script>
        joinRoom('{{ $gameInstance['token'] }}');
    </script>
@endsection