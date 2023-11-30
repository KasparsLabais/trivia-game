<div class="bg-zinc-600 py-3">
    <div class="flex flex-col px-2">
        <h2 class="text-yellow-500 josefin-sans text-2xl">Leaderboard:</h2>
        <div v-for="(leader, index) in leaderboard" class="flex flex-row text-slate-200 font-semibold text-lg">
            <span class="w-2/12">[[ index + 1 ]]st</span><span class="w-7/12"> [[ leader.username ]]</span><span class="w-3/12 text-center">[[ leader.points ]] Pts.</span>
        </div>
        <div class="py-4">
            <button class="text-center px-2 py-2 rounded font-semibold shadow bg-lime-600 text-gray-100" @click="updateLeaderboard()">Update Leaderboard</button>
        </div>
    </div>
</div>