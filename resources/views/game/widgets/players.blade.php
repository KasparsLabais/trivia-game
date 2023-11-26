<div id="answered-players-holder" class="flex flex-row w-full">
    <div class="w-full" v-for="player in game.playerInstances">
        <div class="w-3/12 px-1 py-1 user-holder" v-bind:["id"]="(player.user_type == 'guest') ? 'user-holder-' + player.user.tmp_user_id : 'user-holder-' + player.user.id ">
            <div class="relative flex flex-col bg-slate-100 justify-between shadow-md rounded">
                <div class="flex flex-row justify-center relative">
                    <div class="w-2/4 h-16 bg-cover" v-bind:["style"]="(player.user.avatar == null) ? 'background-image: url(/images/default-avatar.jpg)' :  'background-image: url('+player.user.avatar+')'">
                        <div v-if="player.user.icon_flair_id  != null">
                            <img v-bind:["src"]="player.user.icon_flair.icon_url" class="w-6 h-6 absolute left-0"/>
                        </div>
                    </div>
                    <div class="w-2/4" v-bind:["id"]="(player.user_type == 'guest') ? 'answer-holder-' + player.user.tmp_user_id : 'user-holder-' + player.user.id ">
                    </div>
                </div>

                <div class="flex flex-row justify-center bg-slate-200 px-1 py-1">
                    <div class="username-div josefin-sans font-semibold text-slate-700">[[ player.user.username ]] [[player.user.iconFlair ]]</div>
                </div>


                <div class="answered-label hidden bg-rose-600 font-semibold fira-sans text-slate-100 text-sm absolute top-0 right-0 py-1 px-1 rounded">
                    Answered
                </div>
            </div>
        </div>
    </div>
</div>
