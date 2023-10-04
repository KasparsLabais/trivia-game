@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col">
            <div class="bg-slate-300 px-6 py-8">
                <h1 id="question-holder">Waiting For Question...</h1>
            </div>
            <div class="bg-slate-200 px-6 py-8">
                <div class="answer-holder">
                </div>
            </div>
        </div>
    </div>

    <script>
        //let triviaId = '{{ $remoteData['trivia_id'] }}';
        let currentQuestion = '{{ $remoteData['current_question'] }}';

        fetch('/trv/trivia/{{ $gameInstance['token'] }}/question', {'method': 'GET', 'headers': {'Content-Type': 'application/json'}})
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.status) {
                    let questionHolder = document.getElementById('question-holder');
                    currentQuestion = data.data.question_id;
                    questionHolder.innerHTML = data.data.question;
                    let answerHolder = document.querySelector('.answer-holder');
                    data.data.answers.forEach(answer => {
                        let answerButton = document.createElement('button');
                        answerButton.classList.add('py-2', 'px-4', 'shadow-md', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'mr-2', 'mb-2');
                        answerButton.innerHTML = answer.answer;
                        answerButton.setAttribute('onclick', 'answerQuestion(' + answer.id + ')');
                        answerHolder.appendChild(answerButton);
                    });
                }
            })
            .catch(error => console.log(error));

        function answerQuestion(id) {
            //replace answer buttons with loading spinner
            let answerHolder = document.querySelector('.answer-holder');
            answerHolder.innerHTML = '<div class="loader ease-linear rounded-full border-8 border-t-8 border-gray-200 h-64 w-64"></div>';

            //first step to hit API to store given answer into table and get response back
            fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': id, 'question_id': currentQuestion})})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if (data.status) {

                        GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance, 'updateUserRemoteData');
                        //if answer was correct, display correct message
                        if (data.data.correct) {
                            answerHolder.innerHTML = '<h1>Correct!</h1>';
                        } else {
                            //if answer was incorrect, display incorrect message
                            answerHolder.innerHTML = '<h1>Incorrect!</h1>';
                        }
                    }
                })
                .catch(error => console.log(error));


            //second step to emit event to all players in game that answer was given
        }

    </script>
@endsection