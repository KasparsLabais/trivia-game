@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <div id="game-app">
        <x-section title="">
            <div class="flex flex-row justify-center mt-4">
                <div class="flex flex-row bg-alternative-accent px-1 py-1 shadow-md" style="border-radius: 25px;">
                    <div class="flex flex bg-main-light text-xl p-1" style="border-radius: 25px;">
                        <div id="all-cats-btn-holder" onclick="switchVisibleSection('categories')" class="bg-main-accent px-2 py-2 rounded-lg text-slate-200 josefin-sans shadow-md" style="border-radius: 20px">
                            All Categories
                        </div>
                        <div id="public-access-btn-holder" onclick="switchVisibleSection('public')" class=" px-2 py-2 josefin-sans font-normal" style="border-radius: 20px">
                            Public Access Trivia's
                        </div>
                    </div>
                </div>
            </div>
        </x-section>

        <x-section title="">
            <div class="flex flex-col py-4">
                <h1 class="px-4 z-20 text-center josefin-sans text-main-dark font-semibold text-2xl">Select Trivia</h1>

                <div class="flex flex-row  w-full px-2 flex-wrap">
                    <div class="flex flex-row w-full">
                        <p>Select Category</p>
                    </div>

                    <div class="flex flex-row w-full">
                        <div v-for="category in categories" class="bg-alternative-light rounded-md">
                            [[ category.name ]]
                        </div>
                    </div>

                    <select class="w-full py-2 text-xl" v-model="selectedCategory" name="selectedCategory" id="selectedCategory">
                        <option value="0">Select Category</option>
                        <option v-for="category in categories" :value="category.id">[[ category.name ]]</option>
                    </select>
                </div>

                <div class="flex flex-row  w-full px-2 flex-wrap">
                    <div class="flex flex-row w-full">
                        <p>Select Difficulty</p>
                    </div>
                    <div class="flex flex-row justify-center w-1/3 border border-lime-600 rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-lime-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span class="px-2.5 text-xl">Easy</span>
                    </div>
                    <div class="flex flex-row w-1/3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-amber-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span class="px-2.5">Medium</span>
                    </div>
                    <div class="flex flex-row w-1/3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-rose-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                        </svg>
                        <span class="px-2.5">Hard</span>
                    </div>
                </div>

                <div class="flex flex-row py-4">
                    <div class="flex flex-row  w-2/4 px-2 flex-wrap">
                        <!--
                        <select class="w-full py-2 text-xl" v-model="selectedDifficulty" name="selectedDifficulty" id="selectedDifficulty">
                            <option value="0">Select Difficulty</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                        -->
                    </div>
                </div>

                <!--
                <div class="flex flex-row py-4">
                    <div class="flex flex-row w-2/4 px-2">
                        <select class="w-full py-2 text-xl" v-model="selectedCategory" name="selectedCategory" id="selectedCategory">
                            <option value="0">Select Category</option>
                            <option v-for="category in categories" :value="category.id">[[ category.name ]]</option>
                        </select>
                    </div>
                    <div class="flex flex-row  w-2/4 px-2">
                        <select class="w-full py-2 text-xl" v-model="selectedDifficulty" name="selectedDifficulty" id="selectedDifficulty">
                            <option value="0">Select Difficulty</option>
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                </div>
                -->
            </div>
        </x-section>

        <x-section title="">
            <div id="category-holder"  class="flex flex-col">
                <div v-for="trivia in selectedTriviasByCategoryAndDifficulty">
                    <div class="flex flex-row py-4 border-2 border-slate-300 bg-slate-300 mb-2">
                        <div class="raleway flex flex-col justify-center px-4 w-3/6">
                            <span class="font-semibold ">[[ trivia.title ]] ([[ trivia.questions ]])  <span v-if="trivia.private" class="text-rose-600">Private</span> </span>
                            <div class="font-normal fira-sans flex flex-row">
                                <div v-for="index in 4">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6" :class="{'fill-amber-500' : trivia.rating >= index && trivia.rating != 0, 'stroke-amber-500' : trivia.rating >= index && trivia.rating != 0}">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                    </svg>
                                </div>
                                ([[ trivia.rating ]])
                            </div>
                            <div class="flex flex-row"><span class="font-normal">Author: </span><span class="font-semibold"> [[ trivia.author]]&nbsp</span></div>
                        </div>
                        <div class="flex flex-col justify-center px-4 w-3/6">
                            <div>
                                Difficulty: <span class="capitalize text-semibold fira-sans" :class="{'text-amber-700' : trivia.difficulty == 'medium', 'text-red-700' :  trivia.difficulty == 'hard', 'text-lime-600' : trivia.difficulty == 'easy' }">[[ trivia.difficulty ]] </span>
                            </div>
                            <div>
                                <span  class="font-semibold">[[ trivia.questions ]]</span> Questions
                            </div>
                            @if(Auth::check())
                                <div v-if="trivia.is_premium">
                                    <a href="/premium" class="flex justify-center w-full fira-sans py-2 px-2 md:px-6 shadow bg-yellow-500 text-slate-100 font-semibold text-lg shadow-gray-900 my-2">Get Premium</a>
                                </div>
                                <div v-else>
                                    <button type="button" v-on:click="startTriviaGame( trivia.id )" class="w-full fira-sans py-2 px-2 md:px-6 shadow bg-lime-600 text-slate-100 font-semibold text-lg shadow-gray-900 my-2">Play</button>
                                </div>
                            @else
                                <div>
                                    <div>
                                        <span class="">You Need To Login To Play</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div id="open-trivias-holder" class="hidden flex flex-col">
                <div v-for="openTrivia in selectedOpenTriviasByCategoryAndDifficulty" class="mb-2">
                    <div class="py-4 px-2 flex flex-row justify-between bg-slate-300 fira-sans shadow-md border-b rounded border-b-slate-400">
                        <span class="flex flex-col px-4 w-3/6">
                            <span class="font-semibold">[[ openTrivia.title ]]</span>
                            <span class="hidden md:flex">[[ openTrivia.description ]]</span>
                            <span class="flex md:hidden">[[ openTrivia.description ]]</span>
                        </span>
                        <div class="flex flex-col">
                            <span>Date Created: [[ openTrivia.created_at_date ]]</span>
                            <span>Time: [[ openTrivia.created_at_time ]]</span>
                        </div>
                    </div>
                    <div class="flex flex-col md:flex-row justify-between py-4 border-b border-b-slate-300 bg-slate-200 px-2 fira-sans font-normal">
                        <div class="flex flex-row w-full md:w-5/6">
                            <div class="hidden md:flex">
                                <div class="px-4 flex flex-col justify-center">
                                    <div>
                                        Created By: <span class="font-semibold">[[openTrivia.author ]]</span>
                                    </div>
                                </div>
                                <div class="px-4 flex flex-col justify-center">
                                    <div>
                                        Difficulty: <span class="font-semibold capitalize" :class="{'text-amber-700' : openTrivia.difficulty == 'medium', 'text-red-700' :  openTrivia.difficulty == 'hard', 'text-lime-600' : openTrivia.difficulty == 'easy' }">[[ openTrivia.difficulty ]]</span>
                                    </div>
                                </div>
                                <div class="px-4 flex flex-col justify-center">
                                    <div>
                                        <span class="font-semibold">[[ openTrivia.questions ]]</span> Questions
                                    </div>
                                </div>
                                <div class="px-4 flex flex-col justify-center">
                                    <div>
                                        <span class="font-semibold">[[ openTrivia.connected_players ]]/ <span v-if="openTrivia.player_limit_enabled">[[ openTrivia.player_limit ]]</span> <span v-else>&infin;</span> Players </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex md:hidden">
                                <div class="px-4 flex flex-col justify-center w-2/6">
                                    <div>
                                        Created By: <span class="font-semibold">[[openTrivia.author ]]</span>
                                    </div>
                                </div>
                                <div class="px-4 flex flex-col justify-center w-2/6">
                                    <div>
                                        Difficulty: <span class="font-semibold capitalize" :class="{'text-amber-700' : openTrivia.difficulty == 'medium', 'text-red-700' :  openTrivia.difficulty == 'hard', 'text-lime-600' : openTrivia.difficulty == 'easy' }">[[ openTrivia.difficulty ]]</span>
                                    </div>
                                </div>
                                <div class="px-4 flex flex-col justify-center w-2/6">
                                    <div>
                                        <span class="font-semibold">[[ openTrivia.questions ]]</span> Questions
                                    </div>
                                    <div>
                                        <span class="font-semibold">[[ openTrivia.connected_players ]]/ <span v-if="openTrivia.player_limit_enabled">[[ openTrivia.player_limit ]]</span> <span v-else>&infin;</span>  Players </span> Players
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-row w-full md:w-1/6">
                            @if(Auth::check())
                                <a v-bind:href="'/join/' + openTrivia.token" class="flex justify-center w-full fira-sans py-2 px-2 md:px-6 shadow bg-lime-600  text-slate-100 font-semibold text-lg shadow-gray-900 my-2">Join</a>
                            @else
                                <span>You Need To Login To Play</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </x-section>

        @if(Auth::check())
        <div class="fixed bottom-0 left-2 flex flex-row justify-center">

            <div>
                <button type="button" v-on:click="openRandomTriviaModal()" class="w-full text-center fira-sans py-4 px-2 md:px-6 shadow bg-yellow-500 text-zinc-900 font-semibold text-xl shadow-md shadow-zinc-900">Play Random Quiz</button>
            </div>
        </div>
        @endif
    </div>

    @if(Auth::check())

    <x-section title="">
        <div class="flex flex-col bg-zinc-700 py-4">
            <h1 class="px-4 z-20 text-center josefin-sans text-yellow-400 font-semibold text-2xl">Manage your Quizzes</h1>

            <div class="flex flex-row raleway py-2 px-2 text-slate-200 font-bold">
                <div>Total Trivia's: <span class="pr-4 pl-2">{{ $usersTrivias->count() }}</span></div>
                <div>Active Trivia's: <span class="pr-4 pl-2">{{ $usersTrivias->where('is_active', 1)->count() }}</span></div>
                <div>Total Times Played: <span class="pr-4 pl-2">0</span></div>
            </div>
            <div class="flex flex-row py-2 px-2">
                <div>
                    <x-btn-primary isALink="{{ true }}" link="/trv/management">Manage</x-btn-primary>
                </div>
            </div>
        </div>
    </x-section>

    @endif



    <script>
        const { createApp } = Vue
        createApp({
            data() {
                return {
                    message: 'Hello Vue!',
                    categories: [
                        @foreach($categories as $cat)
                        {
                            'id' : {{ $cat['id'] }},
                            'name' : '{{ $cat['name'] }}',
                        },
                        @endforeach
                    ],
                    trivias : [
                        @foreach($categories as $cat)
                        {
                            'id' : {{ $cat['id'] }},
                            'trivias' : [
                                @foreach($cat->availableTrivia as $trivia)
                                {
                                    'id' : {{ $trivia['id'] }},
                                    'title' : '{{ $trivia['title'] }}',
                                    'description' : '{{ $trivia['description'] }}',
                                    'difficulty' : '{{ $trivia['difficulty'] }}',
                                    'questions' : {{ $trivia->questions->count() }},
                                    'author' : '{{ $trivia->user->username }}',
                                    'rating' : {{ ($trivia->getRating())['rating'] }},
                                    'rating_count' : {{ ($trivia->getRating())['count'] }},
                                    'is_premium' : {{ $trivia->is_premium }},
                                    'private' : {{ $trivia->private }},
                                    'category_id' : {{ $trivia->category_id }},
                                },
                                @endforeach
                            ]
                        },
                        @endforeach
                    ],
                    openTrivias : [
                        @foreach($openTrivias as $trivia)
                        {
                            'id' : {{ $trivia->id }},
                            'title' : '{{ $trivia->trivia->title }}',
                            'description' : '{{ $trivia->trivia->description }}',
                            'difficulty' : '{{ $trivia->trivia->difficulty }}',
                            'questions' : {{ $trivia->trivia->questions->count() }},
                            'author' : '{{ $trivia->user->username }}',
                            'private' : {{ $trivia->trivia->private }},
                            'category_id' : {{ $trivia->trivia->category_id }},
                            'created_at_date' : '{{ $trivia->created_at->format('d/m/Y') }}',
                            'created_at_time' : '{{ $trivia->created_at->format('H:i') }}',
                            'connected_players' : {{ $trivia->gameInstance->playerInstances->count() }},
                            'token' : '{{ $trivia->gameInstance->token }}',
                            'player_limit' : @if((int)GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit_enabled') == 1 ) {{ GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit') }} @else '' @endif,
                            'player_limit_enabled' : {{ (int)GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit_enabled') }},
                        },
                        @endforeach
                    ],
                    selectedCategory: 0,
                    selectedDifficulty: 'any',
                }
            },
            methods: {
                getTrivias() {
                    if(this.selectedCategory == 0 && this.selectedDifficulty == 'any') {
                        this.message = 'All Trivia\'s';
                    } else if(this.selectedCategory != 0 && this.selectedDifficulty == 'any') {
                        this.message = 'Trivia\'s in ' + this.categories[this.selectedCategory - 1].name + ' Category';
                    } else if(this.selectedCategory == 0 && this.selectedDifficulty != 'any') {
                        this.message = 'Trivia\'s with ' + this.selectedDifficulty + ' Difficulty';
                    } else {
                        this.message = 'Trivia\'s in ' + this.categories[this.selectedCategory - 1].name + ' Category with ' + this.selectedDifficulty + ' Difficulty';
                    }
                },
                startTriviaGame(triviaId) {
                    console.log(triviaId);

                    fetch('/trivia', {
                        'method': 'POST',
                        'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                        'body': JSON.stringify({'trivia_id': triviaId})
                    })
                        .then(response => response.json())
                        .then(data => {

                            if (data.status == false) {
                                alert(data.message);
                                return;
                            }
                            console.log(typeof GameApi)
                            console.log(GameApi);
                            GameApi.addGameInstance(data.data.token, data.data);

                            console.log(data);
                            window.location.href = '/trv/trivia/' + data.data.token;
                        })
                        .catch(error => console.log(error));
                },
                openRandomTriviaModal() {
                    let title = 'Create Auto Generated Trivia';

                    //create form for modal body to submit request to create trivia from all available questions in category
                    let form = document.createElement('form');
                    form.setAttribute('method', 'POST');
                    form.setAttribute('action', '/trivia/random');
                    form.setAttribute('id', 'random-trivia-form');
                    form.setAttribute('class', 'flex flex-row flex-wrap justify-center w-full');

                    //create div to hold trivia title and description
                    let triviaInfoDiv = document.createElement('div');
                    triviaInfoDiv.setAttribute('class', 'flex flex-col w-full md:w-4/6 px-2');

                    //create input for trivia title
                    let titleInput = document.createElement('input');
                    titleInput.setAttribute('type', 'text');
                    titleInput.setAttribute('name', 'title');
                    titleInput.setAttribute('id', 'title');
                    titleInput.setAttribute('placeholder', 'Trivia Title');
                    titleInput.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2 text-xl');

                    //create input for trivia description
                    let descriptionInput = document.createElement('input');
                    descriptionInput.setAttribute('type', 'text');
                    descriptionInput.setAttribute('name', 'description');
                    descriptionInput.setAttribute('id', 'description');
                    descriptionInput.setAttribute('placeholder', 'Trivia Description');
                    descriptionInput.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2 text-xl');

                    triviaInfoDiv.appendChild(titleInput);
                    triviaInfoDiv.appendChild(descriptionInput);

                    //create input for trivia category
                    let categoryInput = document.createElement('select');
                    categoryInput.setAttribute('name', 'category');
                    categoryInput.setAttribute('id', 'category');
                    categoryInput.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2 text-xl');

                    //create options for category input
                    let anyCategoryOption = document.createElement('option');
                    anyCategoryOption.setAttribute('value', '23');
                    anyCategoryOption.innerText = 'Any Category';

                    //append options to category input
                    categoryInput.appendChild(anyCategoryOption);

                    let categoryOptions = [
                            @foreach($categories as $cat)
                        {
                            'id' : {{ $cat['id'] }},
                            'name' : '{{ $cat['name'] }}',
                        },
                        @endforeach
                    ];

                    categoryOptions.forEach(category => {
                        let option = document.createElement('option');
                        option.setAttribute('value', category.id);
                        option.innerText = category.name;
                        categoryInput.appendChild(option);
                    });

                    triviaInfoDiv.appendChild(categoryInput);

                    //create input for trivia difficulty
                    let difficultyInput = document.createElement('select');
                    difficultyInput.setAttribute('name', 'difficulty');
                    difficultyInput.setAttribute('id', 'difficulty');
                    difficultyInput.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2 text-xl');

                    //create options for difficulty input

                    let anyOption = document.createElement('option');
                    anyOption.setAttribute('value', 'any');
                    anyOption.innerText = 'Any Difficulty';

                    let easyOption = document.createElement('option');
                    easyOption.setAttribute('value', 'easy');
                    easyOption.innerText = 'Easy';

                    let mediumOption = document.createElement('option');
                    mediumOption.setAttribute('value', 'medium');
                    mediumOption.innerText = 'Medium';

                    let hardOption = document.createElement('option');
                    hardOption.setAttribute('value', 'hard');
                    hardOption.innerText = 'Hard';

                    //append options to difficulty input
                    difficultyInput.appendChild(anyOption);
                    difficultyInput.appendChild(easyOption);
                    difficultyInput.appendChild(mediumOption);
                    difficultyInput.appendChild(hardOption);

                    //add question count input
                    let questionCountInput = document.createElement('input');
                    questionCountInput.setAttribute('type', 'number');
                    questionCountInput.setAttribute('name', 'question_count');
                    questionCountInput.setAttribute('id', 'question_count');
                    questionCountInput.setAttribute('placeholder', 'Number of Questions');
                    questionCountInput.setAttribute('class', 'border border-slate-400 shadow shadow-slate-400 rounded py-1 px-2 mb-2 text-xl');


                    triviaInfoDiv.appendChild(difficultyInput);
                    triviaInfoDiv.appendChild(questionCountInput);

                    //create input for csrf token for submit form
                    let csrfInput = document.createElement('input');
                    csrfInput.setAttribute('type', 'hidden');
                    csrfInput.setAttribute('name', '_token');
                    csrfInput.setAttribute('id', '_token');
                    csrfInput.setAttribute('value', '{{ csrf_token() }}');

                    //create submit button
                    let submitButton = document.createElement('button');
                    submitButton.setAttribute('type', 'submit');
                    submitButton.setAttribute('class', 'py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold');
                    submitButton.innerText = 'Create Trivia';

                    triviaInfoDiv.appendChild(submitButton);

                    //form.appendChild(categoryInput);
                    form.appendChild(csrfInput);
                    form.appendChild(triviaInfoDiv);

                    GameApi.openModal('game-modal', title, form);
                }
            },
            computed: {
                selectedTriviasByCategoryAndDifficulty() {

                    //TODO: show all trivias if no category or difficulty is selected
                    if (this.selectedCategory == 0) {

                        let triviaList = [];

                        this.trivias.forEach(category => {
                            category.trivias.forEach(trivia => {
                                triviaList.push(trivia);
                            });
                        });

                        if (this.selectedDifficulty == 'any' || this.selectedDifficulty == 0) {
                            return triviaList;
                        }

                        let filteredByDifficulty = triviaList.filter(trivia => trivia.difficulty == this.selectedDifficulty);
                        return filteredByDifficulty;
                    }


                    let filteredByCategory = this.trivias.filter(trivia => trivia.id == this.selectedCategory);

                    if (filteredByCategory.length == 0) {
                        return [];
                    }

                    if (this.selectedDifficulty == 'any' || this.selectedDifficulty == 0) {
                        return filteredByCategory[0].trivias;
                    }

                    let filteredByDifficulty = filteredByCategory[0].trivias.filter(trivia => trivia.difficulty == this.selectedDifficulty);
                    return filteredByDifficulty;
                },
                selectedOpenTriviasByCategoryAndDifficulty() {

                    if (this.selectedCategory == 0) {
                        let tmpOpentrivias = [];

                        this.openTrivias.forEach(trivia => {
                            tmpOpentrivias.push(trivia);
                        });

                        if (this.selectedDifficulty == 'any' || this.selectedDifficulty == 0) {
                            return tmpOpentrivias;
                        }

                        let filteredByDifficulty = tmpOpentrivias.filter(trivia => trivia.difficulty == this.selectedDifficulty);
                        return filteredByDifficulty;
                    }

                    let filteredByCategory = this.openTrivias.filter(trivia => trivia.category_id == this.selectedCategory);
                    if (filteredByCategory.length == 0) {
                        return [];
                    }

                    if (this.selectedDifficulty == 'any' || this.selectedDifficulty == 0) {
                        return filteredByCategory;
                    }

                    let filteredByDifficulty = filteredByCategory.filter(trivia => trivia.difficulty == this.selectedDifficulty);
                    return filteredByDifficulty;
                },
                joinUrl(token) {
                    return '/join/' + token;
                }
            },
            //change delimiters
            delimiters: ['[[', ']]']
        }).mount('#game-app')

    </script>

    <script>

        function expandCategory(categoryId) {

            //hide expand button and show collapse button
            document.getElementById('expanded-cat-' + categoryId).classList.remove('hidden');
            document.getElementById('collapsed-cat-' + categoryId).classList.add('hidden');

            //show all trivia's in category
            document.getElementById('trivia-holder-' + categoryId).classList.remove('hidden');
        }

        function hideCategory(categoryId) {

            //hide collapse button and show expand button
            document.getElementById('expanded-cat-' + categoryId).classList.add('hidden');
            document.getElementById('collapsed-cat-' + categoryId).classList.remove('hidden');

            //hide all trivia's in category
            document.getElementById('trivia-holder-' + categoryId).classList.add('hidden');
        }

        function rateTrivia(triviaId, rating) {
            fetch('/trv/trivia/rating', {'method' : 'POST', 'body': JSON.stringify({'trivia_id' : triviaId, 'rating' : rating}), 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN' : '{{ csrf_token() }}'}})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if(data.success) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
        }

        function selectedBox(box) {
            if (box == 'categories') {
                document.getElementById('category-holder').classList.remove('hidden');
                document.getElementById('open-trivias-holder').classList.add('hidden');

                document.getElementById('all-categories-nav').classList.add('bg-slate-300');
                document.getElementById('all-categories-nav').classList.remove('bg-slate-100');

                document.getElementById('open-trivias-nav').classList.add('bg-slate-100');
                document.getElementById('open-trivias-nav').classList.remove('bg-slate-300');

            } else {
                document.getElementById('category-holder').classList.add('hidden');
                document.getElementById('open-trivias-holder').classList.remove('hidden');

                document.getElementById('all-categories-nav').classList.add('bg-slate-100');
                document.getElementById('all-categories-nav').classList.remove('bg-slate-300');

                document.getElementById('open-trivias-nav').classList.add('bg-slate-300');
                document.getElementById('open-trivias-nav').classList.remove('bg-slate-100');
            }
        }

        function switchVisibleSection(sectionName = 'categories')
        {

            //first change outlined button
            if (sectionName == 'categories') {

                let allCatsBtnHolder = document.getElementById('all-cats-btn-holder');
                allCatsBtnHolder.classList.add('shadow-md');
                allCatsBtnHolder.classList.add('text-slate-200');
                //allCatsBtnHolder.classList.add('border-2');
                allCatsBtnHolder.classList.add('bg-main-accent');

                let publicAccessBtnHolder = document.getElementById('public-access-btn-holder');
                publicAccessBtnHolder.classList.remove('shadow-md');
                publicAccessBtnHolder.classList.remove('text-slate-200');
                //publicAccessBtnHolder.classList.remove('border-2');
                publicAccessBtnHolder.classList.remove('bg-main-accent');


                document.getElementById('category-holder').classList.remove('hidden');
                document.getElementById('open-trivias-holder').classList.add('hidden');

            }

            if (sectionName == 'public') {

                let publicAccessBtnHolder = document.getElementById('public-access-btn-holder');
                publicAccessBtnHolder.classList.add('shadow-md');
                publicAccessBtnHolder.classList.add('bg-main-accent');
                publicAccessBtnHolder.classList.add('text-slate-200');
                //publicAccessBtnHolder.classList.add('border-2');
                //publicAccessBtnHolder.classList.add('border-lime-700');
                //bg-main-accent px-2 py-2 rounded-lg text-slate-200 josefin-sans shadow-md

                let allCatsBtnHolder = document.getElementById('all-cats-btn-holder');
                allCatsBtnHolder.classList.remove('shadow-md');
                allCatsBtnHolder.classList.remove('bg-main-accent');
                allCatsBtnHolder.classList.remove('text-slate-200');
                //allCatsBtnHolder.classList.remove('border-2');
                //allCatsBtnHolder.classList.remove('border-lime-700');

                document.getElementById('category-holder').classList.add('hidden');
                document.getElementById('open-trivias-holder').classList.remove('hidden');

            }

        }

    </script>
@endsection