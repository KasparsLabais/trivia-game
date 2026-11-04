@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    @php
        // Shared field styling so every input/select matches the rest of the app.
        $label  = 'fira-sans text-xs uppercase tracking-widest text-main-dark/60 mb-1.5';
        $input  = 'w-full bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-3 py-2.5 fira-sans text-main-dark placeholder:text-main-dark/40 focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] transition-all';
        $select = 'w-full appearance-none bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] pl-3 pr-10 py-2.5 fira-sans font-bold text-main-dark focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] cursor-pointer transition-all';
    @endphp

    <div id="question-editor" class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-5xl mx-auto flex flex-col gap-8">

            {{-- Header: highlighter title + back link --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h1 class="relative self-start inline-block max-w-full">
                    <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                    <span class="relative block russo text-main-dark text-lg md:text-2xl tracking-widest uppercase px-4 py-1.5 break-words">{{ $trivia['title'] }}</span>
                </h1>
                <a href="/trv/management" class="shrink-0 self-start inline-flex items-center gap-2 fira-sans font-black uppercase text-xs tracking-wide bg-white text-main-dark border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-4 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" class="w-4 h-4 stroke-main-dark"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
                    Back
                </a>
            </div>

            {{-- Trivia settings window --}}
            <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                    <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Trivia Settings</span>
                </div>
                <div class="p-5 md:p-6">
                    <form action="" method="POST" class="flex flex-col gap-5">
                        {{ csrf_field() }}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="title">Title</label>
                                <input value="{{ $trivia['title'] }}" class="{{ $input }}" type="text" name="title" id="title">
                            </div>
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="description">Description</label>
                                <input value="{{ $trivia['description'] }}" class="{{ $input }}" type="text" name="description" id="description">
                            </div>
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="difficulty">Difficulty</label>
                                <div class="relative">
                                    <select class="{{ $select }}" name="difficulty" id="difficulty">
                                        <option value="easy" @if($trivia['difficulty'] == 'easy') selected @endif>Easy</option>
                                        <option value="medium" @if($trivia['difficulty'] == 'medium') selected @endif>Medium</option>
                                        <option value="hard" @if($trivia['difficulty'] == 'hard') selected @endif>Hard</option>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="category">Category</label>
                                <div class="relative">
                                    <select class="{{ $select }}" name="category" id="category">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat['id'] }}" @if($trivia['category_id'] == $cat['id']) selected @endif>{{ $cat['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Flags --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="is_active">Active</label>
                                <div class="relative">
                                    <select class="{{ $select }}" name="is_active" id="is_active">
                                        <option value="1" @if($trivia['is_active'] == 1) selected @endif>Yes</option>
                                        <option value="0" @if($trivia['is_active'] == 0) selected @endif>No</option>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="private">Private</label>
                                <div class="relative">
                                    <select class="{{ $select }}" name="private" id="private">
                                        <option value="1" @if($trivia['private'] == 1) selected @endif>Yes</option>
                                        <option value="0" @if($trivia['private'] == 0) selected @endif>No</option>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col">
                                <label class="{{ $label }}" for="is_premium">Premium</label>
                                <div class="relative">
                                    <select class="{{ $select }}" name="is_premium" id="is_premium">
                                        <option value="1" @if($trivia['is_premium'] == 1) selected @endif>Yes</option>
                                        <option value="0" @if($trivia['is_premium'] == 0) selected @endif>No</option>
                                    </select>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-main-accent text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Save Changes</button>
                    </form>
                </div>
            </div>

            {{-- Questions --}}
            <div class="flex flex-col gap-4">
                <div class="flex flex-row flex-wrap items-center justify-between gap-3">
                    <h2 class="russo text-main-dark text-base uppercase tracking-widest">Questions</h2>
                    <span class="fira-sans text-xs uppercase tracking-widest border-2 border-main-dark px-3 py-1 {{ $tierMaxQuestions === null ? 'bg-green-500 text-white' : 'bg-pop text-main-dark' }}">
                        {{ $tierName }} plan ·
                        @if($tierMaxQuestions === null) Unlimited questions @else <span v-text="questionCount"></span>/{{ $tierMaxQuestions }} questions @endif
                    </span>
                </div>

                <div v-for="question in sortedQuestions" :key="question.id" class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]">

                    {{-- Card header (dark bar) --}}
                    <div class="flex flex-col md:flex-row md:items-center gap-3 bg-main-dark px-4 py-3">
                        <div class="flex flex-1 items-center gap-3 min-w-0">
                            <span class="russo text-pop text-sm shrink-0">Q[[ question.order_nr ]]</span>
                            <template v-if="question.id == editedQuestionId">
                                <input v-model="editedQuestionValues.question" class="flex-1 min-w-0 bg-white border-2 border-main-dark px-3 py-1.5 fira-sans text-main-dark text-sm focus:outline-none" type="text" placeholder="Enter question">
                            </template>
                            <template v-else>
                                <span class="fira-sans font-bold text-white truncate">[[ question.question ]]</span>
                            </template>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            {{-- Type: badge (view) / selects (edit) --}}
                            <template v-if="question.id == editedQuestionId">
                                <select v-model="editedQuestionValues.question_type" class="bg-white border-2 border-main-dark px-2 py-1.5 fira-sans text-main-dark text-xs font-bold focus:outline-none cursor-pointer">
                                    <option value="options">Options</option>
                                    <option v-if="typeAllowed('text_input') || question.question_type == 'text_input'" value="text_input">Text Input</option>
                                    <option v-if="typeAllowed('open_input') || question.question_type == 'open_input'" value="open_input">Open Submission</option>
                                </select>
                                <select v-if="editedQuestionValues.question_type == 'open_input'" v-model="editedQuestionValues.input_type" class="bg-white border-2 border-main-dark px-2 py-1.5 fira-sans text-main-dark text-xs font-bold focus:outline-none cursor-pointer">
                                    <option value="string">String</option>
                                    <option value="number">Number</option>
                                    <option value="image">Image</option>
                                </select>
                                <button type="button" v-on:click="saveEditQuestion()" class="fira-sans font-black uppercase text-xs tracking-wide bg-green-500 text-white border-2 border-main-dark px-3 py-1.5 hover:bg-green-600 transition-colors">Save</button>
                                <button type="button" v-on:click="cancelEditQuestion()" class="fira-sans font-black uppercase text-xs tracking-wide bg-main-accent text-white border-2 border-main-dark px-3 py-1.5 hover:brightness-110 transition-all">Cancel</button>
                            </template>
                            <template v-else>
                                <span class="bg-pop text-main-dark border-2 border-main-dark px-2.5 py-1 russo text-[10px] uppercase tracking-wide shrink-0">[[ typeLabel(question.question_type) ]]</span>
                                <button type="button" v-on:click="editQuestion(question.id)" title="Edit question" class="w-8 h-8 flex items-center justify-center bg-brand text-white border-2 border-main-dark hover:bg-white hover:text-main-dark transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                </button>
                                <button type="button" v-on:click="moveQuestionUp(question.id)" title="Move up" class="w-8 h-8 flex items-center justify-center bg-white text-main-dark border-2 border-main-dark hover:bg-pop transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                                </button>
                                <button type="button" v-on:click="moveQuestionDown(question.id)" title="Move down" class="w-8 h-8 flex items-center justify-center bg-white text-main-dark border-2 border-main-dark hover:bg-pop transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Card body --}}
                    <div class="p-4 md:p-5">
                        {{-- EDIT MODE: answer editors --}}
                        <template v-if="question.id == editedQuestionId">
                            <template v-if="editedQuestionValues.question_type == 'options' || editedQuestionValues.question_type == 'text_input'">
                                <div class="flex flex-row flex-wrap gap-3">
                                    <div v-for="(answer, index) in editedQuestionValues.answers" class="flex flex-col gap-2 border-2 border-main-dark p-3 bg-main-light">
                                        <div class="flex flex-col">
                                            <label class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mb-1">Answer</label>
                                            <input v-model="answer.answer" class="bg-white border-2 border-main-dark px-2.5 py-1.5 fira-sans text-main-dark text-sm focus:outline-none" type="text" placeholder="Answer">
                                        </div>
                                        <div v-if="editedQuestionValues.question_type == 'options'" class="flex flex-col">
                                            <label class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mb-1">Correct?</label>
                                            <select v-model="answer.is_correct" class="bg-white border-2 border-main-dark px-2.5 py-1.5 fira-sans text-main-dark text-sm focus:outline-none cursor-pointer">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                        <button type="button" v-on:click="removeAnswer(index)" class="fira-sans font-black uppercase text-[11px] tracking-wide bg-main-accent text-white border-2 border-main-dark px-2.5 py-1.5 hover:brightness-110 transition-all">Remove</button>
                                    </div>
                                </div>
                            </template>
                            <p v-else class="fira-sans text-sm text-main-dark/60">Open submissions are judged live during the game — no preset answers.</p>
                        </template>

                        {{-- VIEW MODE: answer pills --}}
                        <template v-else>
                            <div v-if="question.question_type == 'open_input'" class="fira-sans text-sm text-main-dark/60">Open submission ([[ question.input_type || 'string' ]]) — judged live during the game.</div>
                            <div v-else class="flex flex-row flex-wrap gap-2">
                                <template v-for="answer in question.answers">
                                    <template v-if="question.question_type == 'options'">
                                        <span class="border-2 border-main-dark px-3 py-2 fira-sans font-bold text-sm" :class="answer.is_correct == 1 || answer.is_correct === true ? 'bg-green-500 text-white' : 'bg-surface text-main-dark'">[[ answer.answer ]]</span>
                                    </template>
                                    <template v-else>
                                        <div class="flex flex-col gap-2 border-2 border-main-dark p-2 bg-main-light">
                                            <span class="text-center border-2 border-main-dark bg-green-500 text-white px-3 py-1.5 fira-sans font-bold text-sm">[[ answer.answer ]]</span>
                                            <a v-if="answer.file_url_type != ''" class="fira-sans text-xs font-bold text-brand underline text-center" v-bind:href="answer.file_url" target="_blank">Open attached file</a>
                                            <form method="POST" v-bind:action="'/admin/trv/answer-image/' + answer.id" enctype="multipart/form-data" class="flex flex-col gap-1.5">
                                                {{ csrf_field() }}
                                                <label class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60">Image / video (optional)</label>
                                                <div class="flex items-stretch bg-white border-2 border-main-dark">
                                                    <label v-bind:for="'answer-image-' + answer.id" class="flex items-center bg-main-dark text-white fira-sans font-bold uppercase text-[11px] tracking-wide px-2.5 py-1.5 cursor-pointer whitespace-nowrap">File</label>
                                                    <input v-bind:id="'answer-image-' + answer.id" class="hidden" type="file" name="answer-image" v-on:change="$event.target.nextElementSibling.textContent = $event.target.files.length ? $event.target.files[0].name : 'No file'">
                                                    <span class="flex items-center px-2 py-1.5 fira-sans text-main-dark text-xs truncate min-w-0" style="max-width:9rem">No file</span>
                                                </div>
                                                <button type="submit" class="fira-sans font-black uppercase text-[11px] tracking-wide bg-pop text-main-dark border-2 border-main-dark px-3 py-1.5 hover:brightness-105 transition-all">Upload</button>
                                            </form>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </template>
                    </div>

                    {{-- Card footer: add answer (view mode, not open_input) --}}
                    <div class="bg-main-light border-t-4 border-main-dark px-4 py-3">
                        <template v-if="question.id == editedQuestionId">
                            <span class="fira-sans text-sm font-bold text-main-dark/60">Question is in edit mode.</span>
                        </template>
                        <template v-else>
                            <div v-if="question.question_type == 'open_input'" class="fira-sans text-sm text-main-dark/60">No answers to add for open submissions.</div>
                            <div v-else class="flex flex-row flex-wrap items-end gap-3">
                                <div class="flex flex-col">
                                    <label class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mb-1">Answer</label>
                                    <input v-model="question.new_answer" class="bg-white border-2 border-main-dark px-2.5 py-1.5 fira-sans text-main-dark text-sm focus:outline-none" type="text" placeholder="Enter answer">
                                </div>
                                <div v-if="question.question_type == 'options'" class="flex flex-col">
                                    <label class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mb-1">Correct?</label>
                                    <select v-model="question.new_answer_is_correct" class="bg-white border-2 border-main-dark px-2.5 py-1.5 fira-sans text-main-dark text-sm focus:outline-none cursor-pointer">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                                <button type="button" v-on:click="addAnswer(question.id)" class="fira-sans font-black uppercase text-xs tracking-wide bg-brand text-white border-2 border-main-dark px-4 py-2 hover:bg-main-dark transition-colors">Add Answer</button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Add a question --}}
                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 md:p-6 flex flex-col gap-4">
                    <h3 class="russo text-main-dark text-sm uppercase tracking-widest">Add a Question</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col sm:col-span-2">
                            <label class="{{ $label }}">Question</label>
                            <input v-model="newQuestion" class="{{ $input }}" type="text" placeholder="Type your question">
                        </div>
                        <div class="flex flex-col">
                            <label class="{{ $label }}">Question Type</label>
                            <div class="relative">
                                <select v-model="newQuestionType" class="{{ $select }}">
                                    <option v-if="typeAllowed('options')" value="options">Options (A, B, C...)</option>
                                    <option v-if="typeAllowed('text_input')" value="text_input">Text Input (auto-graded)</option>
                                    <option v-if="typeAllowed('open_input')" value="open_input">Open Submission (judged)</option>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                </span>
                            </div>
                        </div>
                        <div v-if="newQuestionType == 'open_input'" class="flex flex-col">
                            <label class="{{ $label }}">Input Format</label>
                            <div class="relative">
                                <select v-model="newInputType" class="{{ $select }}">
                                    <option value="string">String</option>
                                    <option value="number">Number</option>
                                    <option value="image">Image</option>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row flex-wrap items-center gap-3">
                        <button type="button" v-on:click="addQuestion()" :disabled="atQuestionCap"
                                class="self-start fira-sans font-black uppercase tracking-wider border-4 border-main-dark px-6 py-2.5 transition-all duration-100"
                                :class="atQuestionCap ? 'bg-surface text-main-dark/40 cursor-not-allowed' : 'bg-main-accent text-white shadow-[5px_5px_0_0_#14323B] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]'">Add Question</button>
                        <p v-if="atQuestionCap" class="fira-sans text-sm text-main-accent font-bold">Question limit reached for the {{ $tierName }} plan — upgrade to add more.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        @php $jsonFlags = JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP; @endphp
        let questionsList = {
            @foreach($questions as $question)
            '{{ $question['id'] }}' :{ 'id' :{{ $question['id'] }}, 'order_nr':{{ $question['order_nr'] }}, 'question': {!! json_encode($question['question'], $jsonFlags) !!}, 'question_type': {!! json_encode($question['question_type'], $jsonFlags) !!}, 'input_type': {!! json_encode($question['input_type'] ?? '', $jsonFlags) !!}, 'new_answer' : '', 'new_answer_is_correct' : 0, 'answers':[
                        @foreach($question->answers as $answer)
                    {
                        'id': {{ $answer['id'] }},
                        'answer': {!! json_encode($answer['answer'], $jsonFlags) !!},
                        'is_correct': {{ (int) $answer['is_correct'] }},
                        'file_url': {!! json_encode($answer['file_url'] ?? '', $jsonFlags) !!},
                        'file_url_type': {!! json_encode($answer['file_url_type'] ?? '', $jsonFlags) !!}
                    },
                    @endforeach
                ]},
            @endforeach
        };

        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    triviaId: {{ $trivia['id'] }},
                    editedQuestionId: 0,
                    editedQuestionValues: { question: '', question_type: '', input_type: '', answers: [] },
                    questions: questionsList,
                    newQuestion: '',
                    newQuestionType: 'options',
                    newInputType: 'string',
                    // Plan limits (null = unlimited) for this quiz's owner.
                    maxQuestions: @json($tierMaxQuestions),
                    allowedTypes: @json($tierQuestionTypes),
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                csrf() {
                    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                },
                typeLabel(type) {
                    if (type === 'text_input') return 'Text Input';
                    if (type === 'open_input') return 'Open Submission';
                    return 'Options';
                },
                typeAllowed(type) {
                    return this.allowedTypes.includes(type);
                },
                editQuestion(questionId) {
                    this.editedQuestionId = questionId;
                    this.editedQuestionValues.question = this.questions[questionId].question;
                    this.editedQuestionValues.question_type = this.questions[questionId].question_type;
                    this.editedQuestionValues.input_type = this.questions[questionId].input_type || 'string';
                    this.editedQuestionValues.answers = this.questions[questionId].answers;
                },
                cancelEditQuestion() {
                    this.editedQuestionId = 0;
                    this.editedQuestionValues = { question: '', question_type: '', input_type: '', answers: [] };
                },
                saveEditQuestion() {
                    fetch('/management/trivia/' + this.triviaId + '/question/' + this.editedQuestionId, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                        body: JSON.stringify({
                            question: this.editedQuestionValues.question,
                            question_type: this.editedQuestionValues.question_type,
                            input_type: this.editedQuestionValues.input_type,
                            answers: this.editedQuestionValues.answers
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.questions[this.editedQuestionId].question = this.editedQuestionValues.question;
                            this.questions[this.editedQuestionId].question_type = this.editedQuestionValues.question_type;
                            this.questions[this.editedQuestionId].input_type = this.editedQuestionValues.input_type;
                            this.questions[this.editedQuestionId].answers = this.editedQuestionValues.answers;
                            this.cancelEditQuestion();
                        }
                    });
                },
                addAnswer(questionId) {
                    const q = this.questions[questionId];
                    fetch('/management/trivia/' + this.triviaId + '/question/' + questionId + '/answer', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                        body: JSON.stringify({
                            answer: q.new_answer,
                            is_correct: q.question_type === 'options' ? q.new_answer_is_correct : 1
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            q.answers.push(data.payload);
                            q.new_answer = '';
                            q.new_answer_is_correct = 0;
                        }
                    });
                },
                removeAnswer(index) {
                    const q = this.questions[this.editedQuestionId];
                    fetch('/management/trivia/' + this.triviaId + '/question/' + this.editedQuestionId + '/answer/' + q.answers[index].id, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf() }
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            q.answers.splice(index, 1);
                        }
                    });
                },
                addQuestion() {
                    if (!this.newQuestion.trim()) return;
                    if (this.atQuestionCap) return;
                    fetch('/trv/management/trivia/' + this.triviaId + '/question', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                        body: JSON.stringify({
                            question: this.newQuestion,
                            question_type: this.newQuestionType,
                            input_type: this.newInputType
                        })
                    }).then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const q = data.payload;
                            q.answers = [];
                            q.new_answer = '';
                            q.new_answer_is_correct = 0;
                            if (!q.input_type) q.input_type = this.newInputType;
                            // Reactive add — Vue 3 Proxy picks up the new key and the list re-sorts.
                            this.questions[q.id] = q;
                            this.newQuestion = '';
                        }
                    }).catch(error => console.log(error));
                },
                updateOrder(questionId, orderNr) {
                    fetch('/trv/management/trivia/' + this.triviaId + '/question/' + questionId + '/order', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': this.csrf() },
                        body: JSON.stringify({ order_nr: orderNr })
                    }).then(response => response.json()).catch(error => console.log(error));
                },
                // Swap order_nr with the neighbour above/below, then persist both.
                moveQuestionUp(questionId) {
                    const q = this.questions[questionId];
                    const neighbour = Object.values(this.questions).find(x => x.order_nr === q.order_nr - 1);
                    if (!neighbour) return;
                    neighbour.order_nr = q.order_nr;
                    q.order_nr = q.order_nr - 1;
                    this.updateOrder(q.id, q.order_nr);
                    this.updateOrder(neighbour.id, neighbour.order_nr);
                },
                moveQuestionDown(questionId) {
                    const q = this.questions[questionId];
                    const neighbour = Object.values(this.questions).find(x => x.order_nr === q.order_nr + 1);
                    if (!neighbour) return;
                    neighbour.order_nr = q.order_nr;
                    q.order_nr = q.order_nr + 1;
                    this.updateOrder(q.id, q.order_nr);
                    this.updateOrder(neighbour.id, neighbour.order_nr);
                },
            },
            computed: {
                sortedQuestions() {
                    return Object.values(this.questions).sort((a, b) => a.order_nr - b.order_nr);
                },
                questionCount() {
                    return Object.keys(this.questions).length;
                },
                atQuestionCap() {
                    return this.maxQuestions !== null && this.questionCount >= this.maxQuestions;
                }
            }
        }).mount('#question-editor');
    </script>
@endsection
