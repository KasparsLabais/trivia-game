@php
    // Inherited from the parent view when included; fall back for safety.
    $joinUrlBase = $joinUrlBase ?? rtrim(config('settings.join_url') ?: url('/'), '/');
@endphp
<div class="h-full">
    <div v-if="selectedQuestionId != null && selectedView == 'question' " class="text-center flex flex-col justify-between h-full">
        <div class="py-2">
            <h1 class="text-4xl josefin-sans text-slate-200">[[ selectedQuestion.question ]]</h1>
        </div>
        <div class="flex flex-row px-2 py-2 md:px-6 md:py-2">
            <div class="w-2/12 text-slate-200 font-bold flex flex-col justify-center cursor-pointer hover:text-main-accent" @click="previousQuestion()">
                <div class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                    </svg>
                </div>
                <div>Previous</div>
            </div>
            <div class="answer-holder flex flex-row flex-wrap justify-between w-8/12">
                <div v-if="selectedQuestion.question_type == 'options'" class="w-full">
                    <div v-for="(answer, index) in selectedQuestion.answers" class="flex flex-col justify-center px-2 w-full">
                        <button class="py-3 px-4 shadow-md text-left text-slate-100 text-2xl font-semibold mb-2 w-full rounded bg-brand" :class="{'bg-green-600' : (showCorrectAnswerEnabled && answer.is_correct) }">
                            <span v-if="index == 0" class="text-white josefin-sans">A)</span>
                            <span v-if="index == 1" class="text-white josefin-sans">B)</span>
                            <span v-if="index == 2" class="text-white josefin-sans">C)</span>
                            <span v-if="index == 3" class="text-white josefin-sans">D)</span>
                            <span v-if="index == 4" class="text-white josefin-sans">E)</span>
                            <span v-if="index == 5" class="text-white josefin-sans">F)</span>
                            [[ answer.answer ]]
                        </button>
                    </div>
                </div>
                <div v-else-if="selectedQuestion.question_type == 'text_input'" class="w-full">
                    <div v-if="showCorrectAnswerEnabled" class="flex flex-row justify-center">
                        <div v-for="(answer, index) in selectedQuestion.answers">
                            <h1 class="text-main-accent josefin-sans text-4xl">[[ answer.answer ]]</h1>
                            <div v-if="answer.file_url != '' " class="flex flex-row justify-center">
                                <template v-if="answer.file_url_type == 'image'">
                                    <img v-bind:["src"]="answer.file_url" class="w-7/12">
                                </template>
                                <template v-else-if="answer.file_url_type == 'video'">
                                    <video v-bind:["src"]="answer.file_url" class="w-7/12" controls></video>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div v-else class="flex flex-row justify-center w-full">
                        <h1 class="text-slate-200 josefin-sans text-2xl">Answer hidden</h1>
                    </div>
                </div>
                <div v-else-if="selectedQuestion.question_type == 'open_input'" class="w-full">
                    <p class="text-slate-300 text-sm text-center mb-2">Submissions arrive live — click one to pick the winner.</p>
                    <div v-if="openSubmissions.length == 0" class="text-slate-300 text-xl text-center py-6">Waiting for submissions&hellip;</div>
                    <div v-else class="flex flex-col gap-2 max-h-72 overflow-y-auto px-1">
                        <div v-for="sub in openSubmissions" @click="selectWinner(sub.user_id)"
                             class="flex flex-row items-center gap-3 px-3 py-2 rounded cursor-pointer border-2"
                             :class="selectedWinnerUserId == sub.user_id ? 'bg-main-accent border-main-accent' : 'bg-main-dark border-transparent hover:bg-panel'">
                            <span class="text-main-accent font-semibold raleway whitespace-nowrap">[[ sub.username ]]</span>
                            <template v-if="sub.is_image">
                                <img :src="sub.value" class="h-16 rounded border border-panel">
                            </template>
                            <template v-else>
                                <span class="text-slate-100 text-xl break-words text-left">[[ sub.value ]]</span>
                            </template>
                            <svg v-if="selectedWinnerUserId == sub.user_id" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="ml-auto w-6 h-6 text-main-accent shrink-0">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-2/12 text-slate-200 font-bold flex flex-col justify-center cursor-pointer hover:text-main-accent" @click="nextQuestion()">
                <div class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
                <div>Next</div>
            </div>
        </div>


        <div class="flex flex-row justify-center px-2 py-1 bg-slate-500">
          <!-- open_input (judged): Show Question -> Reveal Answers -> Show Winner -->
          <template v-if="selectedQuestion.question_type == 'open_input'">
            <div class="px-2">
                <button @click="startQuestion()" class="py-2 px-2 shadow-md bg-brand text-left text-slate-100 text-lg font-semibold mb-2 rounded">
                    Show Question
                </button>
            </div>
            <div class="px-2">
                <button @click="revealAnswers()" class="py-2 px-2 shadow-md bg-brand text-left text-slate-100 text-lg font-semibold mb-2 rounded">
                    Reveal Answers
                </button>
            </div>
            <div class="px-2">
                <button @click="showWinningTeam()" class="py-2 px-2 shadow-md bg-main-accent text-left text-slate-100 text-lg font-semibold mb-2 rounded">
                    Show Winner
                </button>
            </div>
          </template>
          <template v-else>
            <!-- Reveal step: shown after "Show Question Only", to reveal the options -->
            <div v-if="questionOnlyActive" class="px-2">
                <button :disabled="timerActive" @click="revealAnswers()" class="py-2 px-2 shadow-md bg-brand text-left text-slate-100 text-lg font-semibold mb-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                    <span v-if="timerActive">Timer running&hellip; [[ timerTimeLeft ]]s</span>
                    <span v-else-if="settings.timeLimitEnabled == 1">Reveal Answers &amp; Start Timer</span>
                    <span v-else>Reveal Answers</span>
                </button>
            </div>
            <!-- Start step: question + answers in one go (timed or untimed) -->
            <template v-else>
                <div class="px-2">
                    <button v-if="settings.timeLimitEnabled == 1" :disabled="timerActive" @click="startTimer()" class="py-2 px-2 shadow-md bg-main-accent text-left text-slate-100 text-lg font-semibold mb-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        <span v-if="timerActive">Timer running&hellip; [[ timerTimeLeft ]]s</span>
                        <span v-else>Start Timer</span>
                    </button>
                    <button v-else class="py-2 px-2 shadow-md bg-brand text-left text-slate-100 text-lg font-semibold mb-2 rounded" @click="startQuestion()">
                        Start Question
                    </button>
                </div>
                <!-- button to show the question only; options revealed on confirmation -->
                <div class="px-2">
                    <button :disabled="timerActive" @click="startQuestionOnly()" class="py-2 px-2 shadow-md bg-panel text-left text-slate-100 text-lg font-semibold mb-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        Show Question Only
                    </button>
                </div>
            </template>
            <!-- button to show correct answer -->
            <div class="px-2">
                <button class="py-2 px-2 shadow-md bg-green-600 text-left text-slate-100 text-lg font-semibold mb-2 rounded" @click="showCorrectAnswer()">
                    Show Correct Answer
                </button>
            </div>
            <!-- button to show team that answered first and correctly -->
            <div class="px-2">
                <button class="py-2 px-2 shadow-md bg-main-accent text-left text-slate-100 text-lg font-semibold mb-2 rounded" @click="showWinningTeam()">
                    Show Winner
                </button>
            </div>
          </template>
        </div>
    </div>

    <div v-else-if="selectedView == 'winner'" class="flex flex-col justify-center bg-main-dark h-full" @click="showQuestionView()">
        <div>
            <div class="py-2">
                <h1 class="text-4xl josefin-sans text-slate-200 text-center">WINNER</h1>
            </div>
            <div class="px-2 py-2 md:px-6 md:py-2">
                <p class="text-main-accent josefin-sans text-center text-6xl">[[ openWinnerName ? openWinnerName : questionWinner.username ]]</p>
            </div>
        </div>
    </div>

    <div v-else class="text-center flex flex-col justify-center h-full">
        <!-- add some waiting animation here -->
        <h1 class="text-4xl josefin-sans text-main-light">Waiting for players!</h1>
        <p class="text-2xl josefin-sans text-alternative-accent">PIN</p>
        <h2 class="text-6xl josefin-sans text-alternative-accent">[[game.pin]]</h2>
        <div class="flex flex-col justify-center px-4 py-4">
            <div class="flex flex-row justify-center" id="qrcode"></div>
            <span class="flex flex-row justify-center my-4 text-slate-200 text-xl">OR</span>
            <h2 class="fira-sans flex flex-row justify-center text-slate-200 text-2xl"><span>{{ $joinUrlBase }}/join/{{ $gameInstance['token'] }}</span></h2>
        </div>
    </div>
</div>
