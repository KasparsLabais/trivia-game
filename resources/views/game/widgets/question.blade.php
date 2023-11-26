<div class="h-full">
    <div v-if="selectedQuestionId != null" class="text-center flex flex-col justify-center h-full">
        <div>
            <h1 class="text-4xl josefin-sans text-slate-200">[[ selectedQuestion.question ]]</h1>
        </div>
        <div class="px-2 py-2 md:px-6 md:py-8">
            <div class="answer-holder flex flex-wrap justify-between">
                <div v-for="(answer, index) in selectedQuestion.answers" class="flex flex-col justify-center px-2 w-full">
                    <button class="py-4 px-4 shadow-md bg-lime-600 text-left text-slate-100 text-3xl font-semibold mb-2 w-full rounded">
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

        <div class="flex flex-row py-2">
            <!-- edit settings from -->
            <div class="flex flex-row w-2/4 px-2">
                <div class="flex flex-col justify-center">
                    <label class="text-slate-200 font-bold text-2xl">Points Per Question: </label>
                </div>
                <input class="text-center text-2xl raleway bg-transparent text-yellow-500 font-bold raleway" min="0" max="100" type="number" id="points-per-question" name="points-per-question" v-model="pointsPerQuestion">
            </div>
            <div class="flex flex-row w-2/4 px-2">
                <div class="flex flex-col justify-center">
                    <label class="text-slate-200 font-bold text-2xl">Speed Bonus Points:</label>
                </div>
                <input class="text-center text-2xl raleway bg-transparent text-yellow-500 font-bold raleway" min="0" max="100"  type="number" id="bonus-for-speed" name="bonus-for-speed" v-model="bonusForSpeed">
            </div>
        </div>

        <div class="flex flex-row justify-center px-2">
            <!-- button to start timer -->
            <div class="px-2">
                <button class="py-4 px-4 shadow-md bg-rose-600 text-left text-slate-100 text-xl font-semibold mb-2 rounded" @click="startTimer()">
                    Start Timer
                </button>
            </div>
            <!-- button to show correct answer -->
            <div class="px-2">
                <button class="py-4 px-4 shadow-md bg-cyan-600 text-left text-slate-100 text-xl font-semibold mb-2 rounded" @click="showCorrectAnswer()">
                    Show Correct Answer
                </button>
            </div>
        </div>

    </div>

    <div v-else class="text-center flex flex-col justify-center h-full">
        <!-- add some waiting animation here -->
        <h1 class="text-4xl josefin-sans text-yellow-500 ">Waiting for first question to load!</h1>
    </div>
</div>
