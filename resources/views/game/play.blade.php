@extends('game-api::layout')
@section('body')

    <div id="timer-holder" class="hidden flex flex-col relative">
        <div id="timer-settings" class="flex flex-row justify-center bg-lime-300" style="width: 100%; height: 24.5px;">
        </div>
        <div class="w-full flex flex-row justify-center">
            <span id="timer-countdown-holder" class="bg-zinc-800 rounded-md text-xl text-slate-300 font-semibold">0</span>
        </div>
    </div>


    <x-section title="">
        <div class="flex flex-col py-4 px-2">
            <h1 id="question-holder" class="josefin-sans text-yellow-400 text-5xl text-center">Waiting For Question...</h1>
            <div>
                <p class="px-4 z-20 text-slate-400 text-center text-xl">Question <span id="current-question-number">{{ $remoteData['current_question'] }}</span> / {{ $totalQuestions }}</p>
            </div>
        </div>

        <div class="flex flex-col py-2 px-2 answer-holder">
        </div>

        <div class="flex flex-col justify-center w-24 h-24 bg-sky-700 rounded-full shadow-md text-center absolute bottom-2 right-2">
            <span class="josefin-sans font-semibold text-3xl text-slate-200"><x-points points="{{ $playerInstance['points']  }}"></x-points></span>
            <span class="raleway text-slate-200 font-semibold">Points</span>
        </div>

    </x-section>


    <script>

        let timeLimitTimer = null;
        let currentQuestion = '{{ $remoteData['current_question'] }}';

        function loadQuestion()
        {
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

                            /*
                                        <div class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold mb-2 w-full h-20 flex flex-col justify-center text-center text-2xl">
                <span class="text-slate-100" id="answer-a">Answer A</span>
            </div>
                             */
                            let answerButtonHolderDiv = document.createElement('button');
                            answerButtonHolderDiv.classList.add('py-2', 'px-4', 'shadow-md', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'mb-2', 'w-full', 'h-24', 'flex', 'flex-col', 'justify-center', 'text-center', 'text-2xl');
                            answerButtonHolderDiv.setAttribute('answer-id', answer.id);

                            let answerSpan = document.createElement('span');
                            answerSpan.classList.add('text-slate-100', 'w-full');
                            answerSpan.innerHTML = answer.answer;

                            answerButtonHolderDiv.appendChild(answerSpan);
                            answerButtonHolderDiv.setAttribute('onclick', 'answerQuestion(' + answer.id + ')');




                            //return
                            /*
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
                            */
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
                        let selectedAnswerButton = document.querySelector('.answer-holder button[answer-id="' + id + '"]');
                        selectedAnswerButton.classList.remove('bg-lime-500');
                        selectedAnswerButton.classList.remove('bg-slate-300');
                        selectedAnswerButton.classList.add('bg-yellow-500');

                        GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                        GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'id': window.id,'username' : window.username, 'avatar': @if(!Auth::check() || is_null(Auth::user()->avatar)) '/images/default-avatar.jpg' @else '{{Auth::user()->avatar}}' @endif}, 'action': 'playerAnsweredEvent'});
                    }
                })
                .catch(error => console.log(error));
        }

        function triggerTimer(initialTime)
        {
            //initialTime = 10;//just for debuging
            let timerHolder = document.getElementById('timer-holder');
            let timerBarHolder = document.getElementById('timer-settings');
            let timerCountdownHolder = document.getElementById('timer-countdown-holder');
            timerCountdownHolder.innerHTML = initialTime;

            timerHolder.classList.remove('hidden');
            //remove all classes from timer holder
            timerBarHolder.classList = [];
            timerBarHolder.classList.add('bg-lime-300');


            timerBarHolder.style.width = '100%';
            timerBarHolder.style.height = '24.5px';


            updateTimer(initialTime, initialTime, timerBarHolder, timerCountdownHolder);
        }

        function updateTimer(timeLeft, initialTime, timerBarHolder, timerCountdownHolder)
        {
            timerCountdownHolder.innerHTML = timeLeft;

            let timeLeftPercentage = (timeLeft / initialTime) * 100;
            timerBarHolder.style.width = timeLeftPercentage + '%';

            if (timeLeftPercentage < 75) {
                timerBarHolder.classList.remove('bg-lime-300');
                timerBarHolder.classList.add('bg-amber-200');
            }

            if (timeLeftPercentage < 55) {
                timerBarHolder.classList.remove('bg-amber-200');
                timerBarHolder.classList.add('bg-amber-300');
            }

            if (timeLeftPercentage < 30) {
                timerBarHolder.classList.remove('bg-amber-300');
                timerBarHolder.classList.add('bg-amber-400');
            }

            if (timeLeftPercentage < 20) {
                timerBarHolder.classList.remove('bg-amber-400');
                timerBarHolder.classList.add('bg-red-400');
            }

            if (timeLeftPercentage < 10) {
                timerBarHolder.classList.remove('bg-rose-400');
                timerBarHolder.classList.add('bg-rose-500');
            }

            if (timeLeftPercentage < 5) {
                timerBarHolder.classList.remove('bg-rose-500');
                timerBarHolder.classList.add('bg-rose-600');
            }

            if (timeLeft <= 0) {
                let answerHolder = document.querySelector('.answer-holder');
                answerHolder.innerHTML = "<div class='w-full fira-sans text-4xl text-center'><h1>TIME'S UP!</h1></div>";

                clearInterval(timeLimitTimer);
                return;
            }

            timeLimitTimer = setTimeout(() => {
                updateTimer(timeLeft - 1, initialTime, timerBarHolder, timerCountdownHolder);
            }, 1000);
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadQuestion();
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
            let answerHolder = document.querySelector('.answer-holder button[answer-id="' + e.detail['answer-id'] + '"]');
            answerHolder.classList.remove('bg-lime-500');
            answerHolder.classList.remove('bg-slate-300');
            answerHolder.classList.remove('bg-yellow-500');
            answerHolder.classList.add('bg-lime-500');

            GameApi.getPlayerPoints('{{ $gameInstance['token'] }}')
        });

    </script>
@endsection