@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <div class="flex flex-row justify-center" id="question-editor">
        <div class="flex flex-col mt-2 px-2  w-11/12">
            <div>

                <h1 class="josefin-sans font-semibold text-4xl text-yellow-500 mt-2">{{ $trivia['title'] }}</h1>
                <div class="bg-slate-100 rounded shadow">
                    <form action="" method="POST" class="flex flex-row px-2 py-4 bg-slate-200 shadow">
                        {{ csrf_field() }}
                        <div class="flex flex-col w-2/6">
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="title">Title:</label>
                                <input value="{{ $trivia['title'] }}" class="px-2 bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 text-lg" type="text" name="title" id="title">
                            </div>
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="question">Description:</label>
                                <input value="{{ $trivia['description'] }}"  class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2 text-lg" type="text" name="description" id="description">
                            </div>
                        </div>
                        <div class="flex flex-col w-2/6">
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="difficulty">Difficulty</label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2 text-lg" name="difficulty" id="difficulty">
                                    <option value="easy" @if($trivia['difficulty'] == 'easy') selected="selected" @endif>Easy</option>
                                    <option value="medium" @if($trivia['difficulty'] == 'medium') selected="selected" @endif>Medium</option>
                                    <option value="hard" @if($trivia['difficulty'] == 'hard') selected="selected" @endif>Hard</option>
                                </select>
                            </div>
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="category">Category</label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2 text-lg" name="category" id="category">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat['id'] }}" @if($trivia['category_id'] == $cat['id']) selected="selected" @endif>{{ $cat['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col w-1/6">
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="is_active">Is Active: </label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2 text-lg" name="is_active" id="is_active">
                                    <option value="0" @if($trivia['is_active'] == 0) selected="selected" @endif>0</option>
                                    <option value="1" @if($trivia['is_active'] == 1) selected="selected" @endif>1</option>
                                </select>
                            </div>

                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="is_active">Private: </label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2 text-lg" name="private" id="private">
                                    <option value="0" @if($trivia['private'] == 0) selected="selected" @endif>0</option>
                                    <option value="1" @if($trivia['private'] == 1) selected="selected" @endif>1</option>
                                </select>
                            </div>

                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="is_premium">Is Premium: </label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2 text-lg" name="is_premium" id="is_premium">
                                    <option value="0" @if($trivia['is_premium'] == 0) selected="selected" @endif>0</option>
                                    <option value="1" @if($trivia['is_premium'] == 1) selected="selected" @endif>1</option>
                                </select>
                            </div>

                        </div>
                        <div class="flex flex-row px-2">
                            <div class="flex flex-col justify-center">
                                <button type="submit" class="py-2 px-4 shadow-md bg-lime-500 text-lg text-slate-100 font-semibold">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>



                <h2 class="josefin-sans font-semibold text-2xl text-yellow-500 mt-2">Questions</h2>

                <div v-for="question in sortedQuestions" class="flex flex-col bg-slate-100 shadow rounded mt-4">
                    <div class="flex flex-row justify-between bg-slate-300 py-2 px-4">
                        <div class="flex flex-col justify-center">
                            <div class="flex flex-row text-xl josefin-sans font-semibold">
                                <template v-if="question.id == editedQuestionId">
                                    <input v-model="editedQuestionValues.question" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded px-2 py-2" type="text" placeholder="Enter Question">
                                </template>
                                <template v-else>
                                    [[ question.question ]]
                                </template>
                                <span class="px-2">|</span>
                                <template v-if="question.id == editedQuestionId">
                                    <select v-model="editedQuestionValues.question_type" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2" >
                                        <option value="options">Options (A, B, C...)</option>
                                        <option value="text_input">Text Input</option>
                                        <option value="number_input">Number Input</option>
                                        <option value="image">Image</option>
                                        <!--<option value="video">Video</option>
                                        <option value="audio">Audio</option>-->
                                    </select>
                                </template>
                                <template v-else>
                                    <span class="font-normal">Question Type: [[ question.question_type ]]</span>
                                </template>

                                <div v-if="question.id == editedQuestionId" class="flex flex-row px-2">
                                    <div>
                                        <button v-on:click="saveEditQuestion()" class="py-2 px-6 shadow-md bg-lime-500 text-lg text-slate-100 font-semibold mx-1">Save</button>
                                        <button v-on:click="cancelEditQuestion()"  class="py-2 px-6 shadow-md bg-rose-500 text-lg text-slate-100 font-semibold mx-1">Cancel</button>
                                    </div>
                                    <!--
                                    <svg v-on:click="saveEditQuestion()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" data-slot="icon" class="stroke-lime-600 mx-2 w-6 h-6 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                                    </svg>
                                    <svg v-on:click="cancelEditQuestion()" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" data-slot="icon" class="stroke-rose-600 mx-2 w-6 h-6 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                    </svg>
                                    -->
                                </div>
                                <div v-else v-on:click="editQuestion(question.id)" class="px-2">
                                    <svg  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" data-slot="icon" class="stroke-cyan-600 w-6 h-6 mx-2 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-row">
                            <div class="flex flex-col rounded  px-1 cursor-pointer" v-on:click="moveQuestionOrderNrUp(question.id)">
                                <div class="flex flex-row justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-lime-600 w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg>
                                </div>
                                Move Up
                            </div>
                            <div class="rounded px-1 cursor-pointer" v-on:click="moveQuestionOrderNrDown(question.id)">
                                <div class="flex flex-row justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-rose-600 w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </div>
                                Move Down
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row py-4 px-4">

                        <template v-if="question.id == editedQuestionId">
                            <template v-if="question.question_type == 'options'">
                                <div v-for="(answer, index) in editedQuestionValues.answers" class="flex flex-col pr-2">
                                    <div class="flex flex-col">
                                        <div class="flex flex-row">
                                            <div class="flex flex-col px-2 py-1">
                                                <label class="raleway font-semibold text-sm" >Answer:</label>
                                                <input v-model="answer.answer" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2" type="text" placeholder="Enter Answer">
                                            </div>
                                            <div class="flex flex-col px-2 py-1">
                                                <label class="raleway font-semibold text-sm" >Is Correct:</label>
                                                <select v-model="answer.is_correct" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2" >
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex flex-row justify-start px-2 h-full">
                                            <button v-on:click="removeAnswer(index)" class="py-2 px-2 shadow-md bg-rose-600 text-left text-slate-100 text-lg font-semibold rounded">Remove Answer</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <template v-if="question.question_type == 'text_input'">
                                <div v-for="(answer, index) in editedQuestionValues.answers" class="flex flex-col pr-2">
                                    <div class="flex flex-col">
                                        <div class="flex flex-row">
                                            <div class="flex flex-col px-2 py-1">
                                                <label class="raleway font-semibold text-sm" >Answer:</label>
                                                <input v-model="answer.answer" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2" type="text" placeholder="Enter Answer">
                                            </div>
                                            <div class="flex flex-col px-2 py-1">
                                                <label class="raleway font-semibold text-sm" >Is Correct:</label>
                                                <select v-model="answer.is_correct" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2 px-2" >
                                                    <option value="0">No</option>
                                                    <option value="1">Yes</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex flex-row justify-start px-2 h-full">
                                            <button v-on:click="removeAnswer(index)" class="py-2 px-2 shadow-md bg-rose-600 text-left text-slate-100 text-lg font-semibold rounded">Remove Answer</button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </template>

                        <template v-else v-for="answer in question.answers">
                            <template v-if="question.question_type == 'options'">
                                <div v-if="answer.is_correct" class="flex flex-row border-lime-500 bg-lime-500 text-slate-100 font-semibold border-2 px-4 py-2 rounded shadow mx-1">
                                    [[ answer.answer ]]
                                </div>
                                <div v-else class="border-slate-500 bg-slate-500 text-slate-100 font-semibold border-2 px-4 py-2 rounded shadow mx-1">
                                    [[ answer.answer ]]
                                </div>
                            </template>
                            <template v-if="question.question_type == 'text_input'">

                                <div class="flex flex-col pr-2">
                                    <div v-if="answer.is_correct" class="text-center border-lime-500 bg-lime-500 text-slate-100 font-semibold border-2 px-4 py-2 rounded shadow mx-1">
                                        [[ answer.answer ]]
                                    </div>
                                    <div v-else class="text-center border-slate-500 bg-slate-500 text-slate-100 font-semibold border-2 px-4 py-2 rounded shadow mx-1">
                                        [[ answer.answer ]]
                                    </div>

                                    <div v-if="answer.file_url_type != '' " class="flex flex-col">
                                        <a class="font-semibold text-cyan-700" v-bind:["href"]="answer.file_url" target="_blank">Open Attached File</a>
                                    </div>
                                </div>

                                <form class="px-3 border-l-2 border-l-slate-300" method="POST" v-bind:["action"]="'/admin/trv/answer-image/' + answer.id" enctype="multipart/form-data">
                                    <div class="flex flex-row">
                                        {{ csrf_field() }}
                                        <div class="flex flex-col pr-2">
                                            <label>Upload Image/Video For Correct answer (optional): </label>
                                            <input type="file" name="answer-image" id="answer-image">
                                        </div>
                                        <button class="py-2 px-2 shadow-md bg-lime-600 text-left text-slate-100 text-lg font-semibold mb-2 rounded" type="submit">Upload Image</button>
                                    </div>
                                </form>
                            </template>
                        </template>

                    </div>

                    <div class="flex flex-row bg-slate-300 py-2 px-2">
                        <template v-if="question.id == editedQuestionId">
                            <span class="text-lg josefin-sans font-semibold">Question is in edit mode.</span>
                        </template>
                        <div v-else class="flex flex-row">
                            <div class="flex flex-col px-2 py-1">
                                <label class="raleway font-semibold text-sm" >Answer:</label>
                                <input v-model="question.new_answer" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded px-2 py-2" type="text" placeholder="Enter Answer">
                            </div>
                            <template v-if="question.question_type == 'options'">
                                <div class="flex flex-col px-2 py-1">
                                    <label class="raleway font-semibold text-sm" >Is Correct:</label>
                                    <select v-model="question.new_answer_is_correct" class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-2" >
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </template>
                            <div class="flex flex-col justify-end pb-2 h-full">
                                <button v-on:click="addAnswer(question.id)" class="py-2 px-2 shadow-md bg-cyan-500 text-slate-100 font-semibold">Add Answer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-100 rounded shadow">
                    <div class="flex flex-col px-4">
                        <h2 class="raleway">Questions:</h2>





                        <h2 class="raleway">Questions:</h2>
                        <div id="question-holder">
                        @foreach($questions as $question)
                            <div id="question-{{ $question['id'] }}" question-type="{{ $question['question_type'] }}"  class="flex flex-col bg-slate-200 shadow rounded mt-4">
                                <div class="flex flex-row bg-slate-300  py-2 px-2">
                                    <div id="move-question-up-{{ $question['id'] }}" class="rounded bg-lime-600 px-1 cursor-pointer" onclick="moveQuestionOrderNrUp()" data-questionid="{{ $question['id'] }}">
                                        <svg data-questionid="{{ $question['id'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-slate-200 w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </div>
                                     <span>
                                         Order Nr: {{ $question['order_nr'] }}
                                     </span>
                                    <div id="move-question-down-{{ $question['id'] }}" class="rounded bg-rose-600 px-1 cursor-pointer" onclick="moveQuestionOrderNrDown()" data-questionid="{{ $question['id'] }}">
                                        <svg data-questionid="{{ $question['id'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="stroke-slate-200 w-6 h-6">
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

                                    @if ($question['question_type'] == 'text_input')
                                        <form method="POST" action="/admin/trv/answer-image/{{ $answer['id'] }}" enctype="multipart/form-data">
                                            <div class="flex flex-row">
                                                {{ csrf_field() }}
                                                <div class="flex flex-col">
                                                    @if($answer['file_url_type'])
                                                        <div class="flex flex-col">
                                                            <a class="font-semibold text-cyan-700" href="{{ $answer['file_url'] }}" target="_blank">Current File</a>
                                                        </div>
                                                    @endif
                                                    <label>Upload Image For Correct answer: </label>
                                                    <input type="file" name="answer-image" id="answer-image">
                                                </div>
                                                <button class="py-2 px-2 shadow-md bg-lime-600 text-left text-slate-100 text-lg font-semibold mb-2 rounded" type="submit">Upload Image</button>
                                            </div>
                                        </form>
                                    @endif

                                @endforeach
                                </div>
                                <div class="flex flex-row bg-slate-300 py-2 px-2">
                                    <div class="flex flex-row">
                                        <div class="flex flex-col px-2 py-1">
                                            <label class="raleway font-semibold text-sm" for="answer-{{ $question['id'] }}">Answer:</label>
                                            <input class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded" type="text" name="answer-{{ $question['id'] }}" id="answer-{{ $question['id'] }}">
                                        </div>
                                        @if($question['question_type'] == 'options')
                                        <div class="flex flex-col px-2 py-1">
                                            <label class="raleway font-semibold text-sm" for="is_correct-{{ $question['id'] }}">Is Correct:</label>
                                            <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded" id="is_correct-{{ $question['id'] }}" name="is_correct-{{ $question['id'] }}">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        @endif
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
                            <div class="flex flex-col px-2 py-2">
                                <label class="raleway font-semibold text-md" for="question_type">Question Type:</label>
                                <select class="bg-slate-100 border border-zinc-400 shadow shadow-zinc-400 rounded py-1" name="question_type" id="question_type">
                                    <option value="options">Options (A, B, C...)</option>
                                    <option value="text_input">Text Input</option>
                                    <option value="number_input">Number Input</option>
                                    <option value="image">Image</option>
                                    <!--<option value="video">Video</option>
                                    <option value="audio">Audio</option>-->
                                </select>
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
            '{{ $question['id'] }}' :{ 'id' :{{ $question['id'] }}, 'order_nr':{{ $question['order_nr'] }}, 'question':'{{ $question['question'] }}', 'question_type': '{{ $question['question_type']  }}', 'new_answer' : '', 'new_answer_is_correct' : 0, 'answers':[
                        @foreach($question->answers as $answer)
                    {
                        'id': {{ $answer['id'] }},
                        'answer': '{{ $answer['answer'] }}',
                        'is_correct': {{ $answer['is_correct'] }},
                        'file_url': '{{ $answer['file_url'] }}',
                        'file_url_type': '{{ $answer['file_url_type'] }}'
                    },
                    @endforeach
                ]},
            @endforeach
        };


        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    editedQuestionId: 0,
                    editedQuestionValues: {
                        question: '',
                        question_type: '',
                        answers: []
                    },
                    questions: questionsList
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                editQuestion(questionId) {
                    console.log('edit question', questionId);
                    this.editedQuestionId = questionId;
                    this.editedQuestionValues.question = this.questions[questionId].question;
                    this.editedQuestionValues.question_type = this.questions[questionId].question_type;
                    this.editedQuestionValues.answers = this.questions[questionId].answers;
                },
                cancelEditQuestion() {
                    console.log('cancel edit question');
                    this.editedQuestionId = 0;
                    this.editedQuestionValues.question = '';
                    this.editedQuestionValues.question_type = '';
                    this.editedQuestionValues.answers = [];
                },
                saveEditQuestion() {
                    console.log('save edit question');
                    console.log(this.editedQuestionValues);
                    fetch('/management/trivia/{{ $trivia['id'] }}/question/' + this.editedQuestionId, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            question: this.editedQuestionValues.question,
                            question_type: this.editedQuestionValues.question_type,
                            answers: this.editedQuestionValues.answers
                        })
                    }).then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.success) {
                            this.questions[this.editedQuestionId].question = this.editedQuestionValues.question;
                            this.questions[this.editedQuestionId].question_type = this.editedQuestionValues.question_type;
                            this.questions[this.editedQuestionId].answers = this.editedQuestionValues.answers;
                            this.editedQuestionId = 0;
                            this.editedQuestionValues.question = '';
                            this.editedQuestionValues.question_type = '';
                            this.editedQuestionValues.answers = [];
                        }
                    });
                },
                addAnswer(questionId) {
                    console.log('add answer', questionId);
                    console.log(this.questions[questionId]);
                    fetch('/management/trivia/{{ $trivia['id'] }}/question/' + questionId + '/answer', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            answer: this.questions[questionId].new_answer,
                            is_correct: this.questions[questionId].new_answer_is_correct
                        })
                    }).then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.success) {
                            this.questions[questionId].answers.push(data.payload);
                            this.questions[questionId].new_answer = '';
                            this.questions[questionId].new_answer_is_correct = 0;
                        }
                    });
                },
                removeAnswer(index) {
                    console.log('remove answer', index);
                    console.log(this.questions[this.editedQuestionId].answers[index]);
                    fetch('/management/trivia/{{ $trivia['id'] }}/question/' + this.editedQuestionId + '/answer/' + this.questions[this.editedQuestionId].answers[index].id, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    }).then(response => response.json())
                    .then(data => {
                        console.log(data);
                        if (data.success) {
                            this.questions[this.editedQuestionId].answers.splice(index, 1);
                        }
                    });
                },
            },
            computed: {
                sortedQuestions() {
                    return Object.values(this.questions).sort((a, b) => a.order_nr - b.order_nr);
                }
            },
            mount() {
                console.log('mounted');
            },
        }).mount('#question-editor');








        const addQuestion = () => {
            console.log('add question');

            fetch('/trv/management/trivia/{{ $trivia['id'] }}/question', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    question: document.querySelector('#question').value,
                    question_type: document.querySelector('#question_type').value
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
                    questionDiv.setAttribute('question-type', question.question_type);
                    questionDiv.classList.add('flex', 'flex-col', 'bg-slate-200', 'shadow', 'rounded', 'mt-4');

                    let questionHeader = document.createElement('div');
                    questionHeader.classList.add('flex', 'flex-row', 'bg-slate-300', 'py-2', 'px-2');

                    let questionHeaderUpButton = document.createElement('div');
                    questionHeaderUpButton.classList.add('px-1', 'cursor-pointer', 'rounded', 'bg-lime-600');
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
                    questionHeaderUpButtonSvg.setAttribute('class', 'stroke-slate-200 w-6 h-6');
                    questionHeaderUpButtonSvg.setAttribute('stroke-linecap', 'round');
                    questionHeaderUpButtonSvg.setAttribute('stroke-linejoin', 'round');
                    questionHeaderUpButtonSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />';

                    questionHeaderUpButton.appendChild(questionHeaderUpButtonSvg);

                    let questionHeaderDownButton = document.createElement('div');
                    questionHeaderDownButton.classList.add('px-1', 'cursor-pointer', 'rounded', 'bg-rose-600');
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
                    questionHeaderDownButtonSvg.setAttribute('class', 'stroke-slate-200	w-6 h-6');
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



                    if (question.question_type == 'options') {

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

                        questionFooterAnswer.appendChild(questionFooterAnswerIsCorrect);
                    }

                    questionFooterAnswer.appendChild(questionFooterAnswerAnswer);

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
            let questionType = document.querySelector('#question-' + questionId).getAttribute('question-type');

            fetch('/management/trivia/{{ $trivia['id'] }}/question/' + questionId + '/answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    answer: document.querySelector('#answer-'+questionId).value,
                    is_correct: (questionType != 'options') ? 1 :  document.querySelector('#is_correct-'+questionId).value
                })
            }).then(response => response.json())
            .then(data => {
                console.log(data);
                if (data.success) {

                    let answerHolder = document.querySelector('#answer-holder-' + questionId);
                    let answer = data.payload;

                    let answerDiv = document.createElement('div');

                    if (questionType == 'options') {
                        if(answer.is_correct == "1") {
                            answerDiv.classList.add('border-lime-500', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
                        } else {
                            answerDiv.classList.add('border-slate-500', 'bg-slate-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
                        }

                        answerDiv.innerText = answer.answer;
                        answerHolder.appendChild(answerDiv);

                    } else if (questionType == 'text_input') {

                        answerDiv.classList.add('border-lime-500', 'bg-lime-500', 'text-slate-100', 'font-semibold', 'border-2', 'px-4', 'py-2', 'rounded', 'shadow', 'mx-1');
                        answerDiv.innerText = answer.answer;

                        let answerImageForm = document.createElement('form');
                        answerImageForm.setAttribute('method', 'POST');
                        answerImageForm.setAttribute('action', '/admin/trv/answer-image/' + answer.id);
                        answerImageForm.setAttribute('enctype', 'multipart/form-data');

                        let answerImageFormFlex = document.createElement('div');
                        answerImageFormFlex.classList.add('flex', 'flex-row');

                        let answerImageFormToken = document.createElement('input');
                        answerImageFormToken.setAttribute('type', 'hidden');
                        answerImageFormToken.setAttribute('name', '_token');
                        answerImageFormToken.setAttribute('value', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

                        let answerImageFormInputHolder = document.createElement('div');
                        answerImageFormInputHolder.classList.add('flex', 'flex-col');

                        let answerImageFormLabel = document.createElement('label');
                        answerImageFormLabel.innerText = 'Upload Image For Correct answer: ';

                        let answerImageFormInput = document.createElement('input');
                        answerImageFormInput.setAttribute('type', 'file');
                        answerImageFormInput.setAttribute('name', 'answer-image');
                        answerImageFormInput.setAttribute('id', 'answer-image');

                        let answerImageFormButtonHolder = document.createElement('button');
                        answerImageFormButtonHolder.classList.add('py-2', 'px-2', 'shadow-md', 'bg-lime-600', 'text-left', 'text-slate-100', 'text-lg', 'font-semibold', 'mb-2', 'rounded');

                        let answerImageFormButton = document.createElement('button');
                        answerImageFormButton.setAttribute('type', 'submit');
                        answerImageFormButton.innerText = 'Upload Image';

                        answerImageFormButtonHolder.appendChild(answerImageFormButton);
                        answerImageFormInputHolder.appendChild(answerImageFormLabel);
                        answerImageFormInputHolder.appendChild(answerImageFormInput);
                        answerImageFormFlex.appendChild(answerImageFormToken);
                        answerImageFormFlex.appendChild(answerImageFormInputHolder);
                        answerImageFormFlex.appendChild(answerImageFormButtonHolder);
                        answerImageForm.appendChild(answerImageFormFlex);

                        answerHolder.appendChild(answerDiv);
                        answerHolder.appendChild(answerImageForm);
                    }
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