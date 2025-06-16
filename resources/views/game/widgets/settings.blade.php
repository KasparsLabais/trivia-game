<div>
    <div class="flex flex-col bg-main-light">
        <!-- edit settings from -->

        <div class="flex flex-col pb-4 px-2">
            <div>
                <span class="font-semibold text-main-dark">Main Settings</span>
            </div>
            <div class="flex flex-row py-1">
                <div class="flex flex-col justify-center">
                    <label for="" class="text-slate-600 font-bold text-normal px-2">Title: </label>
                </div>
                <input v-on:change="changeTriviaTitle()"  class="text-center text-normal border border-slate-500 rounded font-bold raleway" type="text" v-model="game.gameInstance.title" name="title" id="title">
            </div>

            <div class="flex flex-row py-1">
                <div class="flex flex-col justify-center">
                    <label class="text-slate-600 font-bold text-normal px-2">Points Per Question: </label>
                </div>
                <input class="text-center text-normal border border-slate-500 rounded font-bold raleway" min="0" max="100" type="number" id="points-per-question" name="points-per-question" v-model="pointsPerQuestion">
            </div>
            <div class="flex flex-row py-1">
                <div class="flex flex-col justify-center">
                    <label class="text-slate-600 font-bold text-normal px-2">Speed Bonus Points:</label>
                </div>
                <input class="text-center text-normal border border-slate-500 rounded font-bold raleway" min="0" max="100"  type="number" id="bonus-for-speed" name="bonus-for-speed" v-model="bonusForSpeed">
            </div>
        </div>

        <hr class="mx-6 my-2.5">

        <div class="flex flex-col pb-4 px-2">
            <div class="flex flex-row justify-between w-full">
                <div class="flex flex-row py-1">
                    <div class="flex flex-col justify-center">
                        <label class="text-slate-600 font-bold text-normal px-2">Timer Settings</label>
                    </div>
                    <div class="flex flex-col justify-center" class="check-box">
                        <input v-on:change="changeTimeLimitEnabled()"  class="custom-checkbox" type="checkbox" name="time_limit_enabled" id="time_limit_enabled" v-model="settings.timeLimitEnabled">
                    </div>
                </div>
                <div class="flex flex-row  py-1">
                    <div class="flex flex-col justify-center">
                        <label class="text-slate-600 font-bold text-normal px-2" for="time_per_question">Time (s):</label>
                    </div>
                    <input v-on:change="changeTimeLimit()" type="number" step="1" min="0" max="500" class="text-center text-normal border border-slate-500 rounded font-bold raleway" v-model="settings.timePerQuestion" name="time_per_question" id="time_per_question" />
                </div>
            </div>
        </div>

        <div class="flex flex-col pb-4 px-2">
            <div class="flex flex-row justify-between w-full">

                <div class="flex flex-row py-1">
                    <div class="flex flex-col justify-center">
                        <label class="text-slate-600 font-bold text-normal px-2" for="player_limit_enabled">Player Limit</label>
                    </div>
                    <div class="flex flex-col justify-center" class="check-box">
                        <input v-on:change="changePlayerLimit()" class="custom-checkbox" type="checkbox" v-model="settings.playerLimitEnabled" name="player_limit_enabled" id="player_limit_enabled">
                    </div>
                </div>
                <div class="flex flex-row py-1">
                    <div class="flex flex-col justify-center">
                        <label class="text-slate-600 font-bold text-normal px-2" for="player_limit">Limit:</label>
                    </div>
                    <input v-on:change="changePlayerLimit()" type="number" step="1" min="0" max="500" class="text-center text-normal border border-slate-500 rounded font-bold raleway" v-model="settings.playerLimit" name="player_limit" id="player_limit" />
                </div>
            </div>
        </div>

        <hr class="mx-6 my-2.5">

        <div class="flex flex-col pb-4 px-2">
            <div>
                <span class="font-semibold text-main-dark">Trivia Accessibility</span>
            </div>
            <div class="flex flex-row justify-between w-full flex-wrap">
                <div class="flex flex-row w-full">
                    <div class="w-full flex flex-row shadow-inner rounded bg-slate-300 py-2 px-2">
                        <div class="px-1 w-2/4">
                            <div v-on:click="changeAccessibility('private')" class="accessibility-setting text-center px-2 py-2 rounded font-semibold shadow" :class="{'btn-main-accent' : settings.accessibility == 'private', 'btn-main-light' : settings.accessibility != 'private'}">
                                Private
                            </div>
                        </div>
                        <div class="px-1 w-2/4">
                            <div v-on:click="changeAccessibility('public')" class="accessibility-setting text-center px-2 py-2 rounded font-semibold shadow" :class="{'btn-main-accent' : settings.accessibility == 'public', 'btn-main-light' : settings.accessibility != 'public'}">
                                Public
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-row w-full">
                    <div v-if="settings.accessibility == 'public' ">
                        <p class="text-slate-600 font-bold text-normal px-2">Anyone can join your game trough open game selector.</p>
                    </div>
                    <div v-if="settings.accessibility == 'private' ">
                        <p class="text-slate-600 font-bold text-normal px-2">Only people that have URL or PIN can join your game.</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mx-6 my-2.5">
    </div>
</div>