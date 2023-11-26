<div class="flex flex-col">
    <div v-for="question in game.questions">
        <!-- TODO: flag question in different color if it is answered -->
        <div class="w-full border-b border-b-zinc-500 px-2 py-2" v-on:click="selectQuestion(question.id)">
            <p class="text-slate-200 font-semibold"><span class="text-slate-400">[[ question.order_nr ]])</span> [[ question.question ]]</p>
            <div class="text-slate-200">
                Type, etc
            </div>
        </div>
    </div>
</div>