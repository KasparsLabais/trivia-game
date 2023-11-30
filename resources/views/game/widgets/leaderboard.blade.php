<div class="bg-zinc-600">
    <div class="flex flex-col px-2">
        <h2 class="text-yellow-500 josefin-sans text-2xl">Leaderboard:</h2>
        <div v-for="(leader, index) in leaderboard" class="flex flex-row text-slate-200 font-semibold text-lg">
            <span class="w-2/12">[[ index + 1 ]]st</span><span class="w-8/12">| [[ leader.username ]]</span>|<span class="w-2/12 text-center">[[ leader.points ]] </span>
        </div>
    </div>
</div>