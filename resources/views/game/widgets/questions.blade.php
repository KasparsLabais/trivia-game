<div class="flex flex-col">
    <div class="w-full border-b border-b-zinc-500 px-2 py-2" v-on:click="deselectQuestion()">
        <p class="text-slate-200 font-semibold text-2xl text-center">Back to main screen</p>
    </div>

    <div v-for="question in questions">
        <!-- TODO: flag question in different color if it is answered -->
        <div class="w-full border-b border-b-zinc-500 px-2 py-2" v-on:click="selectQuestion(question.id)" :class="{'bg-panel' : question.id == selectedQuestionId, 'bg-brand' : hasBeenPlayed(question.id) }">
            <p class="text-slate-200 font-semibold"><span class="text-slate-400">[[ question.order_nr ]])</span> [[ question.question ]]</p>
            <div class="text-slate-200">
                Type, etc
            </div>
        </div>
    </div>

    <div class="w-full border-b border-b-zinc-500 px-2 py-2 flex flex-col gap-2">
        {{-- Optional: play the results reveal on the Display Screen first; players'
             phones only get the results once the quiz is completed. --}}
        <button class="text-center px-2 py-2 rounded font-semibold shadow bg-brand text-white" v-on:click="revealResultsOnDisplay()">
            [[ resultsRevealedOnDisplay ? 'Replay Results on Display' : 'Reveal Results on Display' ]]
        </button>
        <p v-if="resultsRevealedOnDisplay" class="text-slate-300 text-sm">Revealing on the big screen. Complete the quiz to send results to players' phones.</p>
        <button class="text-center px-2 py-2 rounded font-semibold shadow bg-main-accent text-white" v-on:click="completeTrivia()">
            Complete Quiz
        </button>
    </div>
</div>