@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col mt-2 px-12  w-2/3">
            <div>
                <h1 class="fira-sans font-semibold text-2xl">{{ $trivia['title'] }}</h1>
                <hr>
                <div class="bg-slate-100 pb-4 rounded shadow">
                    <div class="flex flex-col px-2 py-4 bg-slate-200 shadow-md">
                        {{ csrf_field() }}
                        <div class="flex flex-row">
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="question">Question:</label>
                                <input class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" type="text" name="question" id="question">
                            </div>
                        </div>
                        <div class="flex flex-row px-2">
                            <button type="submit" class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="addQuestion()">Add Question</button>
                        </div>
                    </div>
                    <div class="flex flex-col px-4 mt-4">
                        <h2 class="raleway">Questions:</h2>
                        <div id="question-holder">
                        @foreach($questions as $question)
                            <div id="question-{{ $question['id'] }}"  class="flex flex-col bg-slate-200 shadow rounded mt-4">
                                <div class="flex flex-row bg-slate-300  py-2 px-2">
                                    <div class="px-1 cursor-pointer" onclick="moveUp()" data-questionid="{{ $question['id'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-lime-600 w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                     <span>
                                         Order Nr: {{ $question['order_nr'] }}
                                     </span>
                                    <div class="px-1 cursor-pointer" onclick="moveDown()" data-questionid="{{ $question['id'] }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-rose-600	w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </div>
                                    |
                                    <span class="text-xl px-4">{{ $question['question'] }}</span>
                                </div>
                                <div id="answer-holder-{{ $question['id'] }}" class="flex flex-row py-4">
                                @foreach($question->answers as $answer)
                                    <div class="@if($answer['is_correct']) border-lime-500 bg-lime-500 @else border-slate-500 bg-slate-500 @endif text-slate-100 font-semibold border-2 px-4 py-2 rounded shadow mx-1">
                                        {{ $answer['answer'] }}
                                    </div>
                                @endforeach
                                </div>
                                <div class="flex flex-row bg-slate-300 py-2 px-2">
                                    <div class="flex flex-row">
                                        <div class="flex flex-col px-2 py-1">
                                            <label class="raleway font-semibold text-sm" for="answer-{{ $question['id'] }}">Answer:</label>
                                            <input class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded" type="text" name="answer-{{ $question['id'] }}" id="answer-{{ $question['id'] }}">
                                        </div>
                                        <div class="flex flex-col px-2 py-1">
                                            <label class="raleway font-semibold text-sm" for="is_correct-{{ $question['id'] }}">Is Correct:</label>
                                            <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded" id="is_correct-{{ $question['id'] }}" name="is_correct-{{ $question['id'] }}">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="flex flex-col justify-center">
                                        <button data-questionid="{{ $question['id'] }}" onclick="addAnswer()" class="py-2 px-2 shadow-md bg-cyan-500 text-slate-100 font-semibold">Add Answer</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        let questionsList = {
            @foreach($questions as $question)
            '{{ $question['order_nr'] }}' :{ 'id' :{{ $question['id'] }}, 'order_nr':{{ $question['order_nr'] }}, 'question':'{{ $question['question'] }}', 'answers':[
                @foreach($question->answers as $answer)
                {
                    'id': {{ $answer['id'] }},
                    'answer': '{{ $answer['answer'] }}',
                    'is_correct': {{ $answer['is_correct'] }}
                },
                @endforeach
            ]},
        @endforeach
        };

const addQuestion = () => {
    console.log('add question');

    fetch('/trv/management/trivia/{{ $trivia['id'] }}/question', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question: document.querySelector('#question').value
        })
    }).then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.success) {
            let questionHolder = document.querySelector('#question-holder');
            let question = data.payload;

            let questionDiv = document.createElement('div');
            questionDiv.classList.add('flex', 'flex-col', 'bg-slate-200', 'shadow', 'rounded', 'mt-4');

            let questionHeader = document.createElement('div');
            questionHeader.classList.add('flex', 'flex-row', 'bg-slate-300', 'py-2', 'px-2');

            let questionHeaderNr = document.createElement('span');
            questionHeaderNr.innerText = 'Order Nr: ' + question.order_nr + ' | ';

            let questionHeaderQuestion = document.createElement('span');
            questionHeaderQuestion.classList.add('text-xl', 'px-4');
            questionHeaderQuestion.innerText = question.question;

            questionHeader.appendChild(questionHeaderNr);
            questionHeader.appendChild(questionHeaderQuestion);

            let answerHolder = document.createElement('div');
            answerHolder.classList.add('flex', 'flex-row', 'py-4');
            answerHolder.id = 'answer-holder-' + question.id;

            if (question.answers) {
                question.answers.forEach(answer => {
                    let answerDiv = document.createElement('div');
                    answerDiv.classList.add('border-slate-500', 'bg-slate-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
                    answerDiv.innerText = answer.answer;

                    answerHolder.appendChild(answerDiv);
                });
            }

            let questionFooter = document.createElement('div');
            questionFooter.classList.add('flex', 'flex-row', 'bg-slate-300', 'py-2', 'px-2');

            let questionFooterAnswer = document.createElement('div');
            questionFooterAnswer.classList.add('flex', 'flex-row');

            let questionFooterAnswerAnswer = document.createElement('div');
            questionFooterAnswerAnswer.classList.add('flex', 'flex-col', 'px-2', 'py-1');

            let questionFooterAnswerAnswerLabel = document.createElement('label');
            questionFooterAnswerAnswerLabel.classList.add('raleway', 'font-semibold', 'text-sm');
            questionFooterAnswerAnswerLabel.setAttribute('for', 'answer');
            questionFooterAnswerAnswerLabel.innerText = 'Answer:';

            let questionFooterAnswerAnswerInput = document.createElement('input');
            questionFooterAnswerAnswerInput.classList.add('bg-slate-100', 'border', 'border-zinc-400', 'shadow', 'shadow-zinc-400', 'rounded');
            questionFooterAnswerAnswerInput.setAttribute('type', 'text');
            questionFooterAnswerAnswerInput.setAttribute('name', 'answer-' + question.id);
            questionFooterAnswerAnswerInput.setAttribute('id', 'answer-' + question.id);

            questionFooterAnswerAnswer.appendChild(questionFooterAnswerAnswerLabel);
            questionFooterAnswerAnswer.appendChild(questionFooterAnswerAnswerInput);

            let questionFooterAnswerIsCorrect = document.createElement('div');
            questionFooterAnswerIsCorrect.classList.add('flex', 'flex-col', 'px-2', 'py-1');

            let questionFooterAnswerIsCorrectLabel = document.createElement('label');
            questionFooterAnswerIsCorrectLabel.classList.add('raleway', 'font-semibold', 'text-sm');
            questionFooterAnswerIsCorrectLabel.setAttribute('for', 'is_correct-' + question.id);
            questionFooterAnswerIsCorrectLabel.innerText = 'Is Correct:';

            let questionFooterAnswerIsCorrectSelect = document.createElement('select');
            questionFooterAnswerIsCorrectSelect.classList.add('bg-slate-100', 'border', 'border-zinc-400', 'shadow', 'shadow-zinc-400', 'rounded');
            questionFooterAnswerIsCorrectSelect.setAttribute('name', 'is_correct-' + question.id);
            questionFooterAnswerIsCorrectSelect.setAttribute('id', 'is_correct-' + question.id);

            let questionFooterAnswerIsCorrectSelectOptionNo = document.createElement('option');
            questionFooterAnswerIsCorrectSelectOptionNo.setAttribute('value', '0');
            questionFooterAnswerIsCorrectSelectOptionNo.innerText = 'No';

            let questionFooterAnswerIsCorrectSelectOptionYes = document.createElement('option');
            questionFooterAnswerIsCorrectSelectOptionYes.setAttribute('value', '1');
            questionFooterAnswerIsCorrectSelectOptionYes.innerText = 'Yes';

            questionFooterAnswerIsCorrectSelect.appendChild(questionFooterAnswerIsCorrectSelectOptionNo);
            questionFooterAnswerIsCorrectSelect.appendChild(questionFooterAnswerIsCorrectSelectOptionYes);

            questionFooterAnswerIsCorrect.appendChild(questionFooterAnswerIsCorrectLabel);
            questionFooterAnswerIsCorrect.appendChild(questionFooterAnswerIsCorrectSelect);

            questionFooterAnswer.appendChild(questionFooterAnswerAnswer);
            questionFooterAnswer.appendChild(questionFooterAnswerIsCorrect);

            let questionFooterAddAnswer = document.createElement('div');
            questionFooterAddAnswer.classList.add('flex', 'flex-col', 'justify-center');

            let questionFooterAddAnswerButton = document.createElement('button');
            questionFooterAddAnswerButton.classList.add('py-2', 'px-2', 'shadow-md', 'bg-cyan-500', 'text-slate-100', 'font-semibold');
            questionFooterAddAnswerButton.setAttribute('data-questionid', question.id);
            questionFooterAddAnswerButton.innerText = 'Add Answer';
            questionFooterAddAnswerButton.addEventListener('click', addAnswer);

            questionFooterAddAnswer.appendChild(questionFooterAddAnswerButton);

            questionFooter.appendChild(questionFooterAnswer);
            questionFooter.appendChild(questionFooterAddAnswer);

            questionDiv.appendChild(questionHeader);
            questionDiv.appendChild(answerHolder);
            questionDiv.appendChild(questionFooter);

            questionHolder.appendChild(questionDiv);
        }
    })
    .catch(error => console.log(error));
}

const addAnswer = () => {
    console.log('add answer');

    let questionId = event.target.getAttribute('data-questionid');

    fetch('/trv/management/trivia/{{ $trivia['id'] }}/question/' + questionId + '/answer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            answer: document.querySelector('#answer-'+questionId).value,
            is_correct: document.querySelector('#is_correct-'+questionId).value
        })
    }).then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.success) {

            let answerHolder = document.querySelector('#answer-holder-' + questionId);
            let answer = data.payload;

            let answerDiv = document.createElement('div');

            if(answer.is_correct == "1") {
                answerDiv.classList.add('border-lime-500', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
            } else {
                answerDiv.classList.add('border-slate-500', 'bg-slate-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
            }
            answerDiv.innerText = answer.answer;

            answerHolder.appendChild(answerDiv);
        }
    })
    .catch(error => console.log(error));
}

</script>
@endsection