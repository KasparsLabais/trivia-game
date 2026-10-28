@extends('game-api::layout')
@section('page-bg', 'bg-pop')
@section('body')
    @php
        // Base URL for join links / QR codes. Defaults to the app's own URL;
        // override with GAME_JOIN_URL (config settings.join_url) for a short domain.
        $joinUrlBase = rtrim(config('settings.join_url') ?: url('/'), '/');
    @endphp

    @include('game-api::partials.game-notification')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        .dotted-canvas {
            background-color: #FFB900;
            background-image: radial-gradient(#14323B 2.5px, transparent 2.6px);
            background-size: 26px 26px;
        }
        .waiting-dot { animation: waiting-bounce 1.2s ease-in-out infinite; }
        .waiting-dot:nth-child(2) { animation-delay: 0.15s; }
        .waiting-dot:nth-child(3) { animation-delay: 0.3s; }
        @keyframes waiting-bounce {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }
        @media (prefers-reduced-motion: reduce) {
            .waiting-dot { animation: none; }
        }
    </style>

    <div id="player-app">


        {{-- Waiting room (joined, waiting for the game master to start) --}}
        <div v-if="currentView == 'game_created' " class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
            <div class="w-full max-w-2xl flex flex-col gap-8">

                {{-- Neo-brutalist "waiting room" window --}}
                <div class="w-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">

                    {{-- Title bar --}}
                    <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                        <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                        <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm">Waiting Room</span>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 md:p-10 flex flex-col items-center gap-6">

                        {{-- Game info --}}
                        <div class="text-center">
                            <h1 class="russo text-3xl md:text-4xl text-main-dark leading-tight break-words">[[ trivia.title ]]</h1>
                            <p class="fira-sans text-main-dark/60 mt-1">Hosted by <span class="font-bold text-brand">[[ gameInstance.user.username ]]</span></p>
                        </div>

                        {{-- Meta tags --}}
                        <div class="flex flex-wrap justify-center gap-3">
                            <span class="bg-pop text-main-dark border-4 border-main-dark px-4 py-1.5 russo text-xs md:text-sm uppercase tracking-wide">{{ $trivia->questions()->count() }} Questions</span>
                            <span v-if="trivia.category" class="bg-brand text-white border-4 border-main-dark px-4 py-1.5 russo text-xs md:text-sm uppercase tracking-wide">[[ trivia.category.name ]]</span>
                            <span v-if="trivia.difficulty" class="border-4 border-main-dark px-4 py-1.5 russo text-xs md:text-sm uppercase tracking-wide"
                                  :class="{'bg-green-500 text-white': trivia.difficulty == 'easy', 'bg-main-light text-main-dark': trivia.difficulty == 'medium', 'bg-main-accent text-white': trivia.difficulty == 'hard'}">[[ trivia.difficulty ]]</span>
                        </div>

                        <p v-if="trivia.description" class="fira-sans text-center text-main-dark/70">[[ trivia.description ]]</p>

                        {{-- Status --}}
                        <div class="w-full bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-5 py-6 flex flex-col items-center gap-3 text-center">
                            <div class="flex items-center gap-2" aria-hidden="true">
                                <span class="waiting-dot w-4 h-4 bg-main-accent border-2 border-main-dark"></span>
                                <span class="waiting-dot w-4 h-4 bg-pop border-2 border-main-dark"></span>
                                <span class="waiting-dot w-4 h-4 bg-brand border-2 border-main-dark"></span>
                            </div>
                            <p class="fira-sans text-main-dark text-xl md:text-2xl font-bold">Waiting for <span class="text-main-accent">[[ gameInstance.user.username ]]</span> to start the game!</p>
                            <p class="fira-sans text-main-dark/60 text-sm">Keep this page open &mdash; the first question will appear here automatically.</p>
                        </div>

                        {{-- Who you're playing as --}}
                        <div v-if="player && player.user" class="flex flex-row items-stretch border-4 border-main-dark">
                            <span class="flex items-center bg-main-dark text-white px-3 russo text-xs uppercase tracking-widest">Playing as</span>
                            <span class="bg-white text-main-dark px-4 py-2 fira-sans font-bold text-lg break-all">[[ player.user.username ]]</span>
                        </div>
                    </div>
                </div>

                {{-- Invite window: PIN, link & QR code --}}
                <div class="w-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                    <div class="flex items-center gap-2.5 bg-brand border-b-4 border-main-dark px-5 py-3">
                        <span class="russo uppercase tracking-widest text-white text-xs md:text-sm">Invite Friends</span>
                    </div>

                    <div class="p-6 md:p-10 flex flex-col items-center gap-6">
                        {{-- PIN --}}
                        <div class="flex flex-col items-center gap-2">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Game PIN</span>
                            <span class="russo text-5xl md:text-6xl tracking-[0.2em] text-main-dark bg-pop border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-6 py-2">[[ gameInstance.pin ]]</span>
                        </div>

                        {{-- Link --}}
                        <div class="w-full flex flex-col gap-2">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60 text-center">Join link</span>
                            <div class="flex flex-row items-stretch w-full border-4 border-main-dark">
                                <span class="flex-1 min-w-0 bg-main-light px-3 py-2.5 fira-sans text-sm md:text-base font-bold text-main-dark break-all">{{ $joinUrlBase }}/join/[[ gameInstance.token ]]</span>
                                <button type="button" @click="copyJoinLink()"
                                        class="shrink-0 bg-main-accent text-white border-l-4 border-main-dark px-4 russo text-xs uppercase tracking-wide transition-colors hover:bg-secondly-accent">[[ linkCopied ? 'Copied!' : 'Copy' ]]</button>
                            </div>
                        </div>

                        {{-- OR divider --}}
                        <div class="flex items-center gap-3 w-full">
                            <div class="flex-1 h-1 bg-main-dark"></div>
                            <span class="shrink-0 russo text-sm bg-main-dark text-white rounded-full w-11 h-11 flex items-center justify-center">OR</span>
                            <div class="flex-1 h-1 bg-main-dark"></div>
                        </div>

                        {{-- QR code --}}
                        <div class="flex flex-col items-center gap-2">
                            <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-3">
                                <div id="qrcode"></div>
                            </div>
                            <span class="fira-sans text-sm text-main-dark/60">Scan to join</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Question (answering, waiting for options, revealed answers) --}}
        <div v-else-if="currentView == 'question_view'" class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
            <div class="w-full max-w-2xl bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">

                {{-- Title bar: traffic lights + points --}}
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm truncate">[[ trivia.title ]]</span>
                    <div class="ml-auto flex items-baseline gap-2 shrink-0">
                        <span class="fira-sans text-xs uppercase tracking-widest text-white/60">Points</span>
                        <span id="game-api-_points-holder" class="russo text-pop text-xl md:text-2xl">[[ player.points ]]</span>
                    </div>
                </div>

                {{-- Timer bar --}}
                <div id="timer-holder" :class="{'hidden': (timerMode == 0) }" class="w-full bg-main-light border-b-4 border-main-dark">
                    <div class="flex flex-row items-center px-5 py-3 gap-4">
                        <div class="flex-1 h-4 bg-white border-2 border-main-dark overflow-hidden">
                            <div id="timer-settings" class="h-full" :class="[timerClass]" :style="{ width: timerSize, transition: 'width 1s linear' }"></div>
                        </div>
                        <span id="timer-countdown-holder" class="russo text-xl text-main-dark w-12 text-right">[[ timeLeft ]]</span>
                    </div>
                </div>

                <div class="p-6 md:p-10 flex flex-col gap-6">

                    {{-- Question text --}}
                    <h1 v-if="questionLoaded == 0" id="question-holder" class="russo text-main-dark/40 text-3xl md:text-4xl text-center leading-tight">Waiting for question&hellip;</h1>
                    <h1 v-else id="question-holder" class="russo text-main-dark text-2xl md:text-4xl text-center leading-tight break-words">[[ question.question ]]</h1>

                    {{-- No question yet --}}
                    <div v-if="questionLoaded == 0" class="bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-5 py-6 flex flex-col items-center gap-3 text-center">
                        <div class="flex items-center gap-2" aria-hidden="true">
                            <span class="waiting-dot w-4 h-4 bg-main-accent border-2 border-main-dark"></span>
                            <span class="waiting-dot w-4 h-4 bg-pop border-2 border-main-dark"></span>
                            <span class="waiting-dot w-4 h-4 bg-brand border-2 border-main-dark"></span>
                        </div>
                        <p class="fira-sans text-main-dark text-lg md:text-xl font-bold">Hang tight &mdash; your question is on its way!</p>
                    </div>

                    {{-- Text input question --}}
                    <div v-else-if="questionLoaded == 1 && question.question_type == 'text_input' " class="flex flex-col gap-4">
                        <div v-if="(question.id == lastAnsweredQuestionId) || (correctAnswer != '' )" class="flex flex-col gap-4">
                            <div class="flex flex-row items-stretch border-4 border-main-dark">
                                <span class="flex items-center bg-main-dark text-white px-3 russo text-xs uppercase tracking-widest shrink-0">Your answer</span>
                                <span class="flex-1 min-w-0 bg-white text-main-dark px-4 py-3 fira-sans font-bold text-xl break-words">[[ correctInputTextAnswer || '—' ]]</span>
                            </div>
                            <div v-if="correctAnswer != ''" class="flex flex-row items-stretch border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]">
                                <span class="flex items-center bg-green-500 text-white px-3 russo text-xs uppercase tracking-widest shrink-0 border-r-4 border-main-dark">Correct</span>
                                <span class="flex-1 min-w-0 bg-white text-main-dark px-4 py-3 fira-sans font-bold text-xl break-words">[[ correctAnswer ]]</span>
                            </div>
                            <p v-else class="fira-sans text-center text-main-dark/60">Answer locked in! Waiting for the host to reveal the correct answer&hellip;</p>
                            <div v-if="correctAnswerFileUrl" class="flex flex-row justify-center">
                                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-2 w-full md:w-2/3">
                                    <template v-if="correctAnswerFileUrlType == 'image'">
                                        <img class="w-full" :src="correctAnswerFileUrl" alt="Correct Answer Image">
                                    </template>
                                    <template v-else-if="correctAnswerFileUrlType == 'video'">
                                        <video class="w-full" controls>
                                            <source :src="correctAnswerFileUrl" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="answersRevealed" class="flex flex-col gap-4">
                            <input v-model="correctInputTextAnswer" @keyup.enter="answerInputText()" class="w-full bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 px-4 text-center text-2xl text-main-dark font-bold placeholder:text-base placeholder:font-medium placeholder:text-gray-400 focus:outline-none fira-sans" type="text" id="correct_answer" name="correct_answer" placeholder="Type your answer">
                            <button @click="answerInputText()" type="button" class="w-full bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-2xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Submit Answer</button>
                        </div>
                        <div v-else class="bg-main-light border-4 border-main-dark px-5 py-6 text-center">
                            <h2 class="russo text-main-dark text-2xl">Get ready&hellip;</h2>
                            <p class="fira-sans text-main-dark/60 mt-1">You'll be able to answer in a moment!</p>
                        </div>
                    </div>

                    {{-- Open input question (judged by the host) --}}
                    <div v-else-if="questionLoaded == 1 && question.question_type == 'open_input'" class="flex flex-col gap-4">
                        {{-- After the host reveals: show everyone's submissions --}}
                        <div v-if="openSubmissionsRevealed" class="flex flex-col gap-3">
                            <h2 class="russo text-main-dark text-xl uppercase tracking-wide text-center">Submitted Answers</h2>
                            <div v-for="sub in openSubmissions" class="bg-main-light border-4 border-main-dark px-4 py-3 flex flex-row items-center gap-3">
                                <span class="fira-sans text-brand font-bold whitespace-nowrap">[[ sub.username ]]</span>
                                <template v-if="sub.is_image">
                                    <img :src="sub.value" class="h-20 border-2 border-main-dark">
                                </template>
                                <template v-else>
                                    <span class="fira-sans text-main-dark text-lg break-words min-w-0">[[ sub.value ]]</span>
                                </template>
                            </div>
                            <p v-if="openSubmissions.length == 0" class="fira-sans text-main-dark/60 text-center py-4">No answers were submitted.</p>
                        </div>
                        {{-- Before submit: input field for the required format --}}
                        <div v-else-if="!openSubmitted" class="flex flex-col gap-4">
                            <template v-if="question.input_type == 'image'">
                                <label class="w-full bg-main-light border-4 border-dashed border-main-dark px-4 py-6 flex flex-col items-center gap-2 cursor-pointer text-center">
                                    <span class="russo text-main-dark uppercase tracking-wide">[[ openImageFile ? 'Change image' : 'Choose an image' ]]</span>
                                    <span v-if="openImageFile" class="fira-sans text-sm text-main-dark/60 break-all">[[ openImageFile.name ]]</span>
                                    <input type="file" accept="image/*" @change="onOpenImageSelected($event)" class="sr-only">
                                </label>
                            </template>
                            <template v-else>
                                <input v-model="openInputValue" @keyup.enter="submitOpenInput()" :type="question.input_type == 'number' ? 'number' : 'text'"
                                       class="w-full bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 px-4 text-center text-2xl text-main-dark font-bold placeholder:text-base placeholder:font-medium placeholder:text-gray-400 focus:outline-none fira-sans"
                                       :placeholder="question.input_type == 'number' ? 'Enter a number' : 'Your answer'">
                            </template>
                            <button @click="submitOpenInput()" type="button" class="w-full bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-2xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Submit Answer</button>
                        </div>
                        {{-- After submit: waiting for the host to judge --}}
                        <div v-else class="bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-5 py-6 text-center">
                            <h2 class="russo text-green-600 text-2xl">Answer submitted!</h2>
                            <p class="fira-sans text-main-dark/60 mt-1">Waiting for the host&hellip;</p>
                        </div>
                    </div>

                    {{-- Multiple choice --}}
                    <div v-else class="flex flex-col gap-4 answer-holder">
                        <div v-if="!answersRevealed" class="bg-main-light border-4 border-main-dark px-5 py-6 text-center">
                            <h2 class="russo text-main-dark text-2xl">Get ready&hellip;</h2>
                            <p class="fira-sans text-main-dark/60 mt-1">The answer options are coming up!</p>
                        </div>
                        <button v-else v-for="(answer, index) in answers" @click="answerQuestion(answer.id, index)" v-bind:["answer-id"]="answer.id" type="button"
                                class="w-full flex flex-row items-stretch text-left border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] transition-all duration-100"
                                :class="answerClass(answer)">
                            <span class="flex items-center justify-center w-14 shrink-0 russo text-2xl border-r-4 border-main-dark" :class="answerLetterClass(answer)">[[ 'ABCDEF'.charAt(index) ]]</span>
                            <span class="flex-1 min-w-0 px-4 py-4 fira-sans text-xl md:text-2xl font-bold break-words">[[ answer.answer ]]</span>
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- Question winner --}}
        <div v-else-if="currentView == 'winners_view'" class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
            <div class="w-full max-w-2xl bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm">Question Winner</span>
                </div>
                <div class="p-6 md:p-10 flex flex-col items-center gap-6 text-center">
                    <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">And the point goes to&hellip;</span>
                    <p class="russo text-main-dark text-4xl md:text-6xl bg-pop border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-6 py-4 break-words max-w-full">[[ questionWinner ]]</p>
                    <p class="fira-sans text-main-dark/60">Next question coming up&hellip;</p>
                </div>
            </div>
        </div>

        {{-- Time's up --}}
        <div v-else-if="currentView == 'times_up'" class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
            <div class="w-full max-w-2xl bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                <div class="flex items-center gap-2.5 bg-main-accent border-b-4 border-main-dark px-5 py-3">
                    <span class="russo uppercase tracking-widest text-white text-xs md:text-sm">Time's Up</span>
                </div>
                <div class="p-6 md:p-10 flex flex-col items-center gap-6 text-center">
                    <h1 class="russo text-main-accent text-5xl md:text-6xl">TIME'S UP!</h1>
                    <p v-if="question" class="fira-sans text-main-dark text-xl md:text-2xl font-bold break-words">[[ question.question ]]</p>
                    <div class="w-full bg-main-light border-4 border-main-dark px-5 py-5 flex flex-col items-center gap-3">
                        <div class="flex items-center gap-2" aria-hidden="true">
                            <span class="waiting-dot w-4 h-4 bg-main-accent border-2 border-main-dark"></span>
                            <span class="waiting-dot w-4 h-4 bg-pop border-2 border-main-dark"></span>
                            <span class="waiting-dot w-4 h-4 bg-brand border-2 border-main-dark"></span>
                        </div>
                        <p class="fira-sans text-main-dark/70">Waiting for the correct answer&hellip;</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Host is revealing the final standings on the Display Screen --}}
        <div v-else-if="currentView == 'results_on_display'" class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
            <div class="w-full max-w-2xl bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm">Final Results</span>
                </div>
                <div class="p-6 md:p-10 flex flex-col items-center gap-6 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-16 text-main-dark">
                        <path d="M3 5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-5v2h2a1 1 0 1 1 0 2H8a1 1 0 1 1 0-2h2v-2H5a2 2 0 0 1-2-2V5Z"/>
                    </svg>
                    <h1 class="russo text-main-dark text-4xl md:text-5xl leading-tight">Eyes on the big screen!</h1>
                    <div class="w-full bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-5 py-5 flex flex-col items-center gap-3">
                        <div class="flex items-center gap-2" aria-hidden="true">
                            <span class="waiting-dot w-4 h-4 bg-main-accent border-2 border-main-dark"></span>
                            <span class="waiting-dot w-4 h-4 bg-pop border-2 border-main-dark"></span>
                            <span class="waiting-dot w-4 h-4 bg-brand border-2 border-main-dark"></span>
                        </div>
                        <p class="fira-sans text-main-dark font-bold text-lg">The winners are being revealed.</p>
                        <p class="fira-sans text-main-dark/60 text-sm">Your results will appear here once [[ gameInstance.user.username ]] wraps up the game.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Game over (host closed the game) --}}
        <div v-else-if="currentView == 'game_over'" class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
            <div class="w-full max-w-2xl bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm">Game Over</span>
                </div>
                <div class="p-6 md:p-10 flex flex-col items-center gap-6">
                    <h1 class="russo text-main-accent text-5xl md:text-6xl text-center">GAME OVER</h1>
                    <div v-if="gameOver" class="w-full max-w-md border-4 border-main-dark">
                        <div class="flex flex-row justify-between gap-4 px-4 py-2.5 border-b-4 border-main-dark bg-main-light">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60 self-center">Game</span><span class="fira-sans font-bold text-main-dark text-right break-words min-w-0">[[ gameOver.name ]]</span>
                        </div>
                        <div class="flex flex-row justify-between gap-4 px-4 py-2.5 border-b-4 border-main-dark">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60 self-center">Questions</span><span class="fira-sans font-bold text-main-dark">[[ gameOver.questionCount ]]</span>
                        </div>
                        <div class="flex flex-row justify-between gap-4 px-4 py-2.5 border-b-4 border-main-dark bg-main-light">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60 self-center">Duration</span><span class="fira-sans font-bold text-main-dark">[[ gameOver.duration ]]</span>
                        </div>
                        <div class="flex flex-row justify-between gap-4 px-4 py-2.5">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60 self-center">Players</span><span class="fira-sans font-bold text-main-dark">[[ gameOver.playerCount ]]</span>
                        </div>
                    </div>
                    <a href="/" class="w-full max-w-sm text-center bg-brand text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-2xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Back Home</a>
                </div>
            </div>
        </div>

    </div>




    <script src="/vendor/trivia-game/js/qrcode.min.js"></script>
    <script>
        var qrcode = new QRCode("qrcode", {
            text: "{{ $joinUrlBase }}/join/{{ $gameInstance['token'] }}",
            width: 168,
            height: 168,
            colorDark : "#14323B",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        GameApi.joinRoom('{{ $gameInstance['token'] }}');


        //TODO: Convert callbackGameInstanceUpdated to event listener

        document.addEventListener('playerJoined', (e) => {
            playerJoinedUserView(game);
        });


    </script>

    <script>

        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    gameToken: '{{ $gameInstance['token'] }}',
                    correctInputTextAnswer: '',
                    correctAnswer: '',
                    correctAnswerFileUrl: '',
                    correctAnswerFileUrlType: 'image',
                    currentView: @if($gameInstance['status'] == 'created') 'game_created' @else 'question_view' @endif,
                    gameInstance: @json($gameInstance),
                    trivia: @json($trivia),
                    player: @json($player),
                    questionLoaded: 0,
                    question: null,
                    answers: [],
                    // When the game master shows a question "question only", the
                    // options stay hidden until they reveal them. Defaults to true
                    // so the normal "question + answers" flow is unchanged.
                    answersRevealed: true,
                    // open_input (judged free-form submission) state.
                    openInputValue: '',
                    openImageFile: null,
                    openSubmitted: false,
                    openSubmissions: [],
                    openSubmissionsRevealed: false,
                    lastAnsweredQuestionId: null,
                    lastAnsweredAnswerId: null,
                    answerSelected: null,
                    questionWinner: null,
                    timeLimitTimer: null,
                    startTimer: 0,
                    timeLeft: 0,
                    timerClass: 'bg-green-500',
                    timerSize: '100%',
                    timerMode: 0,
                    gameOver: null,
                    linkCopied: false,
                }
            },
            delimiters: ['[[', ']]'],
            methods: {
                changeView(view) {
                    this.currentView = view;
                },
                // Multiple-choice button colours: picked (teal) until the host
                // reveals, then correct (green) / your wrong pick (red).
                answerClass(answer) {
                    let picked = this.lastAnsweredAnswerId == answer.id;
                    let revealed = this.answers.some(a => a.is_correct);
                    if (answer.is_correct) { return 'bg-green-500 text-white'; }
                    if (picked) { return revealed ? 'bg-main-accent text-white' : 'bg-brand text-white'; }
                    if (revealed || this.question.id == this.lastAnsweredQuestionId) { return 'bg-white text-main-dark opacity-50'; }
                    return 'bg-white text-main-dark hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]';
                },
                answerLetterClass(answer) {
                    let picked = this.lastAnsweredAnswerId == answer.id;
                    return (answer.is_correct || picked) ? 'bg-main-dark text-white' : 'bg-pop text-main-dark';
                },
                copyJoinLink() {
                    let link = '{{ $joinUrlBase }}/join/' + this.gameInstance.token;
                    // navigator.clipboard only exists on secure (https/localhost) origins.
                    if (!navigator.clipboard) {
                        GameApi.displayAlertNotification('error', 'Could not copy the link.');
                        return;
                    }
                    navigator.clipboard.writeText(link).then(() => {
                        this.linkCopied = true;
                        setTimeout(() => { this.linkCopied = false; }, 2000);
                    }).catch(() => {
                        GameApi.displayAlertNotification('error', 'Could not copy the link.');
                    });
                },
                answerQuestion(answerId, index) {

                    if (this.question.id == this.lastAnsweredQuestionId) {
                        return;
                    }

                    // Give instant feedback and stop the timer up front, so the UI
                    // reflects the selection even if the request/response fails.
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.lastAnsweredQuestionId = this.question.id;
                    this.lastAnsweredAnswerId = answerId;
                    this.setCookie('last_answered_question_id', this.question.id, 1);
                    this.setCookie('last_answered_answer_id', answerId, 1);

                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_id': answerId, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.data && data.data.playerInstance) {
                                    GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                }
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'question_type' : this.question.question_type, 'indexId' : index, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            } else {
                                GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Could not submit your answer.');
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            GameApi.displayAlertNotification('error', 'Could not submit your answer.');
                        });
                },
                answerInputText(answerId) {
                    if (this.question.id == this.lastAnsweredQuestionId) {
                        return;
                    }

                    // Give instant feedback and stop the timer up front.
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.lastAnsweredQuestionId = this.question.id;
                    this.setCookie('last_answered_question_id', this.question.id, 1);

                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {'method' : 'POST', 'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, 'body': JSON.stringify({'answer_text': this.correctInputTextAnswer, 'question_id': this.question.id})})
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                if (data.data && data.data.playerInstance) {
                                    GameApi.updatePlayerInstance('{{ $gameInstance['token'] }}', data.data.playerInstance);
                                }
                                GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', {'data' :  {'question_type' : this.question.question_type ,'answer_text' : this.correctInputTextAnswer, 'questionId': this.question.id, 'id': window.id,'username' : window.username, 'answerid' : answerId }, 'action': 'playerAnsweredEvent'});
                            } else {
                                GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Could not submit your answer.');
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            GameApi.displayAlertNotification('error', 'Could not submit your answer.');
                        });
                },
                onOpenImageSelected(e) {
                    this.openImageFile = (e.target.files && e.target.files[0]) ? e.target.files[0] : null;
                },
                // Submit an open_input (judged) answer: string/number via JSON, or
                // an image via multipart to the temporary-image endpoint.
                submitOpenInput() {
                    if (this.openSubmitted) { return; }

                    if (this.question.input_type == 'image') {
                        if (!this.openImageFile) { GameApi.displayAlertNotification('error', 'Please choose an image first.'); return; }
                        let fd = new FormData();
                        fd.append('image', this.openImageFile);
                        fd.append('question_id', this.question.id);
                        this.openSubmitted = true;
                        fetch('/trv/trivia/{{ $gameInstance['token'] }}/submit-image', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: fd
                        }).then(r => r.json()).then(data => {
                            if (data.success) {
                                this.notifyMasterOpen(true, data.data.url);
                            } else {
                                this.openSubmitted = false;
                                GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Upload failed.');
                            }
                        }).catch(() => { this.openSubmitted = false; GameApi.displayAlertNotification('error', 'Upload failed.'); });
                        return;
                    }

                    let val = ('' + this.openInputValue).trim();
                    if (val === '') { return; }
                    this.openSubmitted = true;
                    fetch('/trv/trivia/{{ $gameInstance['token'] }}/answer', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ answer_text: val, question_id: this.question.id })
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                            this.notifyMasterOpen(false, val);
                        } else {
                            this.openSubmitted = false;
                            GameApi.displayAlertNotification('error', (data && data.message) ? data.message : 'Could not submit your answer.');
                        }
                    }).catch(() => { this.openSubmitted = false; GameApi.displayAlertNotification('error', 'Could not submit your answer.'); });
                },
                notifyMasterOpen(isImage, value) {
                    GameApi.notifyGameMaster('{{ $gameInstance['token'] }}', { 'data': {
                        'question_type': 'open_input',
                        'input_type': this.question.input_type,
                        'is_image': isImage,
                        'value': value,
                        'questionId': this.question.id,
                        'id': window.id,
                        'username': window.username
                    }, 'action': 'playerAnsweredEvent' });
                },
                triggerTimer(initialTime, currentTime) {
                    this.startTimer = initialTime;
                    // currentTime lets a reconnecting player resume from the
                    // remaining time instead of restarting from the beginning.
                    this.timeLeft = (typeof currentTime === 'undefined') ? initialTime : currentTime;

                    this.timerClass = 'bg-green-500';
                    this.timerSize = '100%';

                    this.updateTimer();
                },
                updateTimer() {
                    let timeLeftPercentage = (this.timeLeft / this.startTimer) * 100;
                    this.timerSize = timeLeftPercentage + '%';

                    if (timeLeftPercentage < 75) {
                        this.timerClass = 'bg-pop';
                    }

                    if (timeLeftPercentage < 55) {
                        this.timerClass = 'bg-pop';
                    }

                    if (timeLeftPercentage < 30) {
                        this.timerClass = 'bg-secondly-accent';
                    }

                    if (timeLeftPercentage < 20) {
                        this.timerClass = 'bg-main-accent';
                    }

                    if (timeLeftPercentage < 10) {
                        this.timerClass = 'bg-main-accent';
                    }

                    if (timeLeftPercentage < 5) {
                        this.timerClass = 'bg-main-accent';
                    }

                    if (this.timeLeft <= 0) {
                        this.changeView('times_up');
                        clearInterval(this.timeLimitTimer);
                        return;
                    }

                    this.timeLimitTimer = setTimeout(() => {
                        this.timeLeft = this.timeLeft - 1;
                        this.updateTimer();
                    }, 1000);
                },
                // Persisted in localStorage (not cookies) so it is never sent in
                // request headers — large cookies caused nginx "400 Request Header
                // Or Cookie Too Large" on reload.
                setCookie(name, value, days) {
                    name = name + '_' + this.gameToken;
                    try { localStorage.setItem(name, value); } catch (e) { console.log(e); }
                },
                getCookie(name) {
                    name = name + '_' + this.gameToken;
                    try { return localStorage.getItem(name); } catch (e) { return null; }
                },
                eraseCookie(name) {
                    name = name + '_' + this.gameToken;
                    try { localStorage.removeItem(name); } catch (e) {}
                },
                checkCookie(name) {
                    var value = this.getCookie(name);
                    return value !== null && value !== '';
                },
                // One-time cleanup of legacy cookies (from before the localStorage
                // switch) across all games, so they stop bloating request headers.
                clearLegacyCookies() {
                    var prefixes = ['answeredQuestions', 'startedQuestions', 'master_active_question', 'active_question', 'active_question_answers', 'last_answered_answer_id', 'last_answered_question_id'];
                    document.cookie.split(';').forEach(function (c) {
                        var cookieName = c.split('=')[0].trim();
                        if (prefixes.some(function (p) { return cookieName.indexOf(p) === 0; })) {
                            document.cookie = cookieName + '=; Max-Age=-99999999; path=/';
                        }
                    });
                },
                preLoadInfoFromCookies() {
                    if (this.checkCookie('last_answered_question_id')) {
                        this.lastAnsweredQuestionId = this.getCookie('last_answered_question_id');
                    }

                    if (this.checkCookie('last_answered_answer_id')) {
                        this.lastAnsweredAnswerId = this.getCookie('last_answered_answer_id');
                    }

                    if (this.checkCookie('active_question')) {
                        this.question = JSON.parse(this.getCookie('active_question'));

                        this.questionLoaded = 1;
                        this.changeView('question_view');

                        console.log('Active Question: ' + this.question);
                    }

                    if (this.checkCookie('active_question_answers')) {
                        this.answers = JSON.parse(this.getCookie('active_question_answers'));
                        console.log('Active Question Answers: ' + this.answers);
                    }

                    // Reloaded while the host is revealing results on the big screen.
                    if (this.checkCookie('results_on_display')) {
                        this.changeView('results_on_display');
                    }

                    // Restore whether the options were revealed, so a reload during
                    // the "question only" phase keeps the options hidden.
                    if (this.checkCookie('active_question_revealed')) {
                        this.answersRevealed = this.getCookie('active_question_revealed') !== '0';
                    }
                },
            },
            mounted() {

                this.clearLegacyCookies();
                this.preLoadInfoFromCookies();

                // On (re)load, ask game-node whether a question timer is running and
                // resume it from the exact remaining time (or show expired).
                GameApi.getQuestionTimer(this.gameToken, (timer) => {
                    if (!timer || !timer.active) {
                        return;
                    }
                    // A running timer means the options are live, so restore the
                    // question AND its answers from the timer's (authoritative)
                    // data. The localStorage copy can still be the empty
                    // "question only" set from before the answers were revealed,
                    // which would otherwise leave the player with no options.
                    this.question = {
                        'id': timer.questionData.id,
                        'question': timer.questionData.question,
                        'question_type': timer.questionData.question_type,
                    };
                    this.answers = timer.questionData.answers || [];
                    this.questionLoaded = 1;
                    this.answersRevealed = true;

                    this.changeView('question_view');
                    this.timerMode = 1;

                    if (timer.expired) {
                        clearInterval(this.timeLimitTimer);
                        this.timeLeft = 0;
                        this.changeView('times_up');
                    } else {
                        this.triggerTimer(timer.timeLimit, timer.timeLeft);
                    }
                });

                document.addEventListener('gameStarted', (e) => {
                    console.log(e);
                    this.gameInstance.status = 'started';
                    this.changeView('question_view');
                    //window.location.href = '/trv/trivia/' + e.detail.gameToken;
                });

                document.addEventListener('startQuestion', (e) => {
                    console.log(e);

                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');
                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');
                    this.eraseCookie('results_on_display');

                    this.correctAnswer = '';
                    this.correctAnswerFileUrl = '';
                    this.correctAnswerFileUrlType = 'image';
                    this.timerMode = 0;
                    this.questionWinner = '';
                    this.questionLoaded = 1;
                    this.lastAnsweredAnswerId = null;
                    // answersHidden => the game master is showing the question
                    // only; keep the options hidden until they reveal them.
                    this.answersRevealed = e.detail.answersHidden ? false : true;

                    this.changeView('question_view');

                    // Reset open_input (judged) state for the new question.
                    this.openSubmitted = false;
                    this.openSubmissionsRevealed = false;
                    this.openInputValue = '';
                    this.openImageFile = null;
                    this.openSubmissions = [];

                    this.question = {
                        'id' : e.detail.id,
                        'question' : e.detail.question,
                        'question_type' : e.detail.question_type,
                        'input_type' : e.detail.input_type,
                    }
                    console.log(e.detail);
                    this.answers = e.detail.answers;


                    let activeQuestionJson = JSON.stringify(this.question);
                    let activeQuestionAnswersJson = JSON.stringify(this.answers);

                    this.setCookie('active_question', activeQuestionJson, 1);
                    this.setCookie('active_question_answers',  activeQuestionAnswersJson, 1);
                    this.setCookie('active_question_revealed', this.answersRevealed ? '1' : '0', 1);

                });

                // Game master reveals the answer options for a question that was
                // shown "question only". Fill in the options and let players answer.
                document.addEventListener('revealAnswers', (e) => {
                    console.log('revealAnswers', e.detail);
                    this.questionLoaded = 1;
                    this.changeView('question_view');
                    if (e.detail && e.detail.answers) {
                        this.answers = e.detail.answers;
                    }
                    this.answersRevealed = true;
                    this.setCookie('active_question_answers', JSON.stringify(this.answers), 1);
                    this.setCookie('active_question_revealed', '1', 1);
                });

                // open_input: the host revealed everyone's submissions.
                document.addEventListener('revealSubmissions', (e) => {
                    console.log('revealSubmissions', e.detail);
                    this.openSubmissions = (e.detail && e.detail.submissions) ? e.detail.submissions : [];
                    this.openSubmissionsRevealed = true;
                });

                document.addEventListener('startTimer', (e) => {
                    console.log(e);
                    clearInterval(this.timeLimitTimer);

                    this.timerMode = 1;
                    this.questionWinner = '';
                    this.questionLoaded = 1;
                    this.lastAnsweredAnswerId = null;
                    // A running timer always means the options are live.
                    this.answersRevealed = true;

                    this.changeView('question_view');

                    this.question = {
                        'id' : e.detail.questionData.id,
                        'question' : e.detail.questionData.question,
                        'question_type' : e.detail.questionData.question_type,
                    }

                    this.answers = e.detail.questionData.answers;
                    this.triggerTimer(e.detail.timeLimit);
                });

                document.addEventListener("showCorrectAnswer", (e) => {


                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');

                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');
                    this.eraseCookie('active_question_revealed');

                    console.log(e);
                    if (e.detail.question_type == 'text_input') {
                        this.correctAnswer = e.detail.answer_text;
                        this.correctAnswerFileUrl = e.detail.file_url;
                        this.correctAnswerFileUrlType = e.detail.file_url_type;
                    } else {
                        this.answers.forEach(answer => {
                            if (answer.id == e.detail['answer_id']) {
                                answer.is_correct = 1;
                            }
                        });
                    }
                });
                document.addEventListener('showWinningTeam', (e) => {

                    this.eraseCookie('last_answered_question_id');
                    this.eraseCookie('last_answered_answer_id');

                    this.eraseCookie('active_question');
                    this.eraseCookie('active_question_answers');
                    this.eraseCookie('active_question_revealed');

                    console.log(e);
                    this.changeView('winners_view');
                    this.questionWinner = e.detail.winnerName;
                });

                document.addEventListener('updatePoints', (e) => {
                    GameApi.getPlayerPoints('{{ $gameInstance['token'] }}');
                });

                document.addEventListener('updatePlayerPoints', (e) => {
                    console.log(e);
                    this.player.points = e.detail.points;
                });

                // Host is revealing the final standings on the Display Screen.
                document.addEventListener('revealResultsOnDisplay', (e) => {
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.timerMode = 0;
                    this.setCookie('results_on_display', '1', 1);
                    this.changeView('results_on_display');
                });

                document.addEventListener('gameOverEvent', (e) => {
                    console.log('gameOverEvent', e.detail);
                    // Already watched the reveal on the big screen -> skip the
                    // suspense animation on the phone.
                    let instant = this.checkCookie('results_on_display') ? '?instant=1' : '';
                    this.eraseCookie('results_on_display');
                    window.location.href = '/trivia/{{ $gameInstance['token'] }}/results' + instant;
                });

                document.addEventListener('gameClosed', (e) => {
                    console.log('gameClosed', e.detail);
                    var detail = e.detail || {};

                    // Stop the timer and show the Game Over screen with game details.
                    clearTimeout(this.timeLimitTimer);
                    clearInterval(this.timeLimitTimer);
                    this.timerMode = 0;
                    this.gameOver = {
                        name: detail.name,
                        questionCount: detail.questionCount,
                        playerCount: detail.playerCount,
                        duration: detail.duration
                    };
                    this.changeView('game_over');

                    // Disappearing notification explaining why the game ended.
                    document.dispatchEvent(new CustomEvent('gameMasterNotification', { detail: { from: 'Game', message: detail.message || 'The host has ended the game.', colour: '#dc2626' } }));
                });

                document.addEventListener('userconnected', (e) => {
                    console.log('event userconnected', e.detail);
                    GameApi.joinRoom('{{ $gameInstance['token'] }}');
                });
            }
        }).mount('#player-app');

    </script>


@endsection