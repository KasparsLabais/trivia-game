<div class="flex flex-col">
    <div class="w-full border-b border-b-zinc-500 px-2 py-2" v-on:click="deselectQuestion()">
        <p class="text-slate-200 font-semibold text-2xl text-center">Back to main screen</p>
    </div>

    <div v-for="question in questions">
        <!-- TODO: flag question in different color if it is answered -->
        <div class="w-full border-b border-b-zinc-500 px-2 py-2" v-on:click="selectQuestion(question.id)" :class="{'bg-gray-600' : question.id == selectedQuestionId, 'bg-cyan-700' : hasBeenPlayed(question.id) }">
            <p class="text-slate-200 font-semibold"><span class="text-slate-400">[[ question.order_nr ]])</span> [[ question.question ]]</p>
            <div class="text-slate-200">
                Type, etc
            </div>
        </div>
    </div>

    <div class="w-full border-b border-b-zinc-500 px-2 py-2">
        <button class="text-center px-2 py-2 rounded font-semibold shadow bg-rose-600 text-gray-100" v-on:click="completeTrivia()">
            Complete Quiz
        </button>
    </div>
</div>