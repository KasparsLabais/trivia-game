<div class="h-full">
    <div v-if="selectedQuestionId != null && selectedView == 'question' " class="text-center flex flex-col justify-between h-full">
        <div class="py-2">
            <h1 class="text-4xl josefin-sans text-slate-200">[[ selectedQuestion.question ]]</h1>
        </div>
        <div class="px-2 py-2 md:px-6 md:py-2">
            <div class="answer-holder flex flex-wrap justify-between">
                <div v-for="(answer, index) in selectedQuestion.answers" class="flex flex-col justify-center px-2 w-full">
                    <button class="py-3 px-4 shadow-md text-left text-slate-100 text-2xl font-semibold mb-2 w-full rounded bg-lime-600" :class="{'bg-violet-600' : (showCorrectAnswerEnabled && answer.is_correct) }">
                        <span v-if="index == 0" class="text-zinc-700 josefin-sans">A)</span>
                        <span v-if="index == 1" class="text-zinc-700 josefin-sans">B)</span>
                        <span v-if="index == 2" class="text-zinc-700 josefin-sans">C)</span>
                        <span v-if="index == 3" class="text-zinc-700 josefin-sans">D)</span>
                        <span v-if="index == 4" class="text-zinc-700 josefin-sans">E)</span>
                        <span v-if="index == 5" class="text-zinc-700 josefin-sans">F)</span>
                        [[ answer.answer ]]
                    </button>
                </div>
            </div>
        </div>


        <div class="flex flex-row justify-center px-2 py-1 bg-slate-500">
            <!-- button to start timer -->
            <div class="px-2">
                <button v-if="settings.timeLimitEnabled" class="py-2 px-4 shadow-md bg-rose-600 text-left text-slate-100 text-xl font-semibold mb-2 rounded" @click="startTimer()">
                    Start Timer
                </button>
                <button v-else class="py-2 px-4 shadow-md bg-rose-600 text-left text-slate-100 text-xl font-semibold mb-2 rounded" @click="startQuestion()">
                    Start Question
                </button>
            </div>
            <!-- button to show correct answer -->
            <div class="px-2">
                <button class="py-2 px-4 shadow-md bg-cyan-600 text-left text-slate-100 text-xl font-semibold mb-2 rounded" @click="showCorrectAnswer()">
                    Show Correct Answer
                </button>
            </div>
            <!-- button to show team that answered first and correctly -->
            <div class="px-2">
                <button class="py-2 px-4 shadow-md bg-violet-600 text-left text-slate-100 text-xl font-semibold mb-2 rounded" @click="showWinningTeam()">
                    Show Winner
                </button>
            </div>
        </div>
    </div>

    <div v-else-if="selectedView == 'winner'" class="flex flex-col justify-center bg-zinc-900 h-full" @click="showQuestionView()">
        <div>
            <div class="py-2">
                <h1 class="text-4xl josefin-sans text-slate-200 text-center">WINNER</h1>
            </div>
            <div class="px-2 py-2 md:px-6 md:py-2">
                <p class="text-yellow-500 josefin-sans text-center text-6xl">[[ questionWinner.username ]]</p>
            </div>
        </div>
    </div>

    <div v-else class="text-center flex flex-col justify-center h-full">
        <!-- add some waiting animation here -->
        <h1 class="text-4xl josefin-sans text-slate-200 ">Waiting for players!</h1>
        <h2 class="text-6xl josefin-sans text-yellow-500">PIN: [[game.pin]]</h2>
        <div class="flex flex-col justify-center px-4 py-4">
            <div class="flex flex-row justify-center" id="qrcode"></div>
            <span class="flex flex-row justify-center my-4 text-slate-200 text-xl">OR</span>
            <h2 class="fira-sans flex flex-row justify-center text-slate-200 text-2xl"><span>https://quizcrave.com/join/{{ $gameInstance['token'] }}</span></h2>
        </div>
    </div>
</div>
