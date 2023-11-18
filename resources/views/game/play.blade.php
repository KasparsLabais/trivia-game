@extends('game-api::layout')
@section('body')

    <div class="flex flex-row justify-center">
        <div class="flex flex-col w-full px-4 md:px-none md:w-1/2">
            <div>
                <h1 class="fira-sans text-xl">{{ $gameInstance['title'] }}</h1>
                <div>Question <span id="current-question-number">{{ $remoteData['current_question'] }}</span> / {{ $totalQuestions }}</div>
            </div>
            <hr class="my-4">
            <h2 class="fire-sans font-normal text-lg">
                @if(Auth::check() && (Auth::user()->id == $gameInstance['user_id']))
                    Game Host: {{ Auth::user()->username }}
                @else
                    Your Points: <x-points points="{{ $playerInstance['points']  }}"></x-points>
                @endif
            </h2>
            <div id="timer-settings" class="hidden bg-lime-300" style="width: 100%; height: 8.5px;">
            </div>
            <div class="shadow-md">
                <div class="bg-slate-100 px-6 py-6">
                    <h1 id="question-holder" class="fira-sans text-xl text-center">Waiting For Question...</h1>
                </div>
                <div class="bg-slate-200 px-2 py-2 md:px-6 md:py-8">
                    <div class="answer-holder flex flex-wrap justify-between">
                    </div>
                </div>
            </div>
            <div class="py-4">
                @if(Auth::check() && (Auth::user()->id == $gameInstance['user_id']))
                    <button class="py-2 px-4 shadow-md bg-cyan-500 text-slate-100 font-semibold" onclick="showCorrectAnswer()">Show Correct Answer</button>
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="nextQuestion()">Next Question</button>
                @endif
            </div>
            @if(Auth::check() && (Auth::user()->id == $gameInstance['user_id']))
                <h2 class="fire-sans font-normal text-lg">Players:</h2>
            @endif
            <div id="answered-players-holder" class="flex flex-row">
                @if(Auth::check() && (Auth::user()->id == $gameInstance['user_id']))
                    @foreach($gameInstance->playerInstances as $player)
                        <div class="px-1 py-1 users-holder" id="user-holder-@if($player->user_type == 'guest'){{$player->user->tmp_user_id}}@else{{ $player->user->id }}@endif">
                            <div class="relative flex flex-col bg-slate-100 shadow-md py-2 px-2 rounded">
                                <div class="flex flex-row justify-center relative">
                                    @if(!is_null($player->user->iconFlair)) <img src="{{ $player->user->iconFlair->icon_url }}" class="w-6 h-6 opacity-30 absolute right-0"/> @endif
                                    <img src="@if(is_null($player->user->avatar)) /images/default-avatar.jpg @else{{$player->user->avatar}}@endif" class="opacity-30 w-14 h-14 rounded-full shadow-md border-2 border-slate-500" alt="avatar" />
                                </div>
                                <div class="flex flex-row justify-center">
                                    <div class="username-div raleway font-semibold text-slate-300">{{ $player->user->username }}</div>
                                </div>

                                <div class="answered-label hidden bg-rose-600 font-semibold fira-sans text-slate-100 text-sm absolute top-0 right-0 py-1 px-1 rounded">
                                    Answered
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <script>

        let timeLimitTimer = null;
        let currentQuestion = '{{ $remoteData['current_question'] }}';

        function loadQuestion()
        {

            clearAnsweredUsersDivs();
            clearInterval(timeLimitTimer);

            fetch('/trv/trivia/{{ $gameInstance['token'] }}/question', {'method': 'GET', 'headers': {'Content-Type': 'application/json'}})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        let questionHolder = document.getElementById('question-holder');
                        currentQuestion = data.data.question_id;
                        questionHolder.innerHTML = data.data.question;

                        let answerHolder = document.querySelector('.answer-holder');
                        answerHolder.innerHTML = '';

                        data.data.answers.forEach(answer => {

                            let answerButtonHolderDiv = document.createElement('div');
                            answerButtonHolderDiv.classList.add('flex', 'flex-col', 'justify-center', 'px-2', 'md:w-2/4', 'w-full');
                            answerButtonHolderDiv.setAttribute('answer-id', answer.id);

                            let answerButton = document.createElement('button');
                            answerButton.classList.add('py-2', 'px-4', 'shadow-md', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'mb-2', 'w-full');
                            answerButton.innerHTML = answer.answer;

                            @if(Auth::check() && (Auth::user()->id != $gameInstance['user_id']))
                            answerButton.setAttribute('onclick', 'answerQuestion(' + answer.id + ')');
                            @else
                            answerButton.setAttribute('onclick', 'answerQuestion(' + answer.id + ')');
                            @endif

                            answerButtonHolderDiv.appendChild(answerButton);
                            answerHolder.appendChild(answerButtonHolderDiv);
                        });

                        data.data.settings.forEach(setting => {
                            if (setting.key == 'time_limit_enabled') {
                                if (setting.value == 1) {
                                    let timeLimitForQuestion = data.data.settings.find(setting => setting.key == 'time_per_question');
                                    if(typeof (timeLimitForQuestion) == 'undefined') {
                                        triggerTimer(60);
                                    } else {
                                        triggerTimer(timeLimitForQuestion.value)
                                    }
                                }
                            }
                        });

                    } else {
                        let questionHolder = document.getElementById('question-holder');
                        questionHolder.innerHTML = data.data.question;

                        let answerHolder = document.querySelector('.answer-holder');
                        answerHolder.innerHTML = '<h1>Waiting for next question...</h1>';
                    }
                })
                .catch(error => console.log(error));
        }

        function answerQuestion(id) {
            let answerHolder = document.querySelector('.answer-holder');
            //answerHolder.innerHTML = '<div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-64 w-64"></div>';

            clearInterval(timeLimitTimer);
            fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': id, 'question_id': currentQuestion})})
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

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
                        let selectedAnswerButton = document.querySelector('.answer-holder div[answer-id="' + id + '"] button');
                        selectedAnswerButton.classList.remove('bg-lime-500');
                        selectedAnswerButton.classList.remove('bg-slate-300');
                        selectedAnswerButton.classList.add('bg-yellow-500');


                        GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                        GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'id': window.id,'username' : window.username, 'avatar': @if(!Auth::check() || is_null(Auth::user()->avatar)) '/images/default-avatar.jpg' @else '{{Auth::user()->avatar}}' @endif}, 'action': 'playerAnsweredEvent'});

                        if (data.data.correct) {
                            //answerHolder.innerHTML = '<h1>Correct!</h1>';
                        } else {
                            //answerHolder.innerHTML = '<h1>Incorrect!</h1>';
                        }
                    }
                })
                .catch(error => console.log(error));
        }

        function nextQuestion()
        {
            //let playerHolder = document.getElementById('answered-players-holder');
            //playerHolder.innerHTML = '';

            fetch('/trv/trivia/{{ $gameInstance['token'] }}/next', {'method': 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        switch (data.data.event) {
                            case 'gameOverEvent':
                                GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {}, 'action': 'gameOverEvent'});
                                break;
                            case 'nextQuestionEvent':
                                GameApi.updateGameInstance('{{ $gameInstance['token'] }}', data.data.gameInstance)
                                GameApi.notifyRoom('{{ $gameInstance['token'] }}', {payload: {'question': data.data.question}, 'action': 'nextQuestionEvent'});
                                break;
                        }
                    }
                })
                .catch(error => console.log(error));

            //currentQuestion+=1;
            //GameApi.notifyRoom('{{ $gameInstance['token'] }}', {data: {'question': currentQuestion}, 'action': 'nextQuestionEvent'});
        }


        function showCorrectAnswer() {

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
        }

        function triggerTimer(initialTime)
        {
            //initialTime = 10;//just for debuging
            let timerHolder = document.getElementById('timer-settings');

            //remove all classes from timer holder
            timerHolder.classList = [];
            timerHolder.classList.add('bg-lime-300');


            timerHolder.style.width = '100%';
            timerHolder.style.height = '8.5px';

            updateTimer(initialTime, initialTime, timerHolder);
        }

        function updateTimer(timeLeft, initialTime, timerHolder)
        {
            let timeLeftPercentage = (timeLeft / initialTime) * 100;
            timerHolder.style.width = timeLeftPercentage + '%';

            if (timeLeftPercentage < 75) {
                timerHolder.classList.remove('bg-lime-300');
                timerHolder.classList.add('bg-amber-200');
            }

            if (timeLeftPercentage < 55) {
                timerHolder.classList.remove('bg-amber-200');
                timerHolder.classList.add('bg-amber-300');
            }

            if (timeLeftPercentage < 30) {
                timerHolder.classList.remove('bg-amber-300');
                timerHolder.classList.add('bg-amber-400');
            }

            if (timeLeftPercentage < 20) {
                timerHolder.classList.remove('bg-amber-400');
                timerHolder.classList.add('bg-red-400');
            }

            if (timeLeftPercentage < 10) {
                timerHolder.classList.remove('bg-rose-400');
                timerHolder.classList.add('bg-rose-500');
            }

            if (timeLeftPercentage < 5) {
                timerHolder.classList.remove('bg-rose-500');
                timerHolder.classList.add('bg-rose-600');
            }

            if (timeLeft <= 0) {


                @if(Auth::check() && (Auth::user()->id != $gameInstance['user_id']))
                let answerHolder = document.querySelector('.answer-holder');
                answerHolder.innerHTML = "<div class='w-full fira-sans text-4xl text-center'><h1>TIME'S UP!</h1></div>";
                @else
                let answerHolder = document.querySelector('.answer-holder');
                answerHolder.innerHTML = "<div class='w-full fira-sans text-4xl text-center'><h1>TIME'S UP!</h1></div>";
                @endif

                clearInterval(timeLimitTimer);
                return;
            }

            timeLimitTimer = setTimeout(() => {
                updateTimer(timeLeft - 1, initialTime, timerHolder);
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadQuestion();
        });

        document.addEventListener('playerAnsweredEvent', (e) => {

            console.log(e.detail);
            //find user holder with id user-holder- e.detail.id and change image opacity
            let userHolder = document.getElementById('user-holder-' + e.detail.id);
            userHolder.querySelector('img').classList.add('opacity-100');
            userHolder.querySelector('img').classList.remove('opacity-30');

            //change for user text color from text-slate-100 to text-slate-700
            userHolder.querySelector('.username-div').classList.remove('text-slate-100');
            userHolder.querySelector('.username-div').classList.add('text-slate-700');

            //remove class hidden from answered-label for user holder
            userHolder.querySelector('.answered-label').classList.remove('hidden');
        });

        document.addEventListener('nextQuestionEvent', (e) => {
            console.log('nextQuestionEvent', e.detail);
            currentQuestion = e.detail.question;

            let questionNumberHolder = document.getElementById('current-question-number');
            questionNumberHolder.innerHTML = e.detail.question;

            loadQuestion();
        });

        document.addEventListener('gameOverEvent', (e) => {
            console.log('gameOverEvent', e.detail);
            window.location.href = '/trv/trivia/{{ $gameInstance['token'] }}/results';
        });

        document.addEventListener('userconnected', (e) => {
            console.log('event userconnected', e.detail);
            GameApi.joinRoom('{{ $gameInstance['token'] }}');
        });

        document.addEventListener('showCorrectAnswer', (e) => {
            console.log('showCorrectAnswer', e.detail);
            let answerHolder = document.querySelector('.answer-holder div[answer-id="' + e.detail['answer-id'] + '"] button');
            answerHolder.classList.remove('bg-lime-500');
            answerHolder.classList.remove('bg-slate-300');
            answerHolder.classList.remove('bg-yellow-500');
            answerHolder.classList.add('bg-lime-500');

            GameApi.getPlayerPoints('{{ $gameInstance['token'] }}')
        });

        function clearAnsweredUsersDivs()
        {
            let userHolders = document.querySelectorAll('.users-holder');
            userHolders.forEach(userHolder => {

                userHolder.querySelector('img').classList.remove('opacity-100');
                userHolder.querySelector('img').classList.add('opacity-30');

                userHolder.querySelector('.username-div').classList.remove('text-slate-700');
                userHolder.querySelector('.username-div').classList.add('text-slate-100');

                userHolder.querySelector('.answered-label').classList.add('hidden');
            });
        }

    </script>
@endsection