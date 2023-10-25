@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col w-full px-4 md:px-none md:w-1/2">
            <h2 class="fire-sans font-normal text-lg">
                @if(Auth::user()->id == $gameInstance['user_id'])
                    Game Host: {{ Auth::user()->username }}
                @else
                    Your Points: <x-points points="{{ $playerInstance['points']  }}"></x-points>
                @endif
            </h2>
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
                @if(Auth::user()->id == $gameInstance['user_id'])
                    <button class="py-2 px-4 shadow-md bg-cyan-500 text-slate-100 font-semibold" onclick="showCorrectAnswer()">Show Correct Answer</button>
                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="nextQuestion()">Next Question</button>
                @endif
            </div>
            @if(Auth::user()->id == $gameInstance['user_id'])
                <h2 class="fire-sans font-normal text-lg">Players that have submitted answer:</h2>
            @endif
            <div id="answered-players-holder" class="flex flex-row">
                @if(Auth::user()->id == $gameInstance['user_id'])
                    @foreach($answeredUsers as $user)
                        <div class="px-1 py-1">
                            <div class="flex flex-col bg-slate-100 shadow-md py-2 px-2 rounded">
                                <div class="flex flex-row justify-center">
                                    <img src="@if(is_null($user->user->avatar)) /images/default-avatar.jpg @else{{$user->user->avatar}}@endif" class="w-14 h-14 rounded-full shadow-md border-2 border-slate-500" alt="avatar" />
                                </div>
                                <div class="flex flex-row justify-center">
                                    <div class="raleway font-semibold">{{ $user->user->username }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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

                            let answerButtonHolderDiv = document.createElement('div');
                            answerButtonHolderDiv.classList.add('flex', 'flex-col', 'justify-center', 'px-2', 'md:w-2/4', 'w-full');
                            answerButtonHolderDiv.setAttribute('answer-id', answer.id);

                            let answerButton = document.createElement('button');
                            answerButton.classList.add('py-2', 'px-4', 'shadow-md', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'mb-2', 'w-full');
                            answerButton.innerHTML = answer.answer;
                            @if(Auth::user()->id != $gameInstance['user_id'])
                            answerButton.setAttribute('onclick', 'answerQuestion(' + answer.id + ')');
                            @endif

                            answerButtonHolderDiv.appendChild(answerButton);
                            answerHolder.appendChild(answerButtonHolderDiv);
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
                        GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'username' : '{{ Auth::user()->username }}', 'avatar': @if(is_null(Auth::user()->avatar)) '/images/default-avatar.jpg' @else '{{Auth::user()->avatar}}' @endif}, 'action': 'playerAnsweredEvent'});

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
            let playerHolder = document.getElementById('answered-players-holder');
            playerHolder.innerHTML = '';

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
                    }
                })
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadQuestion();
        });

        document.addEventListener('playerAnsweredEvent', (e) => {
            console.log('playerAnsweredEvent',e.detail);
            let playerHolder = document.getElementById('answered-players-holder');

            let playerDivHolder = document.createElement('div');
            playerDivHolder.classList.add('px-1', 'py-1', 'md:w-1/6', 'w-3/6');

            let playerDiv = document.createElement('div');
            playerDiv.classList.add('flex', 'flex-col', 'py-2', 'px-2', 'rounded', 'bg-slate-100', 'shadow-md');

            let playerImageDiv = document.createElement('div');
            playerImageDiv.classList.add('flex', 'flex-row', 'justify-center');

            let playerImage = document.createElement('img');
            playerImage.classList.add('w-14', 'h-14', 'rounded-full', 'shadow-md', 'border-2', 'border-slate-500');
            playerImage.setAttribute('src', e.detail.avatar);

            playerImageDiv.appendChild(playerImage);

            let playerNicknameDiv = document.createElement('div');
            playerNicknameDiv.classList.add('flex', 'flex-row', 'justify-center', 'raleway', 'font-semibold');
            playerNicknameDiv.innerHTML = e.detail.username;

            playerDiv.appendChild(playerImageDiv);
            playerDiv.appendChild(playerNicknameDiv);

            playerDivHolder.appendChild(playerDiv);

            playerHolder.appendChild(playerDivHolder);
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