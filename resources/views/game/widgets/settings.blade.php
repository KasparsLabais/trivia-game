<div>
    <div class="flex flex-col bg-zinc-200">
        <!-- edit settings from -->

        <div class="flex flex-col border-b border-b-slate-300 pb-4  px-2">
            <div>
                <span class="font-semibold">Time Settings</span>
            </div>
            <div class="flex flex-row justify-between w-full">
                <div class="flex flex-row w-3/6">
                    <label class="raleway font-normal text-base" for="time_limit_enabled">Enabled:</label>
                    <select v-on:change="changeTimeLimitEnabled()" class="raleway font-normal text-base capitalize" name="time_limit_enabled" id="time_limit_enabled" v-model="settings.timeLimitEnabled">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>
                <div class="flex flex-row w-3/6">
                    <label class="raleway font-normal text-base w-4/6" for="time_per_question">Time (s):</label>
                    <input v-on:change="changeTimeLimit()" type="number" step="1" min="0" class="text-center raleway font-normal text-base capitalize w-2/6" v-model="settings.timePerQuestion" name="time_per_question" id="time_per_question" />
                </div>
            </div>
        </div>

        <div class="flex flex-col border-b border-b-slate-300 pb-4 px-2">
            <div>
                <span class="font-semibold">Player Limit</span>
            </div>
            <div class="flex flex-row justify-between w-full">
                <div class="flex flex-row w-3/6">
                    <label class="raleway font-normal text-base" for="player_limit_enabled">Enabled:</label>
                    <select v-on:change="changePlayerLimit()" class="raleway font-normal text-base capitalize" v-model="settings.playerLimitEnabled" name="player_limit_enabled" id="player_limit_enabled">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>
                <div class="flex flex-row w-3/6">
                    <label class="raleway font-normal text-base w-4/6" for="time_per_question">Player Limit:</label>
                    <input v-on:change="changePlayerLimit()" type="number" step="1" min="0" class="text-center raleway font-normal text-base capitalize w-2/6" v-model="settings.playerLimit" name="player_limit" id="player_limit" />
                </div>
            </div>
        </div>

        <div class="border-b border-b-slate-300 pb-4">
            <div>
                <span>Trivia Accessibility</span>
            </div>
            <div class="flex flex-row justify-between w-full">
                <div class="flex flex-row w-full">
                    <div class="w-full flex flex-row shadow-inner rounded bg-slate-300 py-2 px-2">
                        <div class="px-1 w-2/4">
                            <div v-on:click="changeAccessibility('private')" class="accessibility-setting text-center px-2 py-2 rounded font-semibold  shadow" :class="{'bg-lime-600 text-gray-100' : settings.accessibility == 'private', 'bg-slate-300 text-gray-400 hover:bg-lime-500 hover:text-gray-100' : settings.accessibility != 'private'}">
                                Private Access
                            </div>
                        </div>
                        <div class="px-1 w-2/4">
                            <div v-on:click="changeAccessibility('public')" class="accessibility-setting text-center px-2 py-2 rounded font-semibold shadow" :class="{'bg-lime-600 text-gray-100' : settings.accessibility == 'public', 'bg-slate-300 text-gray-400 hover:bg-lime-500 hover:text-gray-100' : settings.accessibility != 'public'}">
                                Open Access
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
</div>