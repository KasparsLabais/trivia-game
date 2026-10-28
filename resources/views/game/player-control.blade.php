@extends('game-api::layout')
@section('body')
    @php
        // Base URL for join links / QR codes. Defaults to the app's own URL;
        // override with GAME_JOIN_URL (config settings.join_url) for a short domain.
        $joinUrlBase = rtrim(config('settings.join_url') ?: url('/'), '/');
    @endphp

    @include('game-api::partials.game-notification')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div id="player-app" style="top: -65px; ">


        <div v-if="currentView == 'game_created' " class="py-4">
            <x-section title="">
                <div class="flex flex-col relative w-full z-20">
                    <h1 class="px-4 z-20 text-center josefin-sans text-main-accent font-semibold text-6xl">[[ trivia.title ]]</h1>
                    <p class="px-4 z-20 text-slate-400 text-center">(Hosted By  [[ gameInstance.user.username ]])</p>
                    <div class="z-20 flex flex-row justify-between px-6 py-2 text-slate-200">
                        <div>
                            <span class="raleway font-normal text-base">Category:</span>
                            <span class="raleway font-normal text-base capitalize">[[ trivia.category.name ]]</span>
                        </div>
                        <div>
                            <span class="raleway font-normal text-base">Difficulty:</span>
                            <span class="raleway font-semibold text-base capitalize" :class="{'text-amber-700': trivia.difficulty == 'medium', 'text-red-700': trivia.difficulty == 'hard', 'text-lime-600' : trivia.difficulty == 'easy'}">[[ trivia.difficulty ]]</span>
                        </div>
                    </div>
                    <p class="z-20 text-slate-400 text-center">[[ trivia.description ]]</p>
                    <div class="w-full flex flex-row bg-main-dark shadow-md h-24 skew-y-3 absolute -bottom-10 z-10">
                    </div>
                </div>

                <div class="bg-panel pt-16 pb-6 relative z-10">
                    <x-card title="" addHeader="{{ false }}">
                        <div class="z-20 flex flex-col justify-around px-6">
                            <!-- TODO: Add animation -->
                            <p class="text-slate-200 font-semibold text-4xl text-center">Waiting <span class="text-main-accent">[[ gameInstance.user.username ]]</span> to start the game!</p>
                        </div>
                    </x-card>

                    <div class="w-full flex flex-row bg-panel shadow-md h-24 skew-y-3 absolute -bottom-4 z-10">
                    </div>
                </div>

                <div class="bg-main-dark pt-16 pb-6 relative z-20">
                    <h2 class="px-4 z-20 text-center josefin-sans text-main-accent font-semibold text-4xl z-20 relative">Ways to join:</h2>
                    <div class="text-slate-200 font-semibold text-2xl px-6 py-2">
                        PIN: <span class="josefin-sans text-main-accent font-bold">[[ gameInstance.pin ]]</span>
                    </div>
                    <div class="text-slate-200 font-semibold text-2xl px-6" style="overflow-wrap: break-word;">
                        <p>Link: {{ $joinUrlBase }}/join/[[ gameInstance.token ]]</p>
                    </div>
                    <p class="text-center text-slate-200 text-2xl px-6 py-4 font-semibold">OR</p>
                    <div class="flex flex-row justify-center z-20 relative" id="qrcode"></div>
                </div>
            </x-section>
        </div>

        <div v-else-if="currentView == 'question_view'">

            <div id="timer-holder" :class="{'hidden': (timerMode == 0) }" class="flex flex-col relative">
                <div id="timer-settings" class="flex flex-row justify-center" :class="[timerClass]"  v-bind:["style"]="'width:' + timerSize + '; height: 24.5px;'">
                </div>
                <div class="w-full flex flex-row justify-center">
                    <span id="timer-countdown-holder" class="bg-main-dark rounded-md text-xl text-slate-300 font-semibold">[[ timeLeft ]]</span>
                </div>
            </div>


            <x-section title="">
                <div class="flex flex-col py-4 px-2">
                    <h1 v-if="questionLoaded == 0" id="question-holder" class="josefin-sans text-main-accent text-5xl text-center">Waiting For Question...</h1>
                    <h1 v-else id="question-holder" class="josefin-sans text-main-accent text-4xl text-center">[[ question.question ]]</h1>
                </div>

                <div v-if="questionLoaded == 0" class="flex text-slate-200 text-xl text-center flex-col py-2 px-2 justify-center">
                    <!-- todo: change different quotes for waiting -->
                    <p>Give A moment and you will receive a question!</p>
                </div>
                <div v-else-if="questionLoaded == 1 && question.question_type == 'text_input' " class="flex text-slate-200 text-xl text-center flex-col py-2 px-2 justify-center">
                    <div v-if="(question.id == lastAnsweredQuestionId) || (correctAnswer != '' )" class="py-4">
                        <div>
                            <p class="text-3xl josefin-sans font-semibold text-slate-200">Your Answer: <span class="text-main-accent">[[ correctInputTextAnswer ]]</span></p>
                            <p class="text-3xl josefin-sans font-semibold text-slate-200">Correct Answer: <span class="text-main-accent">[[ correctAnswer ]]</span></p>
                            <div v-if="correctAnswerFileUrl != ''" class="flex flex-row justify-center">
                                <template v-if="correctAnswerFileUrlType == 'image'">
                                    <img class="w-1/2" :src="correctAnswerFileUrl" alt="Correct Answer Image">
                                </template>
                                <template v-else-if="correctAnswerFileUrlType == 'video'">
                                    <video class="w-1/2" controls>
                                        <source :src="correctAnswerFileUrl" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="answersRevealed">
                        <input v-model="correctInputTextAnswer" class="text-center bg-slate-100 py-2 px-2 text-4xl w-full border-2 border-slate-400 text-main-accent rounded shadow placeholder:text-gray-400 font-semibold"  type="text" id="correct_answer" name="correct_answer" placeholder="Correct Answer">
                        <button @click="answerInputText()" type="button" class="py-2 px-2 shadow-md text-left text-slate-100 text-3xl font-semibold mb-2 w-full rounded bg-brand flex justify-center text-center mt-2">Submit Answer</button>
                    </div>
                    <div v-else class="py-6">
                        <h2 class="josefin-sans text-slate-200 text-3xl font-semibold">Get ready&hellip;</h2>
                        <p class="raleway text-slate-400 text-xl mt-2">You'll be able to answer in a moment!</p>
                    </div>
                </div>

                <div v-else-if="questionLoaded == 1 && question.question_type == 'open_input'" class="flex flex-col py-2 px-2">
                    {{-- After the host reveals: show everyone's submissions --}}
                    <div v-if="openSubmissionsRevealed">
                        <h2 class="text-slate-200 text-2xl text-center josefin-sans mb-3">Submitted Answers</h2>
                        <div class="flex flex-col gap-2">
                            <div v-for="sub in openSubmissions" class="bg-panel rounded px-3 py-2 flex flex-row items-center gap-3">
                                <span class="text-main-accent font-semibold raleway">[[ sub.username ]]</span>
                                <template v-if="sub.is_image">
                                    <img :src="sub.value" class="h-20 rounded border border-panel">
                                </template>
                                <template v-else>
                                    <span class="text-slate-200 text-xl break-words">[[ sub.value ]]</span>
                                </template>
                            </div>
                            <p v-if="openSubmissions.length == 0" class="text-slate-400 text-center py-4">No answers were submitted.</p>
                        </div>
                    </div>
                    {{-- Before submit: input field for the required format --}}
                    <div v-else-if="!openSubmitted">
                        <template v-if="question.input_type == 'image'">
                            <input type="file" accept="image/*" @change="onOpenImageSelected($event)" class="text-slate-200 w-full py-3">
                        </template>
                        <template v-else>
                            <input v-model="openInputValue" :type="question.input_type == 'number' ? 'number' : 'text'"
                                   class="text-center bg-slate-100 py-2 px-2 text-3xl w-full border-2 border-slate-400 text-yellow-600 rounded shadow font-semibold"
                                   :placeholder="question.input_type == 'number' ? 'Enter a number' : 'Your answer'">
                        </template>
                        <button @click="submitOpenInput()" type="button" class="py-2 px-2 shadow-md text-slate-100 text-3xl font-semibold mb-2 w-full rounded bg-brand flex justify-center text-center mt-2">Submit Answer</button>
                    </div>
                    {{-- After submit: waiting for the host to judge --}}
                    <div v-else class="text-center py-8">
                        <h2 class="text-main-accent text-3xl josefin-sans">Answer submitted!</h2>
                        <p class="text-slate-300 raleway mt-2">Waiting for the host&hellip;</p>
                    </div>
                </div>

                <div v-else class="flex flex-col py-2 px-2 answer-holder">
                    <div v-if="!answersRevealed" class="flex flex-col justify-center text-center py-10 px-2">
                        <h2 class="josefin-sans text-slate-200 text-3xl font-semibold">Get ready&hellip;</h2>
                        <p class="raleway text-slate-400 text-xl mt-2">The answer options are coming up!</p>
                    </div>
                    <button v-else v-for="(answer, index) in answers" @click="answerQuestion(answer.id, index)"  v-bind:["answer-id"]="answer.id"  class="py-2 px-2 shadow-md text-left text-slate-100 text-3xl font-semibold mb-2 w-full rounded h-24 flex flex-col justify-center text-center" :class="{'bg-green-600' : answer.is_correct, 'bg-main-accent' : !answer.is_correct && lastAnsweredAnswerId == answer.id, 'bg-brand' : !answer.is_correct && lastAnsweredAnswerId != answer.id}">
                        <span class="flex flex-row w-full josefin-sans">
                            <span v-if="index == 0" class="text-white">A)</span>
                            <span v-if="index == 1" class="text-white">B)</span>
                            <span v-if="index == 2" class="text-white">C)</span>
                            <span v-if="index == 3" class="text-white">D)</span>
                            <span v-if="index == 4" class="text-white">E)</span>
                            <span v-if="index == 5" class="text-white">F)</span>
                            [[ answer.answer ]]
                        </span>
                    </button>
                </div>

                <div class="flex flex-col justify-center w-24 h-24 bg-sky-700 rounded-full shadow-md text-center absolute bottom-2 right-2">
                    <span id="game-api-_points-holder" class="josefin-sans font-semibold text-3xl text-slate-200">[[ player.points ]]</span>
                    <span class="raleway text-slate-200 font-semibold">Points</span>
                </div>

            </x-section>
        </div>

        <div v-else-if="currentView == 'winners_view'" class="bg-main-dark h-screen flex flex-col justify-center">
            <div>
                <div class="py-2">
                    <h1 class="text-4xl josefin-sans text-slate-200 text-center">WINNER</h1>
                </div>
                <div class="px-2 py-2 md:px-6 md:py-2">
                    <p class="text-main-accent josefin-sans text-center text-6xl">[[ questionWinner ]]</p>
                </div>
            </div>
        </div>

        <div v-else-if="currentView == 'times_up'" class="bg-main-dark h-screen flex flex-col justify-center px-6" style="margin-top: -65px;">
            <div class='w-full josefin-sans font-bold text-main-accent text-4xl text-center'>
                <h1>TIME'S UP!</h1>
            </div>
            <div v-if="question" class="w-full text-center text-slate-200 text-2xl mt-6 px-4 raleway">
                [[ question.question ]]
            </div>
            <div class="w-full text-center text-slate-400 text-lg mt-4 raleway">
                Waiting for the correct answer&hellip;
            </div>
        </div>

        <div v-else-if="currentView == 'game_over'" class="bg-main-dark h-screen flex flex-col justify-center px-6" style="margin-top: -65px;">
            <div class="w-full text-center">
                <h1 class="josefin-sans font-bold text-main-accent text-5xl">GAME OVER</h1>
            </div>
            <div v-if="gameOver" class="w-full max-w-md mx-auto mt-8 text-slate-200 raleway">
                <div class="flex flex-row justify-between border-b border-panel py-2">
                    <span class="text-slate-400">Game</span><span class="font-semibold">[[ gameOver.name ]]</span>
                </div>
                <div class="flex flex-row justify-between border-b border-panel py-2">
                    <span class="text-slate-400">Questions</span><span class="font-semibold">[[ gameOver.questionCount ]]</span>
                </div>
                <div class="flex flex-row justify-between border-b border-panel py-2">
                    <span class="text-slate-400">Duration</span><span class="font-semibold">[[ gameOver.duration ]]</span>
                </div>
                <div class="flex flex-row justify-between border-b border-panel py-2">
                    <span class="text-slate-400">Players</span><span class="font-semibold">[[ gameOver.playerCount ]]</span>
                </div>
            </div>
            <div class="w-full flex flex-row justify-center mt-8">
                <a href="/" class="fira-sans py-3 px-8 rounded shadow-md bg-brand text-slate-100 font-semibold text-lg">Back Home</a>
            </div>
        </div>

    </div>




    <script src="/vendor/trivia-game/js/qrcode.min.js"></script>
    <script>
        var qrcode = new QRCode("qrcode", {
            text: "{{ $joinUrlBase }}/join/{{ $gameInstance['token'] }}",
            width: 168,
            height: 168,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        GameApi.joinRoom('{{ $gameInstance['token'] }}');


        //TODO: Convert callbackGameInstanceUpdated to event listener

        document.addEventListener('playerJoined', (e) => {
            playerJoinedUserView(game);
        });


    </script>

    <script>

        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    gameToken: '{{ $gameInstance['token'] }}',
                    correctInputTextAnswer: '',
                    correctAnswer: '',
                    correctAnswerFileUrl: '',
                    correctAnswerFileUrlType: 'image',
                    currentView: @if($gameInstance['status'] == 'created') 'game_created' @else 'question_view' @endif,
                    gameInstance: @json($gameInstance),
                    trivia: @json($trivia),
                    player: @json($player),
                    questionLoaded: 0,
                    question: null,
                    answers: [],
                    // When the game master shows a question "question only", the
                    // options stay hidden until they reveal them. Defaults to true
                    // so the normal "question + answers" flow is unchanged.
                    answersRevealed: true,
                    // open_input (judged free-form submission) state.
                    openInputValue: '',
                    openImageFile: null,
                    openSubmitted: false,
                    openSubmissions: [],
                    openSubmissionsRevealed: false,
                    lastAnsweredQuestionId: null,
                    lastAnsweredAnswerId: null,
                    answerSelected: null,
                    questionWinner: null,
                    timeLimitTimer: null,
                    startTimer: 0,
                    timeLeft: 0,
                    timerClass: 'bg-lime-300',
                    timerSize: '100%',
                    timerMode: 0,
                    gameOver: null,
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                changeView(view) {
                    this.currentView = view;
                },
                answerQuestion(answerId, index) {

                    if (this.question.id == this.lastAnsweredQuestionId) {
                        return;
                    }

                    // Give instant feedback and stop the timer up front, so the UI
                    // reflects the selection even if the request/response fails.
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.lastAnsweredQuestionId = this.question.id;
                    this.lastAnsweredAnswerId = answerId;
                    this.setCookie('last_answered_question_id', this.question.id, 1);
                    this.setCookie('last_answered_answer_id', answerId, 1);

                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': answerId, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.data && data.data.playerInstance) {
                                    GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                }
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'question_type' : this.question.question_type, 'indexId' : index, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            } else {
                                GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Could not submit your answer.');
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            GameApi.displayAlertNotification('error', 'Could not submit your answer.');
                        });
                },
                answerInputText(answerId) {
                    if (this.question.id == this.lastAnsweredQuestionId) {
                        return;
                    }

                    // Give instant feedback and stop the timer up front.
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.lastAnsweredQuestionId = this.question.id;
                    this.setCookie('last_answered_question_id', this.question.id, 1);

                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_text': this.correctInputTextAnswer, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.data && data.data.playerInstance) {
                                    GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                }
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'question_type' : this.question.question_type ,'answer_text' : this.correctInputTextAnswer, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            } else {
                                GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Could not submit your answer.');
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            GameApi.displayAlertNotification('error', 'Could not submit your answer.');
                        });
                },
                onOpenImageSelected(e) {
                    this.openImageFile = (e.target.files && e.target.files[0]) ? e.target.files[0] : null;
                },
                // Submit an open_input (judged) answer: string/number via JSON, or
                // an image via multipart to the temporary-image endpoint.
                submitOpenInput() {
                    if (this.openSubmitted) { return; }

                    if (this.question.input_type == 'image') {
                        if (!this.openImageFile) { GameApi.displayAlertNotification('error', 'Please choose an image first.'); return; }
                        let fd = new FormData();
                        fd.append('image', this.openImageFile);
                        fd.append('question_id', this.question.id);
                        this.openSubmitted = true;
                        fetch('/trv/trivia/{{ $gameInstance['token'] }}/submit-image', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: fd
                        }).then(r => r.json()).then(data => {
                            if (data.success) {
                                this.notifyMasterOpen(true, data.data.url);
                            } else {
                                this.openSubmitted = false;
                                GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Upload failed.');
                            }
                        }).catch(() => { this.openSubmitted = false; GameApi.displayAlertNotification('error', 'Upload failed.'); });
                        return;
                    }

                    let val = ('' + this.openInputValue).trim();
                    if (val === '') { return; }
                    this.openSubmitted = true;
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ answer_text: val, question_id: this.question.id })
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            this.notifyMasterOpen(false, val);
                        } else {
                            this.openSubmitted = false;
                            GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Could not submit your answer.');
                        }
                    }).catch(() => { this.openSubmitted = false; GameApi.displayAlertNotification('error', 'Could not submit your answer.'); });
                },
                notifyMasterOpen(isImage, value) {
                    GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', { 'data': {
                        'question_type': 'open_input',
                        'input_type': this.question.input_type,
                        'is_image': isImage,
                        'value': value,
                        'questionId': this.question.id,
                        'id': window.id,
                        'username': window.username
                    }, 'action': 'playerAnsweredEvent' });
                },
                triggerTimer(initialTime, currentTime) {
                    this.startTimer = initialTime;
                    // currentTime lets a reconnecting player resume from the
                    // remaining time instead of restarting from the beginning.
                    this.timeLeft = (typeof currentTime === 'undefined') ? initialTime : currentTime;

                    this.timerClass = 'bg-lime-300';
                    this.timerSize = '100%';

                    this.updateTimer();
                },
                updateTimer() {
                    let timeLeftPercentage = (this.timeLeft / this.startTimer) * 100;
                    this.timerSize = timeLeftPercentage + '%';

                    if (timeLeftPercentage < 75) {
                        this.timerClass = 'bg-amber-200';
                    }

                    if (timeLeftPercentage < 55) {
                        this.timerClass = 'bg-amber-300';
                    }

                    if (timeLeftPercentage < 30) {
                        this.timerClass = 'bg-amber-400';
                    }

                    if (timeLeftPercentage < 20) {
                        this.timerClass = 'bg-red-400';
                    }

                    if (timeLeftPercentage < 10) {
                        this.timerClass = 'bg-rose-500';
                    }

                    if (timeLeftPercentage < 5) {
                        this.timerClass = 'bg-rose-600';
                    }

                    if (this.timeLeft <= 0) {
                        this.changeView('times_up');
                        clearInterval(this.timeLimitTimer);
                        return;
                    }

                    this.timeLimitTimer = setTimeout(() => {
                        this.timeLeft = this.timeLeft - 1;
                        this.updateTimer();
                    }, 1000);
                },
                // Persisted in localStorage (not cookies) so it is never sent in
                // request headers — large cookies caused nginx "400 Request Header
                // Or Cookie Too Large" on reload.
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
                preLoadInfoFromCookies() {
                    if (this.checkCookie('last_answered_question_id')) {
                        this.lastAnsweredQuestionId = this.getCookie('last_answered_question_id');
                    }

                    if (this.checkCookie('last_answered_answer_id')) {
                        this.lastAnsweredAnswerId = this.getCookie('last_answered_answer_id');
                    }

                    if (this.checkCookie('active_question')) {
                        this.question = JSON.parse(this.getCookie('active_question'));

                        this.questionLoaded = 1;
                        this.changeView('question_view');

                        console.log('Active Question: ' + this.question);
                    }

                    if (this.checkCookie('active_question_answers')) {
                        this.answers = JSON.parse(this.getCookie('active_question_answers'));
                        console.log('Active Question Answers: ' + this.answers);
                    }

                    // Restore whether the options were revealed, so a reload during
                    // the "question only" phase keeps the options hidden.
                    if (this.checkCookie('active_question_revealed')) {
                        this.answersRevealed = this.getCookie('active_question_revealed') !== '0';
                    }
                },
            },
            mounted() {

                this.clearLegacyCookies();
                this.preLoadInfoFromCookies();

                // On (re)load, ask game-node whether a question timer is running and
                // resume it from the exact remaining time (or show expired).
                GameApi.getQuestionTimer(this.gameToken, (timer) => {
                    if (!timer || !timer.active) {
                        return;
                    }
                    // A running timer means the options are live, so restore the
                    // question AND its answers from the timer's (authoritative)
                    // data. The localStorage copy can still be the empty
                    // "question only" set from before the answers were revealed,
                    // which would otherwise leave the player with no options.
                    this.question = {
                        'id': timer.questionData.id,
                        'question': timer.questionData.question,
                        'question_type': timer.questionData.question_type,
                    };
                    this.answers = timer.questionData.answers || [];
                    this.questionLoaded = 1;
                    this.answersRevealed = true;

                    this.changeView('question_view');
                    this.timerMode = 1;

                    if (timer.expired) {
                        clearInterval(this.timeLimitTimer);
                        this.timeLeft = 0;
                        this.changeView('times_up');
                    } else {
                        this.triggerTimer(timer.timeLimit, timer.timeLeft);
                    }
                });

                document.addEventListener('gameStarted', (e) => {
                    console.log(e);
                    this.gameInstance.status = 'started';
                    this.changeView('question_view');
                    //window.location.href = '/trv/trivia/' + e.detail.gameToken;
                });

                document.addEventListener('startQuestion', (e) => {
                    console.log(e);

                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');
                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');

                    this.correctAnswer = '';
                    this.correctAnswerFileUrl = '';
                    this.correctAnswerFileUrlType = 'image';
                    this.timerMode = 0;
                    this.questionWinner = '';
                    this.questionLoaded = 1;
                    this.lastAnsweredAnswerId = null;
                    // answersHidden => the game master is showing the question
                    // only; keep the options hidden until they reveal them.
                    this.answersRevealed = e.detail.answersHidden ? false : true;

                    this.changeView('question_view');

                    // Reset open_input (judged) state for the new question.
                    this.openSubmitted = false;
                    this.openSubmissionsRevealed = false;
                    this.openInputValue = '';
                    this.openImageFile = null;
                    this.openSubmissions = [];

                    this.question = {
                        'id' : e.detail.id,
                        'question' : e.detail.question,
                        'question_type' : e.detail.question_type,
                        'input_type' : e.detail.input_type,
                    }
                    console.log(e.detail);
                    this.answers = e.detail.answers;


                    let activeQuestionJson = JSON.stringify(this.question);
                    let activeQuestionAnswersJson = JSON.stringify(this.answers);

                    this.setCookie('active_question', activeQuestionJson, 1);
                    this.setCookie('active_question_answers',  activeQuestionAnswersJson, 1);
                    this.setCookie('active_question_revealed', this.answersRevealed ? '1' : '0', 1);

                });

                // Game master reveals the answer options for a question that was
                // shown "question only". Fill in the options and let players answer.
                document.addEventListener('revealAnswers', (e) => {
                    console.log('revealAnswers', e.detail);
                    this.questionLoaded = 1;
                    this.changeView('question_view');
                    if (e.detail && e.detail.answers) {
                        this.answers = e.detail.answers;
                    }
                    this.answersRevealed = true;
                    this.setCookie('active_question_answers', JSON.stringify(this.answers), 1);
                    this.setCookie('active_question_revealed', '1', 1);
                });

                // open_input: the host revealed everyone's submissions.
                document.addEventListener('revealSubmissions', (e) => {
                    console.log('revealSubmissions', e.detail);
                    this.openSubmissions = (e.detail && e.detail.submissions) ? e.detail.submissions : [];
                    this.openSubmissionsRevealed = true;
                });

                document.addEventListener('startTimer', (e) => {
                    console.log(e);
                    clearInterval(this.timeLimitTimer);

                    this.timerMode = 1;
                    this.questionWinner = '';
                    this.questionLoaded = 1;
                    this.lastAnsweredAnswerId = null;
                    // A running timer always means the options are live.
                    this.answersRevealed = true;

                    this.changeView('question_view');

                    this.question = {
                        'id' : e.detail.questionData.id,
                        'question' : e.detail.questionData.question,
                        'question_type' : e.detail.questionData.question_type,
                    }

                    this.answers = e.detail.questionData.answers;
                    this.triggerTimer(e.detail.timeLimit);
                });

                document.addEventListener("showCorrectAnswer", (e) => {


                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');

                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');
                    this.eraseCookie('active_question_revealed');

                    console.log(e);
                    if (e.detail.question_type == 'text_input') {
                        this.correctAnswer = e.detail.answer_text;
                        this.correctAnswerFileUrl = e.detail.file_url;
                        this.correctAnswerFileUrlType = e.detail.file_url_type;
                    } else {
                        this.answers.forEach(answer => {
                            if (answer.id == e.detail['answer_id']) {
                                answer.is_correct = 1;
                            }
                        });
                    }
                });
                document.addEventListener('showWinningTeam', (e) => {

                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');

                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');
                    this.eraseCookie('active_question_revealed');

                    console.log(e);
                    this.changeView('winners_view');
                    this.questionWinner = e.detail.winnerName;
                });

                document.addEventListener('updatePoints', (e) => {
                    GameApi.getPlayerPoints('{{ $gameInstance['token'] }}');
                });

                document.addEventListener('updatePlayerPoints', (e) => {
                    console.log(e);
                    this.player.points = e.detail.points;
                });

                document.addEventListener('gameOverEvent', (e) => {
                    console.log('gameOverEvent', e.detail);
                    window.location.href = '/trivia/{{ $gameInstance['token'] }}/results';
                });

                document.addEventListener('gameClosed', (e) => {
                    console.log('gameClosed', e.detail);
                    var detail = e.detail || {};

                    // Stop the timer and show the Game Over screen with game details.
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.timerMode = 0;
                    this.gameOver = {
                        name: detail.name,
                        questionCount: detail.questionCount,
                        playerCount: detail.playerCount,
                        duration: detail.duration
                    };
                    this.changeView('game_over');

                    // Disappearing notification explaining why the game ended.
                    document.dispatchEvent(new CustomEvent('gameMasterNotification', { detail: { from: 'Game', message: detail.message || 'The host has ended the game.', colour: '#dc2626' } }));
                });

                document.addEventListener('userconnected', (e) => {
                    console.log('event userconnected', e.detail);
                    GameApi.joinRoom('{{ $gameInstance['token'] }}');
                });
            }
        }).mount('#player-app');

    </script>


@endsection