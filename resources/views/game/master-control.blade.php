@extends('game-api::layout')
@section('body')
    @php
        // Base URL for join links / QR codes. Defaults to the app's own URL;
        // override with GAME_JOIN_URL (config settings.join_url) for a short domain.
        $joinUrlBase = rtrim(config('settings.join_url') ?: url('/'), '/');
    @endphp

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div id="master-app" class="flex flex-row w-full">

        <div id="master-layout_left" class="w-3/12 h-screen bg-main-dark shadow-md overflow-y-auto">
            @include('trivia-game::game.widgets.questions')
        </div>

        <div id="master-layout_center" class="w-6/12 bg-panel">
            <div class="bg-panel h-3/6">
                @include('trivia-game::game.widgets.question')
            </div>
            <div class="bg-panel h-3/6">
                @include('trivia-game::game.widgets.players')
            </div>
        </div>

        <div id="master-layout_right" class="w-3/12 bg-main-light">
            <div class="px-2 pt-2">
                <a href="/trv/trivia/{{ $gameInstance['token'] }}/display" target="_blank" rel="noopener"
                   class="flex flex-row items-center justify-center gap-2 fira-sans text-sm font-semibold py-2 px-4 rounded bg-main-accent text-white hover:opacity-90 transition-opacity shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-5v2h2a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2h2v-2H5a2 2 0 0 1-2-2V5Z"/>
                    </svg>
                    Open Display Screen
                </a>
            </div>
            <div class="py-2">
                @include('trivia-game::game.widgets.leaderboard')
            </div>
            <div class="py-2">
                @include('trivia-game::game.widgets.settings')
            </div>
            <div class="py-2">
                @include('trivia-game::game.widgets.streamers')
            </div>
        </div>



        <div class="absolute player_modal" :class="{ 'hidden' : !playerEditModalVisible }">
            <div class="game_modal-overlay fixed top-0 h-screen w-screen bg-main-dark opacity-80" @click="closePlayerEditModal()">
            </div>
            <div class="game_modal-content fixed flex flex-col w-full h-full justify-center">
                <div class="flex flex-row w-full justify-center">
                    <div class="w-11/12 bg-main-light mt-4 shadow-md">
                        <div class="game_modal-header bg-surface w-full px-4 py-4 text-center">
                            <h2 class="raleway font-semibold text-2xl">Player: [[ selectedPlayer.username ]]</h2>
                        </div>
                        <div class="game_modal-body w-full flex flex-row px-4 py-4 justify-center">
                            <div class="flex flex-row">
                                <input class="text-center josefin-sans text-2xl" type="number" id="pointsToAdd" name="pointsToAdd" v-model="pointsToAdd">
                                <div class="px-2">
                                    <button type="button" class="text-center px-2 py-2 rounded font-semibold shadow bg-green-600 text-white" @click="addPointsToPlayer()">
                                        Add Points
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-row">
                                <input class="text-center josefin-sans text-2xl" type="number" id="pointsToRemove" name="pointsToRemove" v-model="pointsToRemove">
                                <div class="px-2">
                                    <button type="button" class="text-center px-2 py-2 rounded font-semibold shadow bg-main-accent text-white" @click="removePointsToPlayer()">
                                        Remove Points
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="game_modal-footer" class="bg-surface w-full px-4 py-4">
                            <button type="button" @click="closePlayerEditModal()" class="w-full fira-sans py-2 px-2 md:px-6 shadow bg-brand text-slate-100 font-semibold text-lg  shadow-gray-900 my-2">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <script src="/vendor/trivia-game/js/qrcode.min.js"></script>
    <script>

        var qrcode = new QRCode("qrcode", {
            text: "{{ $joinUrlBase }}/join/{{ $gameInstance['token'] }}",
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
                    gameToken: '{{ $gameInstance['token'] }}',
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
                    bonusForSpeed: @if(GameApi::getGameInstanceSettings($gameInstance['token'], 'bonus_for_speed')) {{ GameApi::getGameInstanceSettings($gameInstance['token'], 'bonus_for_speed') }} @else 0 @endif,
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
                    playerEditModalVisible: false,
                    selectedPlayer: [],
                    pointsToAdd: 0,
                    pointsToRemove: 0,
                    notificationMessage: '',
                    notificationTarget: 'all',
                    notificationActions: @json($notificationActions),
                    selectedActionId: 'custom',
                    notificationTimestamps: [],
                    lastNotificationAt: 0,
                    timerActive: false,
                    timerTimeLeft: 0,
                    timerInterval: null,
                    // True between "Show Question Only" and revealing the options,
                    // so the center widget offers a "Reveal Answers" button.
                    questionOnlyActive: false,
                    // open_input (judged) submissions for the selected question,
                    // the winner the master has picked, and their display name.
                    openSubmissions: [],
                    selectedWinnerUserId: null,
                    openWinnerName: '',
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                sendNotification () {
                    // Either a custom typed message, or a pre-set action that
                    // carries its own message, level and colour.
                    let message, colour = null, level = null;
                    if (this.selectedActionId == 'custom') {
                        message = this.notificationMessage.trim();
                    } else {
                        let action = this.selectedAction;
                        if (!action) { return; }
                        message = action.message;
                        colour = action.colour;
                        level = action.level;
                    }
                    if (!message) { return; }

                    // Rate limiting to prevent spam: at least 5s between messages,
                    // and no more than 3 messages in any 20s window.
                    let now = Date.now();
                    if (this.lastNotificationAt && (now - this.lastNotificationAt) < 5000) {
                        let wait = Math.ceil((5000 - (now - this.lastNotificationAt)) / 1000);
                        GameApi.displayAlertNotification('error', 'Please wait ' + wait + 's before sending another message.');
                        return;
                    }
                    this.notificationTimestamps = this.notificationTimestamps.filter(t => (now - t) < 20000);
                    if (this.notificationTimestamps.length >= 3) {
                        GameApi.displayAlertNotification('error', 'Message limit reached — you can send up to 3 messages per 20 seconds.');
                        return;
                    }
                    this.notificationTimestamps.push(now);
                    this.lastNotificationAt = now;

                    // One-time message; game.js re-dispatches it as a
                    // 'gameMasterNotification' DOM event on the receiver(s).
                    // notificationTarget is 'all' or a player's user_id; the node
                    // verifies we're the game master before delivering.
                    GameApi.gameMasterNotify(this.gameToken, this.notificationTarget, {
                        action: 'gameMasterNotification',
                        payload: { from: window.username, message: message, colour: colour, level: level }
                    }, (response) => {
                        if (response && response.status) {
                            // Clear the message form and confirm to the game master.
                            this.notificationMessage = '';
                            this.selectedActionId = 'custom';
                            GameApi.displayAlertNotification('success', 'Notification sent');
                        } else {
                            GameApi.displayAlertNotification('error', (response && response.message) ? response.message : 'Could not send notification');
                        }
                    });
                },
                startMasterTimer (duration, current) {
                    // Local countdown so the game master sees the running timer on
                    // the Start Timer button. Resumes from `current` after a reload.
                    clearInterval(this.timerInterval);
                    this.timerActive = true;
                    this.timerTimeLeft = (typeof current === 'undefined') ? duration : current;
                    this.timerInterval = setInterval(() => {
                        this.timerTimeLeft = this.timerTimeLeft - 1;
                        if (this.timerTimeLeft <= 0) {
                            this.timerTimeLeft = 0;
                            this.stopMasterTimer();
                        }
                    }, 1000);
                },
                stopMasterTimer () {
                    clearInterval(this.timerInterval);
                    this.timerActive = false;
                },
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

                    // open_input (judged) submissions are collected separately and
                    // shown live in the center widget — no correctness scoring.
                    if (data.question_type == 'open_input') {
                        let sub = { user_id: data.id, username: data.username, value: data.value, is_image: data.is_image };
                        let idx = this.openSubmissions.findIndex(s => s.user_id == data.id);
                        if (idx >= 0) { this.openSubmissions.splice(idx, 1, sub); }
                        else { this.openSubmissions.push(sub); }
                        return;
                    }

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

                    let answeredQuestionsJsonString = JSON.stringify(this.answeredQuestions);
                    this.setCookie('answeredQuestions', answeredQuestionsJsonString, 1);

                },
                playerJoined(player) {
                    console.log('player joined', player);

                    this.game.playerInstances[player.id] = {
                        'id' : player.id,
                        'user_id' : player.id,
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
                    this.questionOnlyActive = false;
                    this.openSubmissions = [];
                    this.selectedWinnerUserId = null;
                    this.openWinnerName = '';
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
                changeTriviaTitle() {
                    console.log('trivia title changed');
                    GameApi.updateGameMainGameInstanceSetting('{{ $gameInstance['token'] }}', 'title', this.game.gameInstance.title);
                },
                showCorrectAnswer() {
                    this.showCorrectAnswerEnabled = true;
                    this.questionOnlyActive = false;
                    this.eraseCookie('master_question_only');

                    // The round is over — clear the timer so reconnecting players
                    // don't resume a finished countdown.
                    GameApi.clearQuestionTimer('{{ $gameInstance['token'] }}');
                    this.stopMasterTimer();

                    let currentQuestion = this.selectedQuestion;
                    console.log('show correct answer', currentQuestion);

                    if (currentQuestion.question_type == 'text_input') {

                        currentQuestion.answers.forEach((answer) => {
                            console.log(answer);
                            GameApi.notifyRoom('{{ $gameInstance['token'] }}', {
                                payload: {'answer_text': answer.answer, 'file_url' : answer.file_url, 'file_url_type': answer.file_url_type, 'question_type': currentQuestion.question_type},
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

                    // Revealing the answer finalises scoring — tell players to
                    // refresh their points immediately (no reload needed).
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {}, 'action': 'updatePoints'});
                },
                showWinningTeam() {
                    // open_input: the winner is whichever submission the master
                    // selected; award points_per_question to them.
                    if (this.selectedQuestion.question_type == 'open_input') {
                        this.showOpenWinner();
                        return;
                    }
                    this.selectedView = 'winner';
                    this.openWinnerName = '';
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {'winnerName': this.questionWinner.username}, 'action': 'showWinningTeam'});
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {}, 'action': 'updatePoints'});
                },
                // open_input: fetch the authoritative submissions, broadcast them
                // to players/display, and show them (selectable) on the master.
                revealOpenSubmissions() {
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/submissions/' + this.selectedQuestion.id, {
                        method: 'GET', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            this.openSubmissions = data.data;
                            GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {submissions: data.data}, 'action': 'revealSubmissions'});
                        }
                    }).catch(error => console.log(error));
                },
                selectWinner(userId) {
                    this.selectedWinnerUserId = (this.selectedWinnerUserId == userId) ? null : userId;
                },
                showOpenWinner() {
                    if (!this.selectedWinnerUserId) {
                        if (!confirm('No winner selected. Are you sure you do not want to pick a winner?')) {
                            return;
                        }
                        // Proceed with no winner and no points.
                        GameApi.displayAlertNotification('info', 'No winner selected — no points awarded.');
                        return;
                    }

                    let winner = this.openSubmissions.find(s => s.user_id == this.selectedWinnerUserId);
                    if (!winner) { return; }

                    this.openWinnerName = winner.username;

                    // Award the game's per-question points to the winner.
                    fetch('/trivia/{{ $gameInstance['token'] }}/add-points', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ user_id: winner.user_id, pointsToAdd: this.pointsPerQuestion })
                    }).then(r => r.json()).then(data => {
                        if (data.success) { this.updateLeaderboard(); }
                    }).catch(error => console.log(error));

                    this.selectedView = 'winner';
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {'winnerName': winner.username}, 'action': 'showWinningTeam'});
                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {}, 'action': 'updatePoints'});
                },
                showQuestionView() {
                    this.selectedView = 'question';
                },
                startQuestion() {
                    console.log('start question');

                    // Untimed question — clear any previous timer.
                    GameApi.clearQuestionTimer('{{ $gameInstance['token'] }}');
                    this.stopMasterTimer();
                    // Full reveal — no pending "reveal answers" step.
                    this.questionOnlyActive = false;
                    this.eraseCookie('master_question_only');

                    if (this.game.gameStatus == 'created') {
                        this.changeTriviaToStarted();
                    }

                    let question = {
                        'id': this.selectedQuestion.id,
                        'question': this.selectedQuestion.question,
                        'question_type' : this.selectedQuestion.question_type,
                        'input_type' : this.selectedQuestion.input_type,
                        'answers' : [],
                    };

                    this.selectedQuestion.answers.forEach((answer) => {
                        question['answers'].push({
                            'id': answer.id,
                            'answer': answer.answer,
                            'is_correct' : 0
                        });
                    });

                    // Fresh question — clear any previous open_input submissions.
                    this.openSubmissions = [];
                    this.selectedWinnerUserId = null;
                    this.openWinnerName = '';

                    this.startedQuestions.push(this.selectedQuestion.id);

                    let startedQuestionsJsonString = JSON.stringify(this.startedQuestions);
                    this.setCookie('startedQuestions', startedQuestionsJsonString, 1);
                    // Remember the active question so a reload returns the master to it.
                    this.setCookie('master_active_question', this.selectedQuestion.id, 1);

                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: question, 'action': 'startQuestion'});
                },
                startQuestionOnly() {
                    console.log('start question only');

                    // Show just the question text — players (and the display) see
                    // the question but the answer options stay hidden until the
                    // game master clicks "Reveal Answers".
                    GameApi.clearQuestionTimer('{{ $gameInstance['token'] }}');
                    this.stopMasterTimer();

                    if (this.game.gameStatus == 'created') {
                        this.changeTriviaToStarted();
                    }

                    let question = {
                        'id': this.selectedQuestion.id,
                        'question': this.selectedQuestion.question,
                        'question_type' : this.selectedQuestion.question_type,
                        'input_type' : this.selectedQuestion.input_type,
                        'answers' : [],
                        // Tells the receiver to keep the options hidden for now.
                        'answersHidden' : true,
                    };

                    // Fresh question — clear any previous open_input submissions.
                    this.openSubmissions = [];
                    this.selectedWinnerUserId = null;
                    this.openWinnerName = '';

                    this.startedQuestions.push(this.selectedQuestion.id);
                    this.setCookie('startedQuestions', JSON.stringify(this.startedQuestions), 1);
                    this.setCookie('master_active_question', this.selectedQuestion.id, 1);
                    // Remember we're mid-reveal so a reload restores the Reveal button.
                    this.questionOnlyActive = true;
                    this.setCookie('master_question_only', this.selectedQuestion.id, 1);

                    GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: question, 'action': 'startQuestion'});
                },
                revealAnswers() {
                    console.log('reveal answers');

                    // open_input: "Reveal Answers" means show everyone's
                    // submissions (to players/display) rather than reveal options.
                    if (this.selectedQuestion.question_type == 'open_input') {
                        this.revealOpenSubmissions();
                        this.questionOnlyActive = false;
                        this.eraseCookie('master_question_only');
                        return;
                    }

                    let answers = [];
                    this.selectedQuestion.answers.forEach((answer) => {
                        answers.push({
                            'id': answer.id,
                            'answer': answer.answer,
                            'is_correct' : 0
                        });
                    });

                    let question = {
                        'id': this.selectedQuestion.id,
                        'question': this.selectedQuestion.question,
                        'question_type' : this.selectedQuestion.question_type,
                        'answers' : answers,
                    };

                    this.questionOnlyActive = false;
                    this.eraseCookie('master_question_only');

                    if (this.settings.timeLimitEnabled == 1) {
                        // Timed round — reveal the options and start the countdown
                        // together (reuses the persisted-timer flow).
                        GameApi.startQuestionTimer('{{ $gameInstance['token'] }}', question, this.settings.timePerQuestion);
                        this.startMasterTimer(this.settings.timePerQuestion);
                    } else {
                        GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: question, 'action': 'revealAnswers'});
                    }
                },
                startTimer() {
                    console.log('start timer');
                    // Full reveal — no pending "reveal answers" step.
                    this.questionOnlyActive = false;
                    this.eraseCookie('master_question_only');
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
                    // Remember the active question so a reload returns the master to it.
                    this.setCookie('master_active_question', this.selectedQuestion.id, 1);
                    // Persist the timer in game-node so reconnecting players can resume it.
                    GameApi.startQuestionTimer('{{ $gameInstance['token'] }}', question, this.settings.timePerQuestion);
                    // Show the same countdown to the game master on the button.
                    this.startMasterTimer(this.settings.timePerQuestion);
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
                userActionModal(playerId) {
                    console.log('user action modal ' + playerId);
                    //get player from playerId
                    this.selectedPlayer = this.game.playerInstances[playerId];
                    this.playerEditModalVisible = true;
                },
                closePlayerEditModal() {
                    this.playerEditModalVisible = false;
                },
                addPointsToPlayer() {
                    console.log('add points to player');
                    fetch('/trivia/{{ $gameInstance['token'] }}/add-points', {'method' : 'POST', 'body': JSON.stringify({'user_id': this.selectedPlayer.user_id, 'pointsToAdd': this.pointsToAdd}), 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                //update points in player list
                                this.game.playerInstances[this.selectedPlayer.user_id].points += this.pointsToAdd;

                                this.pointsToAdd = 0;
                                this.updateLeaderboard();
                            }
                        })
                        .catch(error => console.log(error));
                    //GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {'playerId': this.selectedPlayer.id, 'pointsToAdd': this.pointsToAdd}, 'action': 'addPointsToPlayer'});
                    this.closePlayerEditModal();
                },
                removePointsToPlayer() {
                    console.log('remove points to player');
                    fetch('/trivia/{{ $gameInstance['token'] }}/remove-points', {'method' : 'POST', 'body': JSON.stringify({'user_id': this.selectedPlayer.user_id, 'pointsToRemove': this.pointsToRemove}), 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                //update points in player list
                                this.game.playerInstances[this.selectedPlayer.user_id].points -= this.pointsToRemove;

                                this.pointsToRemove = 0;
                                this.updateLeaderboard();
                            }
                        })
                        .catch(error => console.log(error));

                    this.closePlayerEditModal();
                },
                completeTrivia() {
                    fetch('/trivia/{{ $gameInstance['token'] }}/complete', {'method' : 'POST', 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            if(data.success) {
                                this.game.gameStatus = 'completed';
                                //GameApi.updateGameInstance('{{ $gameInstance['token'] }}', data.data.gameInstance, 'gameCompleted');
                                GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {}, 'action': 'gameOverEvent'});
                            }
                        });
                },
                //add function to store a cookie - accepted params are name, value, and days until expiration
                // Game state is persisted in localStorage (not cookies) so it is
                // never sent in request headers — large cookies were causing
                // nginx "400 Request Header Or Cookie Too Large" on reload.
                setCookie(name, value, days) {
                    name = name + '_' + this.gameToken;
                    try { localStorage.setItem(name, value); } catch (e) { console.log(e); }
                },
                getCookie(name) {
                    name = name + '_' + this.gameToken;
                    try { return localStorage.getItem(name); } catch (e) { return null; }
                },
                eraseCookie(name) {
                    name = name + '_' + this.gameToken;
                    try { localStorage.removeItem(name); } catch (e) {}
                },
                checkCookie(name) {
                    var value = this.getCookie(name);
                    return value !== null && value !== '';
                },
                // One-time cleanup of legacy cookies (from before the localStorage
                // switch) across all games, so they stop bloating request headers.
                clearLegacyCookies() {
                    var prefixes = ['answeredQuestions', 'startedQuestions', 'master_active_question', 'active_question', 'active_question_answers', 'last_answered_answer_id', 'last_answered_question_id'];
                    document.cookie.split(';').forEach(function (c) {
                        var cookieName = c.split('=')[0].trim();
                        if (prefixes.some(function (p) { return cookieName.indexOf(p) === 0; })) {
                            document.cookie = cookieName + '=; Max-Age=-99999999; path=/';
                        }
                    });
                },
                retrieveStartedQuestions() {
                    if (this.checkCookie('startedQuestions')) {
                        let startedQuestions = JSON.parse(this.getCookie('startedQuestions'));
                        this.startedQuestions = startedQuestions;
                    }
                },
                retrieveActiveQuestion() {
                    // On reload, return the master to the question they had started.
                    if (this.checkCookie('master_active_question')) {
                        let questionId = parseInt(this.getCookie('master_active_question'), 10);
                        if (!isNaN(questionId) && this.game.questions.find(q => q.id === questionId)) {
                            this.selectQuestion(questionId);
                            // If we were mid-reveal (question shown, options not yet
                            // revealed), restore the Reveal Answers button.
                            if (this.checkCookie('master_question_only')) {
                                let onlyId = parseInt(this.getCookie('master_question_only'), 10);
                                if (onlyId === questionId) {
                                    this.questionOnlyActive = true;
                                }
                            }
                        }
                    }
                },
                retrieveAnsweredQuestions() {
                    if (this.checkCookie('answeredQuestions')) {
                        let answeredQuestions = JSON.parse(this.getCookie('answeredQuestions'));
                        this.answeredQuestions = answeredQuestions;
                    }
                },
            },
            computed: {
                selectedAction() {
                    if (this.selectedActionId == 'custom') {
                        return null;
                    }
                    return this.notificationActions.find(action => action.id == this.selectedActionId);
                },
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


                /*-----------------------------------------*/
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

                this.clearLegacyCookies();

                // On reload, resume the running timer on the button from the exact
                // remaining time (same source players use).
                GameApi.getQuestionTimer('{{ $gameInstance['token'] }}', (timer) => {
                    if (timer && timer.active && !timer.expired) {
                        this.startMasterTimer(timer.timeLimit, timer.timeLeft);
                    }
                });

                document.addEventListener('playerJoined', (e) => {
                    console.log('Player Joined Event', e.detail);
                    this.playerJoined(e.detail.player);
                });

                document.addEventListener('playerAnsweredEvent', (e) => {
                    console.log('Player Answered Event', e.detail);
                    this.playerAnswered(e.detail);
                });

                document.addEventListener('gameOverEvent', (e) => {
                    console.log('gameOverEvent', e.detail);
                    window.location.href = '/trivia/{{ $gameInstance['token'] }}/results';
                });

                GameApi.joinRoom('{{ $gameInstance['token'] }}');
                //GameApi.registerCallbackGameInstanceUpdated(callbackGameInstanceUpdated);
                //GameApi.registerCallbackPlayerJoined(this.playerJoined);
                this.retrieveStartedQuestions();
                this.retrieveAnsweredQuestions();
                this.retrieveActiveQuestion();

            }
        }).mount('#master-app');

    </script>

@endsection