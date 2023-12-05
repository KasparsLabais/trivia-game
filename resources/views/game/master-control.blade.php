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

        const callbackGameInstanceUpdated = (gameToken, game, action) => {
            console.log('game instance updated');
            switch (action) {
                case 'gameStarted':
                    //window.location.href = '/trv/trivia/' + gameToken;
                    break;
            }
        }

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
                        playerInstances: @if(count($playerInstances) > 0) @json($playerInstances) @else {} @endif,
                        gameInstanceSettings: @json($gameInstance['gameInstanceSettings']),
                        pin: {{ $gameInstance['pin'] }},
                        gameStatus: '{{ $gameInstance['status'] }}',
                    },
                    selectedView: 'question',
                    selectedQuestionId: null,
                    pointsPerQuestion: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'points_per_question')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'points_per_question') }} @else 2 @endif,
                    pointsPerIncorrectAnswer: @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'time_limit_enabled') == 1 ) 1 @else 0 @endif,
                    bonusForSpeed: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'bonus_for_speed')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'bonus_for_speed') }} @else 2 @endif,
                    showCorrectAnswerEnabled: false,
                    settings: {
                        timeLimitEnabled: @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'time_limit_enabled') == 1 ) 1 @else 0 @endif,
                        timePerQuestion: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'time_per_question')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'time_per_question') }} @else 0 @endif,
                        playerLimitEnabled: @if((int)GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit_enabled') == 1 ) 1 @else 0 @endif,
                        playerLimit: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'player_limit') }} @else 0 @endif,
                        accessibility: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == 'private' OR GameApi::getGameInstanceSettings($gameInstance['token'], 'accessibility') == '' ) 'private' @else 'public' @endif,
                    },
                    answeredQuestions: [],
                    startedQuestions: [],
                    leaderboard: @if(count($leaderboard) > 0)  @json($leaderboard) @else [] @endif,
                    triggerNr: 0,
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                getGivenAnswer (userId) {
                    let givenAnswer = "";
                    if (this.answeredQuestions[this.selectedQuestionId] == undefined) {
                        return givenAnswer;
                    }
                    this.answeredQuestions[this.selectedQuestionId].forEach((player) => {
                        if (player.id == userId) {
                            if (player.questionType == 'text_input') {
                                givenAnswer = player.answerText;
                            } else if (player.questionType == 'options') {
                                switch (player.index) {
                                    case 0:
                                        givenAnswer = 'A';
                                        break;
                                    case 1:
                                        givenAnswer = 'B';
                                        break;
                                    case 2:
                                        givenAnswer = 'C';
                                        break;
                                    case 3:
                                        givenAnswer = 'D';
                                        break;
                                    case 4:
                                        givenAnswer = 'E';
                                        break;
                                    case 5:
                                        givenAnswer = 'F';
                                        break;
                                }
                            }
                        }
                    });

                    return givenAnswer;
                },
                playerAnswered (data) {
                    console.log('player answered', data);
                    let player = this.game.playerInstances[data.id];

                    //check if this.answeredQuestions contains key with question id
                    if (this.answeredQuestions[data.questionId] == undefined) {
                        this.answeredQuestions[data.questionId] = [];
                    }

                    //check if provided answerId is correct
                    let isCorrect = false;

                    this.selectedQuestion.answers.forEach((answer) => {
                        if (data.question_type == 'options') {
                            if (answer.id == data.answerid && answer.is_correct) {
                                isCorrect = true;
                            }
                        } else if (data.question_type == 'text_input') {
                            if (answer.answer.toLowerCase() == data.answer_text.toLowerCase()) {
                                isCorrect = true;
                            }
                        }
                    });

                    this.answeredQuestions[data.questionId].push({
                        'id' : data.id,
                        'questionType' : data.question_type,
                        'username': player.username,
                        'answerId': data.answerid,
                        'isCorrect': isCorrect,
                        'index': typeof (data.indexId) !== 'undefined' ? data.indexId : 0,
                        'answerText': typeof (data.answer_text) !== 'undefined' ? data.answer_text : '',
                    });

                    console.log('Answered List' , this.answeredQuestions);

                },
                playerJoined(player) {
                    console.log('player joined', player);
                    /*
                    {
                        "username": "BobTheBuilder",
                        "id": 2,
                        "avatar": "/images/default-avatar.jpg"
                    }
                    */

                    this.game.playerInstances[player.id] = {
                        'id' : player.id,
                        'username' : player.username,
                        'avatar' : player.avatar,
                        'icon_flair' : ''
                    };

                    this.leaderboard.push({
                        'id' : player.id,
                        'user_id': player.id,
                        'username' : player.username,
                        'points' : 0
                    });
                    this.triggerNr++;
                    /*
                    this.$set(this.game.playerInstances, player.id, {
                        'id' : player.id,
                        'username' : player.username,
                        'avatar' : player.avatar,
                        'icon_flair' : ''
                    });
                     */
                    console.log(this.game.playerInstances);

                    //this.game.playerInstances = game.playerInstances;
                },
                selectQuestion(questionId) {

                    this.selectedView = 'question';

                    this.showCorrectAnswerEnabled = false;
                    this.selectedQuestionId = questionId;

                    Object.keys(this.game.playerInstances).forEach((key) => {
                        //this.game.playerInstances[key].answerGiven = false;
                    });
                },
                previousQuestion() {
                    console.log('previous next');
                    let questionIndex = this.game.questions.findIndex(question => question.id === this.selectedQuestionId);
                    let nextQuestionIndex = questionIndex - 1;
                    if (nextQuestionIndex < 0) {
                        nextQuestionIndex = this.game.questions.length - 1;
                    }
                    this.selectQuestion(this.game.questions[nextQuestionIndex].id);
                },
                nextQuestion() {
                    console.log('previous question');
                    let questionIndex = this.game.questions.findIndex(question => question.id === this.selectedQuestionId);
                    let nextQuestionIndex = questionIndex + 1;
                    if (nextQuestionIndex >= this.game.questions.length) {
                        nextQuestionIndex = 0;
                    }
                    this.selectQuestion(this.game.questions[nextQuestionIndex].id);
                },
                deselectQuestion() {
                    this.selectedQuestionId = null;
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
                changePointsPerQuestions() {
                    console.log('points per question changed');
                    GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'points_per_question', this.pointsPerQuestion);
                },
                changeBonusForSpeed() {
                    console.log('bonus for speed changed');
                    GameApi.updateGameInstanceSetting('{{ $gameInstance['token'] }}', 'bonus_for_speed', this.bonusForSpeed);
                },
                showCorrectAnswer() {
                    this.showCorrectAnswerEnabled = true;

                    let currentQuestion = this.selectedQuestion;
                    console.log('show correct answer', currentQuestion);

                    if (currentQuestion.question_type == 'text_input') {

                        currentQuestion.answers.forEach((answer) => {
                            console.log(answer);
                            GameApi.notifyRoom('{{ $gameInstance['token'] }}', {
                                payload: {'answer_text': answer.answer, 'file_url' : answer.file_url, 'question_type': currentQuestion.question_type},
                                'action': 'showCorrectAnswer'
                            });
                        });
                    } else {
                        currentQuestion.answers.forEach((answer) => {
                            console.log(answer);
                            if (answer.is_correct) {
                                GameApi.notifyRoom('{{ $gameInstance['token'] }}', {
                                    payload: {'answer_id': answer.id, 'question_type': currentQuestion.question_type},
                                    'action': 'showCorrectAnswer'
                                });
                            }
                        });
                    }
                },
                showWinningTeam() {
                    this.selectedView = 'winner';
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {'winnerName': this.questionWinner.username}, 'action': 'showWinningTeam'});
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {}, 'action': 'updatePoints'});
                },
                showQuestionView() {
                    this.selectedView = 'question';
                },
                startQuestion() {
                    console.log('start question');

                    if (this.game.gameStatus == 'created') {
                        this.changeTriviaToStarted();
                    }

                    let question = {
                        'id': this.selectedQuestion.id,
                        'question': this.selectedQuestion.question,
                        'question_type' : this.selectedQuestion.question_type,
                        'answers' : [],
                    };

                    this.selectedQuestion.answers.forEach((answer) => {
                        question['answers'].push({
                            'id': answer.id,
                            'answer': answer.answer,
                            'is_correct' : 0
                        });
                    });

                    this.startedQuestions.push(this.selectedQuestion.id);
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: question, 'action': 'startQuestion'});
                },
                startTimer() {
                    console.log('start timer');
                    if (this.game.gameStatus == 'created') {
                        this.changeTriviaToStarted();
                    }

                    let question = {
                        'id': this.selectedQuestion.id,
                        'question': this.selectedQuestion.question,
                        'answers' : [],
                    };

                    this.selectedQuestion.answers.forEach((answer) => {
                        question['answers'].push({
                            'id': answer.id,
                            'answer': answer.answer,
                            'is_correct' : 0
                        });
                    });
                    this.startedQuestions.push(this.selectedQuestion.id);
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: { 'questionData': question, 'timeLimit': this.settings.timePerQuestion }, 'action': 'startTimer'});
                },
                changeTriviaToStarted() {
                    fetch('/trv/start', {'method': 'POST', 'headers': {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'gameToken': '{{ $gameInstance['token'] }}'})})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                this.game.gameStatus = 'started';
                                GameApi.updateGameInstance('{{ $gameInstance['token'] }}', data.data.gameInstance, 'gameStarted');
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.log(error));
                },
                hasBeenPlayed(questionId) {
                    return this.startedQuestions.includes(questionId);
                },
                updateLeaderboard() {
                    console.log('update leaderboard');
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/leaderboard', {'method' : 'GET', 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                this.leaderboard = data.data;
                            }
                        })
                        .catch(error => console.log(error));
                },
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
                playerInstances() {
                    return this.game.playerInstances;
                },
                questionWinner() {
                    //this.answeredQuestions[data.questionId]
                    //return first correct answered player
                    let winner =  {'username':'Winner not found'};

                    if(this.answeredQuestions[this.selectedQuestionId] == undefined) {
                        return winner;
                    }

                    this.answeredQuestions[this.selectedQuestionId].forEach((player) => {
                        console.log('Player Data', player);
                        if (player.isCorrect) {
                            winner = player;
                        }
                    });

                    console.log('Player Data', winner);

                    return winner;
                },
                questions() {
                    //sort this.game.questions by order_nr
                    this.game.questions.sort((a, b) => (a.order_nr > b.order_nr) ? 1 : -1);
                    return this.game.questions;
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
                    console.log('Player Joined Event', e.detail);
                    this.playerJoined(e.detail.player);
                });

                document.addEventListener('playerAnsweredEvent', (e) => {
                    console.log('Player Answered Event', e.detail);
                    this.playerAnswered(e.detail);
                });

                GameApi.joinRoom('{{ $gameInstance['token'] }}');
                //GameApi.registerCallbackGameInstanceUpdated(callbackGameInstanceUpdated);
                //GameApi.registerCallbackPlayerJoined(this.playerJoined);
            }
        }).mount('#master-app');

    </script>

@endsection