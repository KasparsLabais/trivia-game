@extends('game-api::layout')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        /* Play Random Quiz: constant pulse + a short twitch every ~4s. */
        .random-quiz-pulse { animation: random-quiz-pulse 1.4s ease-in-out infinite; }
        .random-quiz-twitch { animation: random-quiz-twitch 4s ease-in-out infinite; }
        .random-quiz-twitch:hover, .random-quiz-twitch:hover .random-quiz-pulse { animation-play-state: paused; }
        @keyframes random-quiz-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes random-quiz-twitch {
            0%, 84%, 100% { transform: rotate(0); }
            86% { transform: rotate(-4deg); }
            88% { transform: rotate(4deg); }
            90% { transform: rotate(-3deg); }
            92% { transform: rotate(3deg); }
            94% { transform: rotate(-1deg); }
            96% { transform: rotate(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .random-quiz-pulse, .random-quiz-twitch { animation: none; }
        }
    </style>
    <div id="game-app">
        {{-- Trivia folder: tabs switch between "All Categories" and "Public Games",
             like opening different document folders. Side padding matches the
             nav and footer (15px / 30px) so all blocks line up. --}}
        <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-8">

            {{-- Folder tabs. Fixed heights so switching never changes the row height:
                 active = 52px with -mb-1 (overlaps the folder's top border, so it
                 reads as part of the folder), inactive = 44px sitting behind. --}}
            <div class="relative z-10 flex items-end gap-2 pl-3 md:pl-6" role="tablist">
                <button type="button" role="tab" :aria-selected="activeSection == 'categories'" @click="setSection('categories')"
                        class="flex items-center gap-2 md:gap-3 border-4 border-b-0 border-main-dark px-3 md:px-5 russo uppercase tracking-wide text-xs md:text-sm transition-colors duration-100"
                        :class="activeSection == 'categories' ? 'bg-white text-main-dark h-[52px] -mb-1' : 'bg-surface text-main-dark/60 h-11 hover:bg-main-light hover:text-main-dark'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 md:w-5 md:h-5 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" />
                    </svg>
                    <span>All Categories</span>
                    <span class="min-w-[1.75rem] text-center border-2 border-main-dark px-1.5 py-0.5 text-[11px] leading-none"
                          :class="activeSection == 'categories' ? 'bg-main-dark text-white' : 'bg-white text-main-dark'"
                          :title="selectedTriviasByCategoryAndDifficulty.length + ' quizzes available'">[[ selectedTriviasByCategoryAndDifficulty.length ]]</span>
                </button>
                <button type="button" role="tab" :aria-selected="activeSection == 'public'" @click="setSection('public')"
                        class="flex items-center gap-2 md:gap-3 border-4 border-b-0 border-main-dark px-3 md:px-5 russo uppercase tracking-wide text-xs md:text-sm transition-colors duration-100"
                        :class="activeSection == 'public' ? 'bg-white text-main-dark h-[52px] -mb-1' : 'bg-surface text-main-dark/60 h-11 hover:bg-main-light hover:text-main-dark'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 md:w-5 md:h-5 shrink-0" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                    <span>Public Games</span>
                    {{-- Live games are worth a look: red badge with a pulsing dot. --}}
                    <span class="relative min-w-[1.75rem] text-center border-2 border-main-dark px-1.5 py-0.5 text-[11px] leading-none"
                          :class="selectedOpenTriviasByCategoryAndDifficulty.length ? 'bg-main-accent text-white' : (activeSection == 'public' ? 'bg-main-dark text-white' : 'bg-white text-main-dark')"
                          :title="selectedOpenTriviasByCategoryAndDifficulty.length + ' public games open to join'">
                        [[ selectedOpenTriviasByCategoryAndDifficulty.length ]]
                        <span v-if="selectedOpenTriviasByCategoryAndDifficulty.length" class="absolute -top-1.5 -right-1.5 flex h-3 w-3" aria-hidden="true">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-main-accent opacity-75"></span>
                            <span class="relative inline-flex h-3 w-3 rounded-full bg-main-accent border-2 border-main-dark"></span>
                        </span>
                    </span>
                </button>
            </div>

            {{-- Folder body --}}
            <div class="relative bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B]">

                {{-- Filters (apply to both folders) --}}
                <div class="flex flex-col md:flex-row md:items-end gap-4 md:gap-8 px-5 md:px-8 py-5 border-b-4 border-main-dark bg-main-light">
                    <div class="flex flex-col gap-1.5 w-full md:w-72">
                        <label for="selectedCategory" class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Category</label>
                        <div class="relative">
                            <select v-model="selectedCategory" name="selectedCategory" id="selectedCategory"
                                    class="w-full appearance-none bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] pl-3 pr-10 py-2 fira-sans font-bold text-main-dark focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] cursor-pointer">
                                <option value="0">All categories</option>
                                <option v-for="category in categories" :value="category.id">[[ category.name ]]</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                            </span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Difficulty</span>
                        {{-- Difficulty tags: all on = show all; click to toggle a difficulty off (filtered out). --}}
                        <div class="flex flex-row flex-wrap gap-2">
                            <button type="button" @click="toggleDifficulty('easy')" :aria-pressed="activeDifficulties.easy"
                                    class="border-4 border-main-dark px-4 py-1.5 russo text-sm uppercase tracking-wide transition-all duration-100"
                                    :class="activeDifficulties.easy ? 'bg-green-500 text-main-dark shadow-[3px_3px_0_0_#14323B]' : 'bg-white text-main-dark opacity-50'">
                                Easy
                            </button>
                            <button type="button" @click="toggleDifficulty('medium')" :aria-pressed="activeDifficulties.medium"
                                    class="border-4 border-main-dark px-4 py-1.5 russo text-sm uppercase tracking-wide transition-all duration-100"
                                    :class="activeDifficulties.medium ? 'bg-amber-400 text-main-dark shadow-[3px_3px_0_0_#14323B]' : 'bg-white text-main-dark opacity-50'">
                                Medium
                            </button>
                            <button type="button" @click="toggleDifficulty('hard')" :aria-pressed="activeDifficulties.hard"
                                    class="border-4 border-main-dark px-4 py-1.5 russo text-sm uppercase tracking-wide transition-all duration-100"
                                    :class="activeDifficulties.hard ? 'bg-main-accent text-white shadow-[3px_3px_0_0_#14323B]' : 'bg-white text-main-dark opacity-50'">
                                Hard
                            </button>
                        </div>
                    </div>

                    @if(Auth::check())
                        {{-- Play Random Quiz: right-aligned on desktop, full width on mobile.
                             Outer = occasional twitch, inner = constant pulse, button = hover/press. --}}
                        <div class="random-quiz-twitch w-full md:w-auto md:ml-auto">
                            <div class="random-quiz-pulse">
                                <button type="button" v-on:click="openRandomTriviaModal()"
                                        class="w-full flex items-center justify-center gap-2.5 bg-pop text-main-dark border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-5 py-2.5 russo uppercase tracking-wide text-sm md:text-base transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">
                                    {{-- Die --}}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-6 h-6 shrink-0" aria-hidden="true">
                                        <rect x="3" y="3" width="18" height="18" fill="#fff" stroke="#14323B" stroke-width="2.5"/>
                                        <circle cx="8" cy="8" r="1.6" fill="#14323B"/><circle cx="16" cy="8" r="1.6" fill="#14323B"/>
                                        <circle cx="12" cy="12" r="1.6" fill="#14323B"/>
                                        <circle cx="8" cy="16" r="1.6" fill="#14323B"/><circle cx="16" cy="16" r="1.6" fill="#14323B"/>
                                    </svg>
                                    Play Random Quiz
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- ALL CATEGORIES folder --}}
                <div v-show="activeSection == 'categories'" id="category-holder" class="p-5 md:p-8">
                    <div v-if="selectedTriviasByCategoryAndDifficulty.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2">
                        <div v-for="trivia in selectedTriviasByCategoryAndDifficulty">
                            <div class="relative mb-6">
                                {{-- Folder tab — difficulty label + colour --}}
                                <div class="inline-block ml-4 px-4 py-1 border-4 border-b-0 border-main-dark russo text-xs uppercase tracking-widest capitalize"
                                     :class="{'bg-green-500 text-main-dark' : trivia.difficulty == 'easy', 'bg-amber-400 text-main-dark' : trivia.difficulty == 'medium', 'bg-main-accent text-white' : trivia.difficulty == 'hard'}">
                                    [[ trivia.difficulty ]]
                                </div>
                                {{-- Folder body --}}
                                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 flex flex-col gap-3">
                                    {{-- Top row: category icon (colour = difficulty) + question count & rating --}}
                                    <div class="flex flex-row items-start justify-between gap-2">
                                        <div class="w-14 h-14 border-2 border-main-dark flex items-center justify-center shrink-0"
                                             :class="{'bg-green-500' : trivia.difficulty == 'easy', 'bg-amber-400' : trivia.difficulty == 'medium', 'bg-main-accent' : trivia.difficulty == 'hard'}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-7 h-7" :class="{'stroke-white' : trivia.difficulty == 'hard', 'stroke-main-dark' : trivia.difficulty != 'hard'}">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="getCategoryIcon(trivia.category_id)" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="fira-sans text-main-dark leading-none text-right"><span class="font-black text-xl">[[ trivia.questions ]]</span><br><span class="text-xs uppercase tracking-wide">questions</span></span>
                                            <div class="flex flex-row">
                                                <svg v-for="index in 4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="w-4 h-4" :class="{'fill-amber-400 stroke-amber-400' : trivia.rating >= index && trivia.rating != 0, 'stroke-main-dark' : !(trivia.rating >= index && trivia.rating != 0)}">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Title with the highlighter-marker swash (same motif as the profile page's x-card headers) --}}
                                    <h3 class="relative self-start inline-block max-w-full mt-1">
                                        <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                                        <span class="relative block russo text-main-dark text-sm md:text-base tracking-wider uppercase leading-snug px-3 py-1 break-words">[[ trivia.title ]]<span v-if="trivia.private" class="ml-1 text-main-accent text-xs">&middot; PRIVATE</span></span>
                                    </h3>
                                    <div class="fira-sans text-sm text-main-dark">by <span class="font-bold text-brand">[[ trivia.author ]]</span></div>
                                    {{-- Play --}}
                                    @if(Auth::check())
                                        <a v-if="trivia.is_premium" href="/premium" class="block w-full text-center fira-sans py-2.5 border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] bg-pop text-main-dark font-black uppercase tracking-wide transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Get Premium</a>
                                        <button v-else type="button" v-on:click="startTriviaGame( trivia.id )" class="w-full fira-sans py-2.5 border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] bg-main-accent text-white font-black uppercase tracking-wide transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Play</button>
                                    @else
                                        <span class="fira-sans text-sm text-main-dark font-semibold">Log in to play</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-10 text-center">
                        <p class="russo text-main-dark/60 text-xl">This folder is empty</p>
                        <p class="fira-sans text-main-dark/60 mt-1">No quizzes match these filters &mdash; try another category or difficulty.</p>
                    </div>
                </div>

                {{-- PUBLIC GAMES folder: live games anyone can join --}}
                <div v-show="activeSection == 'public'" id="open-trivias-holder" class="p-5 md:p-8">
                    <div v-if="selectedOpenTriviasByCategoryAndDifficulty.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-2">
                        <div v-for="openTrivia in selectedOpenTriviasByCategoryAndDifficulty">
                            <div class="relative mb-6">
                                {{-- Folder tab — difficulty label + colour --}}
                                <div class="inline-block ml-4 px-4 py-1 border-4 border-b-0 border-main-dark russo text-xs uppercase tracking-widest capitalize"
                                     :class="{'bg-green-500 text-main-dark' : openTrivia.difficulty == 'easy', 'bg-amber-400 text-main-dark' : openTrivia.difficulty == 'medium', 'bg-main-accent text-white' : openTrivia.difficulty == 'hard'}">
                                    [[ openTrivia.difficulty ]]
                                </div>
                                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 flex flex-col gap-3">
                                    <div class="flex flex-row items-start justify-between gap-2">
                                        <div class="w-14 h-14 border-2 border-main-dark flex items-center justify-center shrink-0"
                                             :class="{'bg-green-500' : openTrivia.difficulty == 'easy', 'bg-amber-400' : openTrivia.difficulty == 'medium', 'bg-main-accent' : openTrivia.difficulty == 'hard'}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" class="w-7 h-7" :class="{'stroke-white' : openTrivia.difficulty == 'hard', 'stroke-main-dark' : openTrivia.difficulty != 'hard'}">
                                                <path stroke-linecap="round" stroke-linejoin="round" :d="getCategoryIcon(openTrivia.category_id)" />
                                            </svg>
                                        </div>
                                        <div class="flex flex-col items-end gap-1.5">
                                            <span class="flex items-center gap-1.5 bg-green-500 text-white border-2 border-main-dark px-1.5 py-0.5 russo text-[10px] uppercase tracking-wide">
                                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse" aria-hidden="true"></span>Live
                                            </span>
                                            <span class="fira-sans text-main-dark leading-none text-right"><span class="font-black text-xl">[[ openTrivia.questions ]]</span> <span class="text-xs uppercase tracking-wide">questions</span></span>
                                        </div>
                                    </div>
                                    {{-- Title with the highlighter-marker swash (same motif as the profile page's x-card headers) --}}
                                    <h3 class="relative self-start inline-block max-w-full mt-1">
                                        <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                                        <span class="relative block russo text-main-dark text-sm md:text-base tracking-wider uppercase leading-snug px-3 py-1 break-words">[[ openTrivia.title ]]</span>
                                    </h3>
                                    <p v-if="openTrivia.description" class="fira-sans text-sm text-main-dark/70 line-clamp-2">[[ openTrivia.description ]]</p>
                                    <div class="fira-sans text-sm text-main-dark">hosted by <span class="font-bold text-brand">[[ openTrivia.author ]]</span></div>
                                    {{-- Players + opened at --}}
                                    <div class="flex flex-row items-stretch border-2 border-main-dark text-main-dark">
                                        <div class="flex-1 px-3 py-1.5 border-r-2 border-main-dark">
                                            <div class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60">Players</div>
                                            <div class="russo text-base">[[ openTrivia.connected_players ]] / <span v-if="openTrivia.player_limit_enabled">[[ openTrivia.player_limit ]]</span><span v-else>&infin;</span></div>
                                        </div>
                                        <div class="flex-1 px-3 py-1.5 bg-main-light">
                                            <div class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60">Opened</div>
                                            <div class="fira-sans font-bold text-sm">[[ openTrivia.created_at_date ]] [[ openTrivia.created_at_time ]]</div>
                                        </div>
                                    </div>
                                    @if(Auth::check())
                                        <a :href="'/join/' + openTrivia.token" class="block w-full text-center fira-sans py-2.5 border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] bg-green-500 text-white font-black uppercase tracking-wide transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Join</a>
                                    @else
                                        <span class="fira-sans text-sm text-main-dark font-semibold">Log in to play</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-10 text-center">
                        <p class="russo text-main-dark/60 text-xl">No public games right now</p>
                        <p class="fira-sans text-main-dark/60 mt-1">Check back soon, or start a quiz from the All Categories folder.</p>
                    </div>
                </div>
            </div>
        </div>

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
                    // Open folder: 'categories' | 'public' (kept in the URL hash).
                    activeSection: window.location.hash == '#public' ? 'public' : 'categories',
                    selectedCategory: 0,
                    // All difficulties on by default; toggling one off filters it out.
                    activeDifficulties: { easy: true, medium: true, hard: true },
                }
            },
            methods: {
                // Switch folder tab; remember it in the URL so reload/links keep it.
                setSection(section) {
                    this.activeSection = section;
                    try { history.replaceState(null, '', section == 'public' ? '#public' : window.location.pathname + window.location.search); } catch (e) {}
                },
                // Toggle a difficulty tag on/off. On = that difficulty is shown.
                toggleDifficulty(level) {
                    this.activeDifficulties[level] = !this.activeDifficulties[level];
                },
                // Category -> outline icon (heroicons path). Unmapped categories
                // fall back to a folder icon.
                getCategoryIcon(categoryId) {
                    const icons = {
                        1:  'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5',
                        2:  'M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25',
                        3:  'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.348c0-.717.772-1.167 1.395-.81l5.603 3.213c.63.36.63 1.267 0 1.627l-5.603 3.213c-.623.357-1.395-.093-1.395-.81V9.348Z',
                        4:  'M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163V5.25a2.25 2.25 0 0 0-1.632-2.163l-1.32-.377a1.803 1.803 0 0 0-.99 3.467m-6 5.25v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 12.553V5.25',
                        5:  'M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z',
                        7:  'M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 0 1-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 0 0-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 0 1-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 0 0 .657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 0 1-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 0 0 5.427-.63 48.05 48.05 0 0 0 .582-4.717.532.532 0 0 0-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 0 0 .658-.663 48.422 48.422 0 0 0-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 0 1-.61-.58v0Z',
                        8:  'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z',
                        9:  'M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z',
                        10: 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z',
                        12: 'M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418',
                        13: 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                        14: 'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25',
                        15: 'M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z',
                        16: 'M14.25 6.087c0-.355.186-.676.401-.959.221-.29.349-.634.349-1.003 0-1.036-1.007-1.875-2.25-1.875s-2.25.84-2.25 1.875c0 .369.128.713.349 1.003.215.283.401.604.401.959v0a.64.64 0 0 1-.657.643 48.39 48.39 0 0 1-4.163-.3c.186 1.613.293 3.25.315 4.907a.656.656 0 0 1-.658.663v0c-.355 0-.676-.186-.959-.401a1.647 1.647 0 0 0-1.003-.349c-1.036 0-1.875 1.007-1.875 2.25s.84 2.25 1.875 2.25c.369 0 .713-.128 1.003-.349.283-.215.604-.401.959-.401v0c.31 0 .555.26.532.57a48.039 48.039 0 0 1-.642 5.056c1.518.19 3.058.309 4.616.354a.64.64 0 0 0 .657-.643v0c0-.355-.186-.676-.401-.959a1.647 1.647 0 0 1-.349-1.003c0-1.035 1.008-1.875 2.25-1.875 1.243 0 2.25.84 2.25 1.875 0 .369-.128.713-.349 1.003-.215.283-.4.604-.4.959v0c0 .333.277.599.61.58a48.1 48.1 0 0 0 5.427-.63 48.05 48.05 0 0 0 .582-4.717.532.532 0 0 0-.533-.57v0c-.355 0-.676.186-.959.401-.29.221-.634.349-1.003.349-1.035 0-1.875-1.007-1.875-2.25s.84-2.25 1.875-2.25c.37 0 .713.128 1.003.349.283.215.604.401.96.401v0a.656.656 0 0 0 .658-.663 48.422 48.422 0 0 0-.37-5.36c-1.886.342-3.81.574-5.766.689a.578.578 0 0 1-.61-.58v0Z',
                        17: 'M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z',
                        18: 'M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0',
                        19: 'M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z',
                        20: 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z',
                        21: 'M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12',
                        22: 'M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42',
                        24: 'M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.87c1.355 0 2.697.055 4.024.165C17.155 8.51 18 9.473 18 10.608v2.513m-9 4.242 1.5.75a3.354 3.354 0 0 0 3 0 3.354 3.354 0 0 1 3 0 3.354 3.354 0 0 0 3 0L21 16.5m-18 0 1.5.75a3.354 3.354 0 0 0 3 0M3 16.5v4.125c0 .621.504 1.125 1.125 1.125h15.75c.621 0 1.125-.504 1.125-1.125V16.5M3 16.5l1.5-3.38m0 0A47.78 47.78 0 0 1 6 13.12m12 0c.513.065 1.022.148 1.526.246l.974 2.754',
                        25: 'm10.5 21 5.25-11.25L21 21m-9-3h7.5M3 5.621a48.474 48.474 0 0 1 6-.371m0 0c1.12 0 2.233.038 3.334.114M9 5.25V3m3.334 2.364C11.176 10.658 7.69 15.08 3 17.502m9.334-12.138c.896.061 1.785.147 2.666.257m-4.589 8.495a18.023 18.023 0 0 1-3.827-5.802',
                    };
                    return icons[categoryId] || 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z';
                },
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
                    let triviaList = [];
                    if (this.selectedCategory == 0) {
                        this.trivias.forEach(category => {
                            category.trivias.forEach(trivia => triviaList.push(trivia));
                        });
                    } else {
                        let filteredByCategory = this.trivias.filter(category => category.id == this.selectedCategory);
                        if (filteredByCategory.length == 0) {
                            return [];
                        }
                        triviaList = filteredByCategory[0].trivias;
                    }
                    // Show everything except the difficulties toggled off.
                    return triviaList.filter(trivia => this.activeDifficulties[trivia.difficulty]);
                },
                selectedOpenTriviasByCategoryAndDifficulty() {
                    let list;
                    if (this.selectedCategory == 0) {
                        list = this.openTrivias.slice();
                    } else {
                        list = this.openTrivias.filter(trivia => trivia.category_id == this.selectedCategory);
                    }
                    return list.filter(trivia => this.activeDifficulties[trivia.difficulty]);
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

    </script>
@endsection