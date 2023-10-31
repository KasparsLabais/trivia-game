@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center" id="question-editor">
        <div class="flex flex-col mt-2 px-12  w-2/3">
            <div>
                <h1 class="fira-sans font-semibold text-2xl">{{ $trivia['title'] }}</h1>
                <hr>
                <div class="bg-slate-100 rounded shadow">

                    <form action="" method="POST" class="flex flex-col px-2 py-4 bg-slate-200 shadow">
                        {{ csrf_field() }}
                        <div class="flex flex-row">
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="title">Title:</label>
                                <input value="{{ $trivia['title'] }}" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" type="text" name="title" id="title">
                            </div>
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="question">Description:</label>
                                <input value="{{ $trivia['description'] }}"  class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" type="text" name="description" id="description">
                            </div>
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="difficulty">Difficulty</label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="difficulty" id="difficulty">
                                    <option value="easy" @if($trivia['difficulty'] == 'easy') selected="selected" @endif>Easy</option>
                                    <option value="medium" @if($trivia['difficulty'] == 'medium') selected="selected" @endif>Medium</option>
                                    <option value="hard" @if($trivia['difficulty'] == 'hard') selected="selected" @endif>Hard</option>
                                </select>
                            </div>
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="category">Category</label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="category" id="category">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat['id'] }}" @if($trivia['category_id'] == $cat['id']) selected="selected" @endif>{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="is_active">Is Active: </label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="is_active" id="is_active">
                                    <option value="0" @if($trivia['is_active'] == 0) selected="selected" @endif>0</option>
                                    <option value="1" @if($trivia['is_active'] == 1) selected="selected" @endif>1</option>
                                </select>
                            </div>

                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="is_active">Private: </label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="private" id="private">
                                    <option value="0" @if($trivia['private'] == 0) selected="selected" @endif>0</option>
                                    <option value="1" @if($trivia['private'] == 1) selected="selected" @endif>1</option>
                                </select>
                            </div>

                        </div>
                        <div class="flex flex-row px-2">
                            <button type="submit" class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold">Save</button>
                        </div>
                    </form>

                    <div class="flex flex-col px-4 mt-4">
                        <h2 class="raleway">Questions:</h2>
                        <div id="question-holder">
                        @foreach($questions as $question)
                            <div id="question-{{ $question['id'] }}"  class="flex flex-col bg-slate-200 shadow rounded mt-4">
                                <div class="flex flex-row bg-slate-300  py-2 px-2">
                                    <div id="move-question-up-{{ $question['id'] }}" class="px-1 cursor-pointer" onclick="moveQuestionOrderNrUp()" data-questionid="{{ $question['id'] }}">
                                        <svg data-questionid="{{ $question['id'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-lime-600 w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                     <span>
                                         Order Nr: {{ $question['order_nr'] }}
                                     </span>
                                    <div id="move-question-down-{{ $question['id'] }}" class="px-1 cursor-pointer" onclick="moveQuestionOrderNrDown()" data-questionid="{{ $question['id'] }}">
                                        <svg data-questionid="{{ $question['id'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-rose-600	w-6 h-6">
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

                    <div class="flex flex-col px-2 py-4 bg-slate-200 shadow-md mt-4">
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
                </div>
            </div>
        </div>
    </div>

    <script>

        let questionsList = {
            @foreach($questions as $question)
            '{{ $question['id'] }}' :{ 'id' :{{ $question['id'] }}, 'order_nr':{{ $question['order_nr'] }}, 'question':'{{ $question['question'] }}', 'answers':[
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
                document.querySelector('#question').value = '';
                console.log(data);
                if (data.success) {
                    let questionHolder = document.querySelector('#question-holder');
                    let question = data.payload;

                    //add question to questionsList
                    console.log("QUESTION LIST", questionsList);
                    questionsList[question.id] = question;
                    questionsList[question.id]['answers'] = [];
                    console.log("QUESTION LIST New", questionsList);


                    let questionDiv = document.createElement('div');
                    questionDiv.setAttribute('id', 'question-' + question.id);
                    questionDiv.classList.add('flex', 'flex-col', 'bg-slate-200', 'shadow', 'rounded', 'mt-4');

                    let questionHeader = document.createElement('div');
                    questionHeader.classList.add('flex', 'flex-row', 'bg-slate-300', 'py-2', 'px-2');

                    let questionHeaderUpButton = document.createElement('div');
                    questionHeaderUpButton.classList.add('px-1', 'cursor-pointer');
                    questionHeaderUpButton.setAttribute('data-questionid', question.id);
                    questionHeaderUpButton.id = 'move-question-up-' + question.id;
                    questionHeaderUpButton.addEventListener('click', moveQuestionOrderNrUp);

                    let questionHeaderUpButtonSvg = document.createElement('svg');
                    questionHeaderUpButtonSvg.classList.add('stroke-lime-600', 'w-6', 'h-6');
                    questionHeaderUpButtonSvg.setAttribute('data-questionid', question.id);
                    questionHeaderUpButtonSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                    questionHeaderUpButtonSvg.setAttribute('fill', 'none');
                    questionHeaderUpButtonSvg.setAttribute('viewBox', '0 0 24 24');
                    questionHeaderUpButtonSvg.setAttribute('stroke-width', '1.5');
                    questionHeaderUpButtonSvg.setAttribute('stroke', 'currentColor');
                    questionHeaderUpButtonSvg.setAttribute('class', 'stroke-lime-600 w-6 h-6');
                    questionHeaderUpButtonSvg.setAttribute('stroke-linecap', 'round');
                    questionHeaderUpButtonSvg.setAttribute('stroke-linejoin', 'round');
                    questionHeaderUpButtonSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />';

                    questionHeaderUpButton.appendChild(questionHeaderUpButtonSvg);

                    let questionHeaderDownButton = document.createElement('div');
                    questionHeaderDownButton.classList.add('px-1', 'cursor-pointer');
                    questionHeaderDownButton.setAttribute('data-questionid', question.id);
                    questionHeaderDownButton.id = 'move-question-down-' + question.id;
                    questionHeaderDownButton.addEventListener('click', moveQuestionOrderNrDown);

                    let questionHeaderDownButtonSvg = document.createElement('svg');
                    questionHeaderDownButtonSvg.classList.add('stroke-rose-600', 'w-6', 'h-6');
                    questionHeaderDownButtonSvg.setAttribute('data-questionid', question.id);
                    questionHeaderDownButtonSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                    questionHeaderDownButtonSvg.setAttribute('fill', 'none');
                    questionHeaderDownButtonSvg.setAttribute('viewBox', '0 0 24 24');
                    questionHeaderDownButtonSvg.setAttribute('stroke-width', '1.5');
                    questionHeaderDownButtonSvg.setAttribute('stroke', 'currentColor');
                    questionHeaderDownButtonSvg.setAttribute('class', 'stroke-rose-600	w-6 h-6');
                    questionHeaderDownButtonSvg.setAttribute('stroke-linecap', 'round');
                    questionHeaderDownButtonSvg.setAttribute('stroke-linejoin', 'round');
                    questionHeaderDownButtonSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />';

                    questionHeaderDownButton.appendChild(questionHeaderDownButtonSvg);

                    let questionHeaderNr = document.createElement('span');
                    questionHeaderNr.innerText = 'Order Nr: ' + question.order_nr;

                    let questionHeaderQuestion = document.createElement('span');
                    questionHeaderQuestion.classList.add('text-xl', 'px-4');
                    questionHeaderQuestion.innerText = question.question;

                    let questionHeaderSeparator = document.createElement('span');
                    questionHeaderSeparator.innerText = ' | ';

                    questionHeader.appendChild(questionHeaderUpButton);
                    questionHeader.appendChild(questionHeaderNr);
                    questionHeader.appendChild(questionHeaderDownButton);
                    questionHeader.appendChild(questionHeaderSeparator);
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

                    $("#move-question-up-" + question.id).html($("#move-question-up-" + question.id).html());
                    $("#move-question-down-" + question.id).html($("#move-question-down-" + question.id).html());
                }
            })
            .catch(error => console.log(error));
        }

        const addAnswer = () => {
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

                    //add answer to questionsList
                    questionsList[questionId]['answers'].push(answer);
                }

                document.querySelector('#answer-' + questionId).value = '';
                document.querySelector('#is_correct-'+questionId).value = 0;

            })
            .catch(error => console.log(error));
        }

        const moveQuestionOrderNrUp = (e) => {

            let questionId = event.target.getAttribute('data-questionid');
            let question = questionsList[questionId];

            if (question['order_nr'] == 0) {
                return;
            }

            question['order_nr'] = question['order_nr'] - 1;

            let questionToReplace = Object.values(questionsList).find(q => q['order_nr'] == question['order_nr']);
            questionToReplace['order_nr'] = questionToReplace['order_nr'] + 1;

            questionsList[questionToReplace['id']] = questionToReplace;
            questionsList[question['id']] = question;

            updateQuestionOrderNr(question['id'], question['order_nr']);
            updateQuestionOrderNr(questionToReplace['id'], questionToReplace['order_nr']);

            redrawQuestionsUI();
        }

        const moveQuestionOrderNrDown = () => {
            let questionId = event.target.getAttribute('data-questionid');
            let question = questionsList[questionId];

            let totalQuestions = Object.keys(questionsList).length;

            if (question['order_nr'] >= totalQuestions) {
                return;
            }

            question['order_nr'] = question['order_nr'] + 1;

            //find question with order_nr + 1 and replace by current questions old order_nr
            let questionToReplace = Object.values(questionsList).find(q => q['order_nr'] == question['order_nr']);
            questionToReplace['order_nr'] = questionToReplace['order_nr'] - 1;
            //update questionList with new values
            questionsList[questionToReplace['id']] = questionToReplace;
            questionsList[question['id']] = question;

            updateQuestionOrderNr(question['id'], question['order_nr']);
            updateQuestionOrderNr(questionToReplace['id'], questionToReplace['order_nr']);
            //order questionList by order_nr
            redrawQuestionsUI();
        }

        const updateQuestionOrderNr = (questionId, orderNr) => {
            fetch('/trv/management/trivia/{{ $trivia['id'] }}/question/' + questionId + '/order', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    order_nr: orderNr
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {
                    console.log('question order nr updated');
                }
            })
        }

        const redrawQuestionsUI = () => {
            //redraw content of question-holder from questionsList
            let questionHolder = document.querySelector('#question-holder');
            questionHolder.innerHTML = '';

            let orderedQuestionsList = Object.values(questionsList).sort((a, b) => a['order_nr'] - b['order_nr']);
            orderedQuestionsList.map((question) => {

                let questionDiv = document.createElement('div');
                questionDiv.classList.add('flex', 'flex-col', 'bg-slate-200', 'shadow', 'rounded', 'mt-4');

                let questionHeader = document.createElement('div');
                questionHeader.classList.add('flex', 'flex-row', 'bg-slate-300', 'py-2', 'px-2');

                let questionHeaderUpButton = document.createElement('div');
                questionHeaderUpButton.classList.add('px-1', 'cursor-pointer');
                questionHeaderUpButton.setAttribute('data-questionid', question.id);
                questionHeaderUpButton.id = 'move-question-up-' + question.id;
                questionHeaderUpButton.addEventListener('click', moveQuestionOrderNrUp);

                let questionHeaderUpButtonSvg = document.createElement('svg');
                questionHeaderUpButtonSvg.classList.add('stroke-lime-600', 'w-6', 'h-6');
                questionHeaderUpButtonSvg.setAttribute('data-questionid', question.id);
                questionHeaderUpButtonSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                questionHeaderUpButtonSvg.setAttribute('fill', 'none');
                questionHeaderUpButtonSvg.setAttribute('viewBox', '0 0 24 24');
                questionHeaderUpButtonSvg.setAttribute('stroke-width', '1.5');
                questionHeaderUpButtonSvg.setAttribute('stroke', 'currentColor');
                questionHeaderUpButtonSvg.setAttribute('class', 'stroke-lime-600 w-6 h-6');
                questionHeaderUpButtonSvg.setAttribute('stroke-linecap', 'round');
                questionHeaderUpButtonSvg.setAttribute('stroke-linejoin', 'round');
                questionHeaderUpButtonSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />';

                questionHeaderUpButton.appendChild(questionHeaderUpButtonSvg);

                let questionHeaderDownButton = document.createElement('div');
                questionHeaderDownButton.classList.add('px-1', 'cursor-pointer');
                questionHeaderDownButton.setAttribute('data-questionid', question.id);
                questionHeaderDownButton.id = 'move-question-down-' + question.id;
                questionHeaderDownButton.addEventListener('click', moveQuestionOrderNrDown);

                let questionHeaderDownButtonSvg = document.createElement('svg');
                questionHeaderDownButtonSvg.classList.add('stroke-rose-600', 'w-6', 'h-6');
                questionHeaderDownButtonSvg.setAttribute('data-questionid', question.id);
                questionHeaderDownButtonSvg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                questionHeaderDownButtonSvg.setAttribute('fill', 'none');
                questionHeaderDownButtonSvg.setAttribute('viewBox', '0 0 24 24');
                questionHeaderDownButtonSvg.setAttribute('stroke-width', '1.5');
                questionHeaderDownButtonSvg.setAttribute('stroke', 'currentColor');
                questionHeaderDownButtonSvg.setAttribute('class', 'stroke-rose-600	w-6 h-6');
                questionHeaderDownButtonSvg.setAttribute('stroke-linecap', 'round');
                questionHeaderDownButtonSvg.setAttribute('stroke-linejoin', 'round');
                questionHeaderDownButtonSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />';

                questionHeaderDownButton.appendChild(questionHeaderDownButtonSvg);

                let questionHeaderNr = document.createElement('span');
                questionHeaderNr.innerText = 'Order Nr: ' + question.order_nr;

                let questionHeaderQuestion = document.createElement('span');
                questionHeaderQuestion.classList.add('text-xl', 'px-4');
                questionHeaderQuestion.innerText = question.question;

                let questionHeaderSeparator = document.createElement('span');
                questionHeaderSeparator.innerText = ' | ';

                questionHeader.appendChild(questionHeaderUpButton);
                questionHeader.appendChild(questionHeaderNr);
                questionHeader.appendChild(questionHeaderDownButton);
                questionHeader.appendChild(questionHeaderSeparator);
                questionHeader.appendChild(questionHeaderQuestion);

                let answerHolder = document.createElement('div');
                answerHolder.classList.add('flex', 'flex-row', 'py-4');
                answerHolder.id = 'answer-holder-' + question.id;

                if (question.answers) {
                    question.answers.forEach(answer => {
                        let answerDiv = document.createElement('div');
                        if (answer.is_correct == "1") {
                            answerDiv.classList.add('border-lime-500', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
                        } else {
                            answerDiv.classList.add('border-slate-500', 'bg-slate-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
                        }
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

                $("#move-question-up-" + question.id).html($("#move-question-up-" + question.id).html());
                $("#move-question-down-" + question.id).html($("#move-question-down-" + question.id).html());
            });
        }

    </script>
@endsection