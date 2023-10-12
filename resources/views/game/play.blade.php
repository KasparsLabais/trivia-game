@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col">
            <div id="answered-players-holder">
            </div>
            <h2>
                @if(Auth::user()->id == $gameInstance['user_id'])
                    HOST: {{ Auth::user()->username }}
                @else
                    POINTS: <x-points points="{{ $playerInstance['points']  }}"></x-points>
                @endif
            </h2>
            <div class="bg-slate-300 px-6 py-8">
                <h1 id="question-holder">Waiting For Question...</h1>
            </div>
            <div class="bg-slate-200 px-6 py-8">
                <div class="answer-holder">
                </div>
            </div>
            <div>
                @if(Auth::user()->id == $gameInstance['user_id'])
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="nextQuestion()">Next Question</button>
                @endif
            </div>
        </div>
    </div>

    <script>

        GameApi.joinRoom('{{ $gameInstance['token'] }}');
        let currentQuestion = '{{ $remoteData['current_question'] }}';

        function loadQuestion()
        {
            fetch('/trv/trivia/{{ $gameInstance['token'] }}/question', {'method': 'GET', 'headers': {'Content-Type': 'application/json'}})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.success) {
                        let questionHolder = document.getElementById('question-holder');
                        currentQuestion = data.data.question_id;
                        questionHolder.innerHTML = data.data.question;
                        let answerHolder = document.querySelector('.answer-holder');
                        //clear answers holder before adding new answers
                        answerHolder.innerHTML = '';

                        data.data.answers.forEach(answer => {
                            let answerButton = document.createElement('button');
                            answerButton.classList.add('py-2', 'px-4', 'shadow-md', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'mr-2', 'mb-2');
                            answerButton.innerHTML = answer.answer;
                            answerButton.setAttribute('onclick', 'answerQuestion(' + answer.id + ')');
                            answerHolder.appendChild(answerButton);
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
            answerHolder.innerHTML = '<div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-64 w-64"></div>';

            fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': id, 'question_id': currentQuestion})})
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                        GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'username' : '{{ Auth::user()->username }}'}, 'action': 'playerAnsweredEvent'});

                        if (data.data.correct) {
                            answerHolder.innerHTML = '<h1>Correct!</h1>';
                        } else {
                            answerHolder.innerHTML = '<h1>Incorrect!</h1>';
                        }
                    }
                })
                .catch(error => console.log(error));
        }

        function nextQuestion()
        {
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

        document.addEventListener('DOMContentLoaded', () => {
            loadQuestion();
        });

        document.addEventListener('playerAnsweredEvent', (e) => {
            console.log('playerAnsweredEvent',e.detail);
            let playerHolder = document.getElementById('answered-players-holder');
            playerHolder.innerHTML = '<h1><span>' + e.detail.username + '</span> answered!</h1>';
        });

        document.addEventListener('nextQuestionEvent', (e) => {
            console.log('nextQuestionEvent', e.detail);
            currentQuestion = e.detail.question;
            loadQuestion();
        });

        document.addEventListener('gameOverEvent', (e) => {
            console.log('gameOverEvent', e.detail);
            window.location.href = '/trv/trivia/{{ $gameInstance['token'] }}/results';
        });

    </script>
@endsection