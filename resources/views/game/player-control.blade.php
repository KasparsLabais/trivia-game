@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div id="player-app">


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

            <div id="timer-holder" class="hidden flex flex-col relative">
                <div id="timer-settings" class="flex flex-row justify-center bg-lime-300" style="width: 100%; height: 24.5px;">
                </div>
                <div class="w-full flex flex-row justify-center">
                    <span id="timer-countdown-holder" class="bg-zinc-800 rounded-md text-xl text-slate-300 font-semibold">0</span>
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
        /*
        document.addEventListener('gameStarted', (e) => {
            console.log(e);
            window.location.href = '/trv/trivia/' + e.detail.gameToken;
        });

         */

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


    </script>

    <script>

        //"id":113,"user_id":"tCS8BRxmDRetKTy","game_instance_id":201,"points":0,"created_at":"2023-11-27T13:49:34.000000Z","updated_at":"2023-11-27T13:49:34.000000Z","status":"joined","remote_data":null,"user_type":"guest"},"message":"Player Instance found"},
        //}
        const { createApp } = Vue;
        createApp({
            data() {
                return {
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

                    //clearInterval(timeLimitTimer);
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': answerId, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {

                                this.lastAnsweredQuestionId = this.question.id;
                                this.lastAnsweredAnswerId = answerId;

                                /*
                                //disable all buttons and mark selected button with bg-yellow-600
                                let answerButtons = document.querySelectorAll('.answer-holder button');
                                answerButtons.forEach(answerButton => {
                                    answerButton.setAttribute('disabled', 'disabled');
                                    answerButton.classList.remove('bg-lime-500');
                                    answerButton.classList.add('bg-slate-300');
                                    answerButton.classList.remove('text-slate-100');
                                    answerButton.classList.add('text-slate-700');
                                });

                                //find selected button and mark it with bg-yellow-600
                                let selectedAnswerButton = document.querySelector('.answer-holder button[answer-id="' + id + '"]');
                                selectedAnswerButton.classList.remove('bg-lime-500');
                                selectedAnswerButton.classList.remove('bg-slate-300');
                                selectedAnswerButton.classList.add('bg-yellow-500');
                                 */


                                GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'indexId' : index, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            }
                        })
                        .catch(error => console.log(error));
                }
            },
            mounted() {
                document.addEventListener('gameStarted', (e) => {
                    console.log(e);
                    this.gameInstance.status = 'started';
                    this.changeView('question_view');
                    //window.location.href = '/trv/trivia/' + e.detail.gameToken;
                });

                document.addEventListener('startQuestion', (e) => {
                    console.log(e);

                    this.questionWinner = '';
                    this.questionLoaded = 1;
                    this.lastAnsweredAnswerId = null;

                    this.changeView('question_view');

                    this.question = {
                        'id' : e.detail.id,
                        'question' : e.detail.question,
                    }
                    this.answers = e.detail.answers;
                });

                document.addEventListener("showCorrectAnswer", (e) => {
                    console.log(e);
                    this.answers.forEach(answer => {
                        if (answer.id == e.detail['answer_id']) {
                            answer.is_correct = 1;
                        }
                    });
                });
                document.addEventListener('showWinningTeam', (e) => {
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
            }
        }).mount('#player-app');

    </script>


@endsection