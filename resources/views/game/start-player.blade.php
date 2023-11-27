@extends('game-api::layout')
@section('body')
    @include('trivia-game::game.partials.scripts')

    <div class="py-4">
        <x-section title="">
            <div class="flex flex-col relative w-full z-20">
                <h1 class="px-4 z-20 text-center josefin-sans text-yellow-400 font-semibold text-6xl">{{ $trivia['title'] }}</h1>
                <p class="px-4 z-20 text-slate-400 text-center">(Hosted By  {{ $gameInstance->user->username }})</p>
                <div class="z-20 flex flex-row justify-between px-6 py-2 text-slate-200">
                    <div>
                        <span class="raleway font-normal text-base">Category:</span>
                        <span class="raleway font-normal text-base capitalize">{{ $trivia->category->name }}</span>
                    </div>
                    <div>
                        <span class="raleway font-normal text-base">Difficulty:</span>
                        <span class="raleway font-semibold text-base capitalize @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600	 @endif">{{ $trivia['difficulty'] }}</span>
                    </div>
                </div>
                <p class="z-20 text-slate-400 text-center">{{ $trivia['description'] }}</p>
                <div class="w-full flex flex-row bg-zinc-800 shadow-md h-24 skew-y-3 absolute -bottom-10 z-10">
                </div>
            </div>

            <div class="bg-zinc-700 pt-16 pb-6 relative z-10">
                <x-card title="" addHeader="{{ false }}">
                    <div class="z-20 flex flex-col justify-around px-6">
                        <!-- TODO: Add animation -->
                        <p class="text-slate-200 font-semibold text-4xl text-center">Waiting <span class="text-yellow-500">{{ $gameInstance->user->username }}</span> to start the game!</p>
                    </div>
                </x-card>

                <div class="w-full flex flex-row bg-zinc-700 shadow-md h-24 skew-y-3 absolute -bottom-4 z-10">
                </div>
            </div>

            <div class="bg-zinc-800 pt-16 pb-12 relative z-0">
                <h2 class="px-4 z-20 text-center josefin-sans text-yellow-400 font-semibold text-4xl">Joined Players:</h2>
                <div id="player-holder" class="flex flex-row">
                    @foreach($gameInstance['playerInstances'] as $player)
                        <div class="flex flex-row py-2 px-2 w-1/3">
                            <div class="flex flex-col bg-zinc-700 rounded-md shadow-zinc-700">
                                <div style="background-image: url('@if($player->user_type == 'guest' || is_null($player->user->avatar)) /images/default-avatar.jpg @else{{$player->user->avatar}}@endif')" class="flex flex-row w-full justify-center relative bg-gray-600 h-16 bg-cover	bg-center bg-no-repeat">
                                </div>
                                <div class="flex flex-col px-2 py-2">
                                    <div class="raleway text-slate-200 font-bold">@if($player->user_type == 'guest') {{  $player->tmpUser->username }}  @else {{  $player->user->username }} @endif</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-zinc-800 pt-16 pb-6 relative z-20">
                <h2 class="px-4 z-20 text-center josefin-sans text-yellow-400 font-semibold text-4xl z-20 relative">Ways to join:</h2>
                <div class="text-slate-200 font-semibold text-2xl px-6 py-2">
                    PIN: <span class="josefin-sans text-yellow-500 font-bold">{{ $gameInstance['pin'] }}</span>
                </div>
                <div class="text-slate-200 font-semibold text-2xl px-6" style="overflow-wrap: break-word;">
                    <p>Link: https://quizcrave.com/join/{{ $gameInstance['token'] }}</p>
                </div>
                <p class="text-center text-slate-200 text-2xl px-6 py-4 font-semibold">OR</p>
                <div class="flex flex-row justify-center z-20 relative" id="qrcode"></div>
            </div>
        </x-section>
    </div>

    <script src="/vendor/trivia-game/js/qrcode.min.js"></script>
    <script>
        var qrcode = new QRCode("qrcode", {
            text: "https://is-a.gay/join/{{ $gameInstance['token'] }}",
            width: 168,
            height: 168,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        GameApi.joinRoom('{{ $gameInstance['token'] }}');


        //TODO: Convert callbackGameInstanceUpdated to event listener
        document.addEventListener('gameStarted', (e) => {
            console.log(e);
            window.location.href = '/trv/trivia/' + e.detail.gameToken;
        });

        document.addEventListener('playerJoined', (e) => {
            playerJoinedUserView(game);
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

        $('document').ready(function() {
            $('#time_limit_enabled').change(function() {
                console.log('time limit enabled changed');
                GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'time_limit_enabled', document.getElementById('time_limit_enabled').value);
            });

            $('#time_per_question').change(function() {
                console.log('time per question changed');
                GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'time_per_question', document.getElementById('time_per_question').value);
            });
        });
    </script>
@endsection