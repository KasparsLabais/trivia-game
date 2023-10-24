@extends('game-api::layout')
@section('body')
    @include('trivia-game::game.partials.scripts')
    <div class="flex flex-row justify-center px-4 md:px-none">
        <div class="flex flex-col">
            <div class="bg-slate-200 px-6 py-8">
                <h1 class="fira-sans font-semibold text-2xl border-b border-slate-300 my-2">{{ $trivia['title'] }}</h1>
                <p class="fira-sans font-normal mb-4">{{ $trivia['description'] }}</p>
                <div class="flex flex-row justify-between">
                    <div>
                        <span class="raleway font-normal text-base">Category:</span>
                        <span class="raleway font-normal text-base capitalize">{{ $trivia->category->name }}</span>
                    </div>
                    <div>
                        <span class="raleway font-normal text-base">Difficulty:</span>
                        <span class="raleway font-normal text-base capitalize @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600	 @endif">{{ $trivia['difficulty'] }}</span>
                    </div>
                </div>
            </div>
            <div class="bg-slate-100 px-6 py-8">
                <span class="fira-sans font-semibold text-xl my-2">Joined Players:</span>
                <div id="player-holder">
                @foreach($gameInstance['playerInstances'] as $player)
                    @if($player['user_id'] == $gameInstance['user_id'])
                        <div class="flex flex-row py-2">
                            <img src="@if(is_null($player->user->avatar)) /images/default-avatar.jpg @else{{$player->user->avatar}}@endif" class="w-14 h-14 rounded-full shadow-md border-2 border-slate-500" alt="avatar" />
                            <div class="flex flex-col px-2">
                                <div class="raleway">{{ $player->user->username }}</div>
                                <div class="raleway">Host</div>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-row py-2">
                            <img src="@if(is_null($player->user->avatar)) /images/default-avatar.jpg @else{{$player->user->avatar}}@endif" class="w-14 h-14 rounded-full shadow-md border-2 border-slate-500" alt="avatar" />
                            <div class="flex flex-col px-2">
                                <div class="raleway">{{ $player->user->username }}</div>
                                <div class="raleway">(Player)</div>
                            </div>
                        </div>
                    @endif
                @endforeach
                </div>
            </div>
            <div class="bg-slate-200 px-6 py-8">
                <span class="fira-sans font-semibold text-xl my-2">Join:</span>
                <div class="flex flex-row justify-center" id="qrcode"></div>
                <span class="flex flex-row justify-center my-4">OR</span>
                <h2 class="fira-sans flex flex-row justify-center"><span>https://is-a.gay/join/{{ $gameInstance['token'] }}</span></h2>
            </div>
            <div class="bg-slate-300 px-6 py-8">
                @if(Auth::user()->id == $gameInstance['user_id'])
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="startTriviaGame()">Start Game</button>
                @else
                    <h2 class="text-center">Wait for game to start!</h2>
                @endif
            </div>
        </div>
    </div>

    <script src="/vendor/trivia-game/js/qrcode.min.js"></script>
    <script>

        var qrcode = new QRCode("qrcode", {
            text: "https://is-a.gay/join/{{ $gameInstance['token'] }}",
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        GameApi.joinRoom('{{ $gameInstance['token'] }}');

        const startTriviaGame = () => {
            fetch('/trv/start', {'method': 'POST', 'headers': {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'gameToken': '{{ $gameInstance['token'] }}'})})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    //window.location.href = '/trv/trivia/' + data.data.token;
                    GameApi.updateGameInstance('{{ $gameInstance['token'] }}', data.data.gameInstance, 'gameStarted');
                })
                .catch(error => console.log(error));
        }

        //TODO: Convert callbackGameInstanceUpdated to event listener
        document.addEventListener('gameStarted', (e) => {
            console.log(e);
            window.location.href = '/trv/trivia/' + e.detail.gameToken;
        });

        document.addEventListener('playerJoined', (e) => {
            playerJoined(game);
        });

        const callbackGameInstanceUpdated = (gameToken, game, action) => {
            console.log('game instance updated');
            switch (action) {
                case 'playerJoined':
                    //playerJoined(game);
                    break;
                case 'gameStarted':
                    //window.location.href = '/trv/trivia/' + gameToken;
                    break;
            }
        }

    </script>
@endsection