@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div id="master-app" class="flex flex-row w-full">

        <div id="master-layout_left" class="w-3/12 h-screen bg-zinc-800 shadow-md overflow-y-auto">
            @include('trivia-game::game.widgets.questions')
        </div>

        <div id="master-layout_center" class="w-6/12 bg-zinc-700">
            <div class="h-1/2 bg-zinc-700">
                @include('trivia-game::game.widgets.question')
            </div>
            <div class="h-1/2  bg-zinc-600">
                @include('trivia-game::game.widgets.players')
            </div>
        </div>

        <div id="master-layout_right" class="w-3/12 bg-slate-200">
            <div>
                @include('trivia-game::game.widgets.leaderboard')
            </div>
            <div>
                @include('trivia-game::game.widgets.streamers')
            </div>
        </div>
    </div>


    @include('trivia-game::game.partials.scripts')
    <div class="flex flex-row justify-center px-4 md:px-none">
        <div class="flex flex-col">
            <div class="bg-slate-200 px-6 py-8">
                <h1 class="fira-sans font-semibold text-2xl border-b border-slate-300 my-2">{{ $trivia['title'] }} (PIN: {{ $gameInstance['pin'] }} )</h1>
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
                @if(Auth::check() && (Auth::user()->id == $gameInstance['user_id']))
                    <span class="fira-sans font-semibold text-xl my-2">Settings:</span>
                    <div class="border-b border-b-slate-300 pb-4">
                        <div>
                            <span>Time Settings</span>
                        </div>
                        <div class="flex flex-row justify-between w-full">
                            <div class="flex flex-row w-3/6">
                                <label class="raleway font-normal text-base" for="time_limit_enabled">Enabled:</label>
                                <select class="raleway font-normal text-base capitalize" name="time_limit_enabled" id="time_limit_enabled">
                                    <option value="1" @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'time_limit_enabled') == 1 ) selected="selected" @endif>True</option>
                                    <option value="0" @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'time_limit_enabled') == 0 ) selected="selected" @endif>False</option>
                                </select>
                            </div>
                            <div class="flex flex-row w-3/6">
                                <label class="raleway font-normal text-base w-4/6" for="time_per_question">Time Per Question (s):</label>
                                <input type="number" step="1" min="0" class="text-center raleway font-normal text-base capitalize w-1/6" value="{{ GameApi::getGameInstanceSettings($gameInstance['token'], 'time_per_question') }}" name="time_per_question" id="time_per_question" />
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-b-slate-300 pb-4">
                        <div>
                            <span>Player Limit</span>
                        </div>
                        <div class="flex flex-row justify-between w-full">
                            <div class="flex flex-row w-3/6">
                                <label class="raleway font-normal text-base" for="player_limit_enabled">Enabled:</label>
                                <select onchange="changePlayerLimit()" class="raleway font-normal text-base capitalize" name="player_limit_enabled" id="player_limit_enabled">
                                    <option value="1" @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit_enabled') == 1 ) selected="selected" @endif>True</option>
                                    <option value="0" @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit_enabled') == 0 ) selected="selected" @endif>False</option>
                                </select>
                            </div>
                            <div class="flex flex-row w-3/6">
                                <label class="raleway font-normal text-base w-4/6" for="time_per_question">Player Limit:</label>
                                <input type="number" step="1" min="0" class="text-center raleway font-normal text-base capitalize w-1/6" value="{{ GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit') }}" name="player_limit" id="player_limit" />
                            </div>
                        </div>
                    </div>
                    <div class="border-b border-b-slate-300 pb-4">
                        <div>
                            <span>Trivia Accessibility</span>
                        </div>
                        <div class="flex flex-row justify-between w-full">
                            <div class="flex flex-row w-full">
                                <div class="w-full flex flex-row shadow-inner rounded bg-slate-300 py-2 px-2">
                                    <div class="px-1 w-1/3">
                                        <div onclick="changeAccessibility('private')" data-accessibility="private" class="accessibility-setting text-center px-2 py-2 rounded font-semibold  shadow @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == 'private' OR GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == '' ) bg-lime-500 text-gray-100 @else bg-slate-300 text-gray-400 hover:bg-lime-500 hover:text-gray-100 @endif">
                                            Only By Link
                                        </div>
                                    </div>
                                    <div class="px-1 w-1/3">
                                        <div onclick="changeAccessibility('public')" data-accessibility="public" class="accessibility-setting text-center px-2 py-2 rounded font-semibold shadow @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == 'public') bg-lime-500 text-gray-100 @else bg-slate-300 text-gray-400 hover:bg-lime-500 hover:text-gray-100 @endif">
                                            Open Access
                                        </div>
                                    </div>
                                    <!--
                                    <div class="px-1 w-1/3">
                                        <div class="text-center bg-slate-300 px-2 py-2 rounded font-semibold text-gray-400 shadow">
                                            Password Protected
                                        </div>
                                    </div>
                                    -->
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="bg-slate-300 px-6 py-8">
                @if(Auth::check() && (Auth::user()->id == $gameInstance['user_id']))
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
            GameApi.updateGameInstanceSettings('{{ $gameInstance['token'] }}', 'time_limit_enabled', document.getElementById('time_limit_enabled').value);
            fetch('/trv/start', {'method': 'POST', 'headers': {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'gameToken': '{{ $gameInstance['token'] }}'})})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if(data.success) {
                        GameApi.updateGameInstance('{{ $gameInstance['token'] }}', data.data.gameInstance, 'gameStarted');
                    } else {
                        alert(data.message);
                    }
                    //window.location.href = '/trv/trivia/' + data.data.token;
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

        const changeAccessibility = (accessibility) => {
            console.log('change accessibility', accessibility);
            fetch('/trv/trivia/{{ $gameInstance['token'] }}/accessibility', {'method' : 'POST', 'body': JSON.stringify({'accessibility':accessibility}), 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if(data.success) {
                        let accessibilitySettings = document.getElementsByClassName('accessibility-setting');
                        for(let i = 0; i < accessibilitySettings.length; i++) {
                            accessibilitySettings[i].classList.remove('bg-lime-500');
                            accessibilitySettings[i].classList.remove('text-gray-100');
                            accessibilitySettings[i].classList.remove('hover:bg-lime-500');
                            accessibilitySettings[i].classList.remove('hover:text-gray-100');
                            accessibilitySettings[i].classList.add('bg-slate-300');
                            accessibilitySettings[i].classList.add('text-gray-400');
                        }
                        let accessibilitySetting = document.querySelector('[data-accessibility="' + accessibility + '"]');
                        accessibilitySetting.classList.remove('bg-slate-300');
                        accessibilitySetting.classList.remove('text-gray-400');
                        accessibilitySetting.classList.add('bg-lime-500');
                        accessibilitySetting.classList.add('text-gray-100');
                        accessibilitySetting.classList.add('hover:bg-lime-500');
                        accessibilitySetting.classList.add('hover:text-gray-100');
                    }
                })
                .catch(error => console.log(error));
        }


        const changePlayerLimit = () => {

            let status = document.getElementById('player_limit_enabled').value;
            let playerLimit = document.getElementById('player_limit').value;

            if (status == 1 && playerLimit <= 0) {
                GameApi.triggerAlertNotification('{{ $gameInstance['token'] }}', 'player', 'error', 'Player limit must be greater than 0', window.id );
                return;
            }

            playerLimit = (status == 1) ? playerLimit : 0;

            GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'player_limit_enabled', status);
            GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'player_limit', playerLimit);
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


    <script>

        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    game: {
                        gameInstance: @json($gameInstance),
                        trivia: @json($trivia),
                        questions: @json($questions->load('answers')),
                        playerInstances: @json($gameInstance['playerInstances']),
                        gameInstanceSettings: @json($gameInstance['gameInstanceSettings']),
                    },
                    selectedQuestionId: null,
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                playerJoined(game) {
                    console.log('player joined', game);
                    this.game.playerInstances = game.playerInstances;
                },
                selectQuestion(questionId) {
                    this.selectedQuestionId = questionId;
                },
            },
            computed: {
                selectedQuestion() {
                    if (this.selectedQuestionId === null) {
                        return null;
                    }
                    return this.game.questions.find(question => question.id === this.selectedQuestionId);
                },
                //still in progress
                playerCount() {
                    return this.game.playerInstances.length;
                },
                playerLimit() {
                    return this.game.gameInstanceSettings.player_limit;
                },
                playerLimitEnabled() {
                    return this.game.gameInstanceSettings.player_limit_enabled;
                },
                timeLimitEnabled() {
                    return this.game.gameInstanceSettings.time_limit_enabled;
                },
                timePerQuestion() {
                    return this.game.gameInstanceSettings.time_per_question;
                },
                accessibility() {
                    return this.game.gameInstanceSettings.accessibility;
                },
                gameStarted() {
                    return this.game.gameInstance.game_started;
                },
                gameInstance() {
                    return this.game.gameInstance;
                },
                questions() {
                    return this.game.questions;
                },
                playerInstances() {
                    return this.game.playerInstances;
                },
                trivia() {
                    return this.game.trivia;
                },
                selectedQuestionAnswers() {
                    return this.selectedQuestion.answers;
                },
                selectedQuestionCorrectAnswer() {
                    return this.selectedQuestion.correct_answer;
                },
                selectedQuestionAnswered() {
                    return this.selectedQuestion.answered;
                },
                selectedQuestionAnsweredCorrectly() {
                    return this.selectedQuestion.answered_correctly;
                },
                selectedQuestionAnsweredIncorrectly() {
                    return this.selectedQuestion.answered_incorrectly;
                },
                selectedQuestionAnsweredCorrectlyBy() {
                    return this.selectedQuestion.answered_correctly_by;
                },
                selectedQuestionAnsweredIncorrectlyBy() {
                    return this.selectedQuestion.answered_incorrectly_by;
                },
                selectedQuestionAnsweredCorrectlyByCount() {
                    return this.selectedQuestion.answered_correctly_by_count;
                },
                selectedQuestionAnsweredIncorrectlyByCount() {
                    return this.selectedQuestion.answered_incorrectly_by_count;
                },
                selectedQuestionAnsweredCount() {
                    return this.selectedQuestion.answered_count;
                },
                selectedQuestionAnsweredCorrectlyPercent() {
                    return this.selectedQuestion.answered_correctly_percent;
                },
                selectedQuestionAnsweredIncorrectlyPercent() {
                    return this.selectedQuestion.answered_incorrectly_percent;
                },
                selectedQuestionAnsweredPercent() {
                    return this.selectedQuestion.answered_percent;
                },
                selectedQuestionAnsweredCorrectlyByPercent() {
                    return this.selectedQuestion.answered_correctly_by_percent;
                },
                selectedQuestionAnsweredIncorrectlyByPercent() {
                    return this.selectedQuestion.answered_incorrectly_by_percent;
                }
            },
            mounted() {

                document.addEventListener('playerJoined', (e) => {
                    this.playerJoined(game);
                });



                //GameApi.registerCallbackGameInstanceUpdated(callbackGameInstanceUpdated);
                //GameApi.registerCallbackPlayerJoined(this.playerJoined);
            }
        }).mount('#master-app');

    </script>

@endsection