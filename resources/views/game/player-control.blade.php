@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div id="player-app" style="top: -65px; ">


        <div v-if="currentView == 'game_created' " class="py-4">
            <x-section title="">
                <div class="flex flex-col relative w-full z-20">
                    <h1 class="px-4 z-20 text-center josefin-sans text-yellow-400 font-semibold text-6xl">[[ trivia.title ]]</h1>
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
                    <div class="w-full flex flex-row bg-zinc-800 shadow-md h-24 skew-y-3 absolute -bottom-10 z-10">
                    </div>
                </div>

                <div class="bg-zinc-700 pt-16 pb-6 relative z-10">
                    <x-card title="" addHeader="{{ false }}">
                        <div class="z-20 flex flex-col justify-around px-6">
                            <!-- TODO: Add animation -->
                            <p class="text-slate-200 font-semibold text-4xl text-center">Waiting <span class="text-yellow-500">[[ gameInstance.user.username ]]</span> to start the game!</p>
                        </div>
                    </x-card>

                    <div class="w-full flex flex-row bg-zinc-700 shadow-md h-24 skew-y-3 absolute -bottom-4 z-10">
                    </div>
                </div>

                <div class="bg-zinc-800 pt-16 pb-6 relative z-20">
                    <h2 class="px-4 z-20 text-center josefin-sans text-yellow-400 font-semibold text-4xl z-20 relative">Ways to join:</h2>
                    <div class="text-slate-200 font-semibold text-2xl px-6 py-2">
                        PIN: <span class="josefin-sans text-yellow-500 font-bold">[[ gameInstance.pin ]]</span>
                    </div>
                    <div class="text-slate-200 font-semibold text-2xl px-6" style="overflow-wrap: break-word;">
                        <p>Link: https://quizcrave.com/join/[[ gameInstance.token ]]</p>
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
                    <span id="timer-countdown-holder" class="bg-zinc-800 rounded-md text-xl text-slate-300 font-semibold">[[ timeLeft ]]</span>
                </div>
            </div>


            <x-section title="">
                <div class="flex flex-col py-4 px-2">
                    <h1 v-if="questionLoaded == 0" id="question-holder" class="josefin-sans text-yellow-400 text-5xl text-center">Waiting For Question...</h1>
                    <h1 v-else id="question-holder" class="josefin-sans text-yellow-400 text-4xl text-center">[[ question.question ]]</h1>
                </div>

                <div v-if="questionLoaded == 0" class="flex text-slate-200 text-xl text-center flex-col py-2 px-2 justify-center">
                    <!-- todo: change different quotes for waiting -->
                    <p>Give A moment and you will receive a question!</p>
                </div>
                <div v-else-if="questionLoaded == 1 && question.question_type == 'text_input' " class="flex text-slate-200 text-xl text-center flex-col py-2 px-2 justify-center">
                    <div v-if="(question.id == lastAnsweredQuestionId) || (correctAnswer != '' )" class="py-4">
                        <div>
                            <p class="text-3xl josefin-sans font-semibold text-slate-200">Your Answer: <span class="text-yellow-500">[[ correctInputTextAnswer ]]</span></p>
                            <p class="text-3xl josefin-sans font-semibold text-slate-200">Correct Answer: <span class="text-yellow-500">[[ correctAnswer ]]</span></p>
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
                    <div v-else>
                        <input v-model="correctInputTextAnswer" class="text-center bg-slate-100 py-2 px-2 text-4xl w-full border-2 border-slate-400 text-yellow-500 rounded shadow placeholder:text-gray-400 font-semibold"  type="text" id="correct_answer" name="correct_answer" placeholder="Correct Answer">
                        <button @click="answerInputText()" type="button" class="py-2 px-2 shadow-md text-left text-slate-100 text-3xl font-semibold mb-2 w-full rounded bg-lime-600 flex justify-center text-center mt-2">Submit Answer</button>
                    </div>
                </div>

                <div v-else class="flex flex-col py-2 px-2 answer-holder">
                    <button v-for="(answer, index) in answers" @click="answerQuestion(answer.id, index)"  v-bind:["answer-id"]="answer.id"  class="py-2 px-2 shadow-md text-left text-slate-100 text-3xl font-semibold mb-2 w-full rounded bg-lime-600 h-24 flex flex-col justify-center text-center" :class="{'bg-violet-600' : answer.is_correct, 'bg-amber-500' : !answer.is_correct && lastAnsweredAnswerId == answer.id,  }">
                        <span class="flex flex-row w-full josefin-sans">
                            <span v-if="index == 0" class="text-zinc-700">A)</span>
                            <span v-if="index == 1" class="text-zinc-700">B)</span>
                            <span v-if="index == 2" class="text-zinc-700">C)</span>
                            <span v-if="index == 3" class="text-zinc-700">D)</span>
                            <span v-if="index == 4" class="text-zinc-700">E)</span>
                            <span v-if="index == 5" class="text-zinc-700">F)</span>
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

        <div v-else-if="currentView == 'winners_view'" class="bg-zinc-900 h-screen flex flex-col justify-center">
            <div>
                <div class="py-2">
                    <h1 class="text-4xl josefin-sans text-slate-200 text-center">WINNER</h1>
                </div>
                <div class="px-2 py-2 md:px-6 md:py-2">
                    <p class="text-yellow-500 josefin-sans text-center text-6xl">[[ questionWinner ]]</p>
                </div>
            </div>
        </div>

        <div v-else-if="currentView == 'times_up'" class="bg-zinc-900 h-screen flex flex-col justify-center" style="margin-top: -65px;">
            <div class='w-full josefin-sans font-bold  text-yellow-500 text-4xl text-center'>
                <h1>TIME'S UP!</h1>
            </div>
        </div>

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

                    clearInterval(this.timeLimitTimer);

                    //clearInterval(timeLimitTimer);
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': answerId, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {

                                this.lastAnsweredQuestionId = this.question.id;
                                this.lastAnsweredAnswerId = answerId;

                                this.setCookie('last_answered_question_id', this.question.id, 1);
                                this.setCookie('last_answered_answer_id', answerId, 1);

                                GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'question_type' : this.question.question_type, 'indexId' : index, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            }
                        })
                        .catch(error => {
                            console.log(error)
                        });
                },
                answerInputText(answerId) {
                    if (this.question.id == this.lastAnsweredQuestionId) {
                        return;
                    }

                    clearInterval(this.timeLimitTimer);

                    //clearInterval(timeLimitTimer);
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_text': this.correctInputTextAnswer, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            this.setCookie('last_answered_question_id', this.question.id, 1);
                            if (data.success) {

                                this.lastAnsweredQuestionId = this.question.id;

                                GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'question_type' : this.question.question_type ,'answer_text' : this.correctInputTextAnswer, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            } else {
                                this.lastAnsweredQuestionId = this.question.id;
                            }
                        })
                        .catch(error => {
                            console.log(error)
                            this.setCookie('last_answered_question_id', this.question.id, 1);
                            this.lastAnsweredQuestionId = this.question.id;
                        });
                },
                triggerTimer(initialTime) {
                    this.startTimer = initialTime;
                    this.timeLeft = initialTime;

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
                setCookie(name, value, days) {
                    name = name + '_' + this.gameToken;
                    var expires = "";
                    if (days) {
                        var date = new Date();
                        date.setTime(date.getTime() + (days*24*60*60*1000));
                        expires = "; expires=" + date.toUTCString();
                    }
                    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
                },
                //add function to retrieve a cookie - accepts param name
                getCookie(name) {
                    name = name + '_' + this.gameToken;
                    console.log('get cookie', name);
                    var nameEQ = name + "=";
                    var ca = document.cookie.split(';');
                    for(var i=0;i < ca.length;i++) {
                        var c = ca[i];
                        while (c.charAt(0)==' ') c = c.substring(1,c.length);
                        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
                    }
                    return null;
                },
                //add function to erase a cookie - accepts param name
                eraseCookie(name) {
                    name = name + '_' + this.gameToken;
                    document.cookie = name+'=; Max-Age=-99999999;';
                },
                //add function to check if a cookie exists - accepts param name
                checkCookie(name) {
                    var cookie = this.getCookie(name);
                    if (cookie) {
                        return true;
                    } else {
                        return false;
                    }
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
                },
            },
            mounted() {

                this.preLoadInfoFromCookies();

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

                    this.changeView('question_view');

                    this.question = {
                        'id' : e.detail.id,
                        'question' : e.detail.question,
                        'question_type' : e.detail.question_type,
                    }
                    console.log(e.detail);
                    this.answers = e.detail.answers;


                    let activeQuestionJson = JSON.stringify(this.question);
                    let activeQuestionAnswersJson = JSON.stringify(this.answers);

                    this.setCookie('active_question', activeQuestionJson, 1);
                    this.setCookie('active_question_answers',  activeQuestionAnswersJson, 1);

                });

                document.addEventListener('startTimer', (e) => {
                    console.log(e);
                    clearInterval(this.timeLimitTimer);

                    this.timerMode = 1;
                    this.questionWinner = '';
                    this.questionLoaded = 1;
                    this.lastAnsweredAnswerId = null;

                    this.changeView('question_view');

                    this.question = {
                        'id' : e.detail.questionData.id,
                        'question' : e.detail.questionData.question,
                    }

                    this.answers = e.detail.questionData.answers;
                    this.triggerTimer(e.detail.timeLimit);
                });

                document.addEventListener("showCorrectAnswer", (e) => {


                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');

                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');

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

                document.addEventListener('userconnected', (e) => {
                    console.log('event userconnected', e.detail);
                    GameApi.joinRoom('{{ $gameInstance['token'] }}');
                });
            }
        }).mount('#player-app');

    </script>


@endsection