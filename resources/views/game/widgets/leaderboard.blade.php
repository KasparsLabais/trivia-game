<div>
    <div class="flex flex-col bg-main-light">
        <div class="flex flex-col pb-2 px-2">
            <h2 class="text-main-accent font-semibold josefin-sans text-2xl">Leaderboard:</h2>
        </div>
        <div v-for="(leader, index) in leaderboard" class="flex flex-row text-main-dark font-semibold text-lg px-2">
            <span class="w-2/12">[[ index + 1 ]]st</span><span class="w-7/12"> [[ leader.username ]]</span><span class="w-3/12 text-center">[[ leader.points ]] Pts.</span>
        </div>
        <div class="py-4 px-2">
            <button class="text-center px-2 py-2 rounded font-semibold shadow btn-main-accent" @click="updateLeaderboard()">Update Leaderboard</button>
        </div>

        <hr class="mx-6 my-2.5">
    </div>
</div>