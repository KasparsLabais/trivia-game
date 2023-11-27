@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div id="master-app" class="flex flex-row w-full">

        <div id="master-layout_left" class="w-3/12 h-screen bg-zinc-800 shadow-md overflow-y-auto">
            @include('trivia-game::game.widgets.questions')
        </div>

        <div id="master-layout_center" class="w-6/12 bg-zinc-700">
            <div class="bg-zinc-700 h-3/6">
                @include('trivia-game::game.widgets.question')
            </div>
            <div class="bg-zinc-600 h-3/6">
                @include('trivia-game::game.widgets.players')
            </div>
        </div>

        <div id="master-layout_right" class="w-3/12 bg-slate-200">
            <div>
                @include('trivia-game::game.widgets.settings')
            </div>
            <div>
                @include('trivia-game::game.widgets.leaderboard')
            </div>
            <div>
                @include('trivia-game::game.widgets.streamers')
            </div>
        </div>
    </div>



    @include('trivia-game::game.partials.scripts')

    <script src="/vendor/trivia-game/js/qrcode.min.js"></script>
    <script>

        var qrcode = new QRCode("qrcode", {
            text: "https://quizcrave.com/join/{{ $gameInstance['token'] }}",
            width: 128,
            height: 128,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });



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

        /*
        document.addEventListener('playerJoined', (e) => {
            playerJoined(game);
        });

         */

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


    <script>

        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    game: {
                        gameInstance: @json($gameInstance),
                        trivia: @json($trivia),
                        questions: @json($questions->load('answers')),
                        playerInstances: @json($playerInstances),
                        gameInstanceSettings: @json($gameInstance['gameInstanceSettings']),
                        pin: {{ $gameInstance['pin'] }},
                    },
                    selectedQuestionId: null,
                    pointsPerQuestion: 2,
                    pointsPerIncorrectAnswer: 0,
                    bonusForSpeed: 2,
                    showCorrectAnswerEnabled: false,
                    settings: {
                        timeLimitEnabled: @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'time_limit_enabled') == 1 ) 1 @else 0 @endif,
                        timePerQuestion: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'time_per_question')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'time_per_question') }} @else 0 @endif,
                        playerLimitEnabled: @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit_enabled') == 1 ) 1 @else 0 @endif,
                        playerLimit: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit') }} @else 0 @endif,
                        accessibility: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == 'private' OR GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == '' ) 'private' @else 'public' @endif,
                    },
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                playerJoined(game) {
                    console.log('player joined', game);
                    this.game.playerInstances = game.playerInstances;
                },
                selectQuestion(questionId) {
                    this.showCorrectAnswerEnabled = false;
                    this.selectedQuestionId = questionId;
                },
                changeAccessibility(accessibilityType)  {
                    console.log('change accessibility', accessibilityType);
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/accessibility', {'method' : 'POST', 'body': JSON.stringify({'accessibility':accessibilityType}), 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                this.settings.accessibility = accessibilityType;
                            }
                        })
                        .catch(error => console.log(error));
                },
                changePlayerLimit() {
                    if (this.settings.playerLimitEnabled == 1 && this.settings.playerLimit <= 0) {
                        GameApi.triggerAlertNotification('{{ $gameInstance['token'] }}', 'player', 'error', 'Player limit must be greater than 0', window.id );
                        return;
                    }

                    playerLimit = (this.settings.playerLimitEnabled == 1) ? this.settings.playerLimit : 0;

                    GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'player_limit_enabled', this.settings.playerLimitEnabled);
                    GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'player_limit', playerLimit);
                },
                changeTimeLimitEnabled() {
                    console.log('time limit enabled changed');
                    GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'time_limit_enabled', this.settings.timeLimitEnabled);
                },
                changeTimeLimit() {
                    console.log('time per question changed');
                    GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'time_per_question', this.settings.timePerQuestion);
                },
                showCorrectAnswer() {

                    this.showCorrectAnswerEnabled = true;
                    /*
                    if (this.selectedQuestionId === null) {
                        return null;
                    }

                    let currentQuestion = this.selectedQuestion;
                    console.log('show correct answer', currentQuestion);

                    currentQuestion.answers.forEach((answer) => {
                        console.log(answer);
                        let answerHolder = document.querySelector('.answer-holder button[answer-id="' + answer.id + '"]');
                        if (answer.is_correct) {
                            answerHolder.classList.remove('bg-lime-600');
                            answerHolder.classList.add('bg-violet-500');
                        } else {
                            //answerHolder.classList.remove('bg-lime-600');
                            //answerHolder.classList.add('bg-slate-200');
                        }
                    });
                    */
                    /*
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/correct', {'question' : currentQuestion})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                let answerHolder = document.querySelector('.answer-holder .flex[answer-id="' + data.data.answer.id + '"] button');
                                answerHolder.classList.remove('bg-lime-500');
                                answerHolder.classList.add('bg-violet-500');

                                GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {'answer-id': data.data.answer.id}, 'action': 'showCorrectAnswer'});
                            }
                        })
                        */
                }
            },
            computed: {
                selectedQuestion() {
                    if (this.selectedQuestionId === null) {
                        return null;
                    }
                    return this.game.questions.find(question => question.id === this.selectedQuestionId);
                },
                playerCount() {
                    return Object.keys(this.game.playerInstances).length;
                },


                //still in progress
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

                GameApi.joinRoom('{{ $gameInstance['token'] }}');
                //GameApi.registerCallbackGameInstanceUpdated(callbackGameInstanceUpdated);
                //GameApi.registerCallbackPlayerJoined(this.playerJoined);
            }
        }).mount('#master-app');

    </script>

@endsection