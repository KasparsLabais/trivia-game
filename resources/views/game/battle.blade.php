@extends('game-api::layout')
@section('page-bg', 'bg-pop')
@section('body')

    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

    <style>
        .dotted-canvas {
            background-color: #FFB900;
            background-image: radial-gradient(#14323B 2.5px, transparent 2.6px);
            background-size: 26px 26px;
        }
        .battle-pop { animation: battle-pop 0.45s cubic-bezier(.2, 1.6, .4, 1) both; }
        @keyframes battle-pop { from { opacity: 0; transform: scale(0.6); } to { opacity: 1; transform: scale(1); } }
        .battle-stamp { animation: battle-stamp 0.5s cubic-bezier(.2, 1.4, .4, 1) both; }
        @keyframes battle-stamp { from { opacity: 0; transform: scale(1.8) rotate(-8deg); } to { opacity: 1; transform: scale(1) rotate(-3deg); } }
        @media (prefers-reduced-motion: reduce) { .battle-pop, .battle-stamp { animation: none; } }
    </style>

    {{-- Trivia Battle: friends play the same Random Quiz together, no game master.
         Everything is timed from the server's start time; see QuizBattleController. --}}
    <div id="battle-app" class="dotted-canvas min-h-screen px-4 py-8 md:py-12">
        <div class="w-full max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-6 items-start">

            {{-- ===================== MAIN WINDOW ===================== --}}
            <div class="bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm truncate">{{ $battle->tmpTrivia->title ?? 'Trivia Battle' }}</span>
                    <span v-if="phase == 'question' || phase == 'reveal'" class="ml-auto shrink-0 fira-sans text-xs uppercase tracking-widest text-white/60">Q <span class="russo text-white text-base">[[ qIndex + 1 ]]</span>/[[ total ]]</span>
                </div>

                {{-- Timer bar --}}
                <div v-if="phase == 'question'" class="w-full bg-main-light border-b-4 border-main-dark">
                    <div class="flex flex-row items-center px-5 py-3 gap-4">
                        <div class="flex-1 h-4 bg-white border-2 border-main-dark overflow-hidden">
                            <div class="h-full" :class="timerClass" :style="{ width: timerPct + '%', transition: 'width 0.25s linear' }"></div>
                        </div>
                        <span class="russo text-xl text-main-dark w-12 text-right">[[ Math.ceil(timeLeft) ]]</span>
                    </div>
                </div>

                <div class="p-6 md:p-10 flex flex-col gap-6">

                    {{-- Loading --}}
                    <div v-if="phase == 'loading'" class="py-16 text-center fira-sans text-main-dark/60">Loading battle&hellip;</div>

                    {{-- LOBBY --}}
                    <template v-if="phase == 'lobby'">
                        <div class="text-center">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Trivia Battle &middot; lobby</span>
                            <h1 class="russo text-main-dark text-3xl md:text-4xl leading-tight mt-1">Waiting for players</h1>
                            <p class="fira-sans text-main-dark/70 mt-2">[[ total ]] questions &middot; [[ timeLimit ]]s each &middot; everyone answers at the same time &mdash; no game master.</p>
                        </div>

                        <div class="flex flex-wrap justify-center gap-3">
                            <div v-for="p in players" :key="p.user_id" class="battle-pop flex items-center gap-2 border-4 border-main-dark bg-main-light pl-1 pr-3 py-1">
                                <img :src="p.avatar" alt="" class="w-9 h-9 border-2 border-main-dark object-cover" v-on:error="$event.target.src='/images/default-avatar.jpg'">
                                <span class="fira-sans font-bold text-main-dark">[[ p.username ]]</span>
                                <span v-if="p.user_id == hostId" class="bg-pop border-2 border-main-dark px-1 russo text-[10px] uppercase">Host</span>
                            </div>
                        </div>

                        {{-- Not in it yet: join --}}
                        <div v-if="!isPlayer" class="flex flex-col items-center gap-2">
                            <button type="button" @click="join()" :disabled="busy"
                                    class="bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-10 py-3.5 text-2xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B] disabled:opacity-50">Join Battle</button>
                        </div>

                        <template v-else>
                            {{-- Start (creator only) --}}
                            <div v-if="isHost" class="flex flex-col items-center gap-2">
                                <button type="button" @click="start()" :disabled="busy || players.length < 2"
                                        class="bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-10 py-3.5 text-2xl font-black uppercase tracking-wider fira-sans transition-all duration-100 enabled:hover:-translate-x-0.5 enabled:hover:-translate-y-0.5 enabled:hover:shadow-[8px_8px_0_0_#14323B] enabled:active:translate-x-1 enabled:active:translate-y-1 enabled:active:shadow-[0px_0px_0_0_#14323B] disabled:opacity-40 disabled:cursor-not-allowed">Start Battle</button>
                                <span v-if="players.length < 2" class="fira-sans text-sm text-main-dark/60">Invite at least one friend to start.</span>
                            </div>
                            <p v-else class="text-center fira-sans font-bold text-main-dark/70">Waiting for the host to start the battle&hellip;</p>

                            {{-- Invite --}}
                            <div class="border-4 border-main-dark">
                                <div class="bg-brand border-b-4 border-main-dark px-4 py-2 russo uppercase tracking-widest text-white text-xs">Invite friends</div>
                                <div class="p-4 flex flex-col gap-3">
                                    <div class="flex flex-row items-stretch border-4 border-main-dark">
                                        <span class="flex-1 min-w-0 bg-main-light px-3 py-2 fira-sans text-sm font-bold text-main-dark break-all">{{ $joinUrl }}</span>
                                        <button type="button" @click="copyLink()" class="shrink-0 bg-main-dark text-white border-l-4 border-main-dark px-4 russo text-xs uppercase tracking-wide">[[ copied ? 'Copied!' : 'Copy' ]]</button>
                                    </div>
                                    <p v-if="!friends.length && friendsLoaded" class="fira-sans text-sm text-main-dark/60">No friends yet &mdash; add some from the mail envelope, or share the link.</p>
                                    <div v-for="f in friends" :key="f.id" class="flex items-center gap-3 border-2 border-main-dark px-2 py-1.5">
                                        <img :src="f.avatar" alt="" class="w-8 h-8 border-2 border-main-dark object-cover" v-on:error="$event.target.src='/images/default-avatar.jpg'">
                                        <span class="flex-1 min-w-0 fira-sans font-bold text-main-dark truncate">[[ f.username ]]</span>
                                        <span v-if="inBattle(f.id)" class="russo text-[10px] uppercase text-green-600">In battle</span>
                                        <button v-else type="button" @click="invite(f)" :disabled="invited[f.id]"
                                                class="border-2 border-main-dark px-3 py-1 russo text-[10px] uppercase tracking-wide disabled:opacity-60" :class="invited[f.id] ? 'bg-main-light text-main-dark' : 'bg-pop text-main-dark hover:bg-white'">[[ invited[f.id] ? 'Invited ✓' : 'Invite' ]]</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <p v-if="error" class="text-center fira-sans font-bold text-main-accent">[[ error ]]</p>
                    </template>

                    {{-- Too late to join --}}
                    <div v-if="phase == 'spectating'" class="py-12 text-center flex flex-col items-center gap-3">
                        <h1 class="russo text-main-dark text-3xl">This battle has already started</h1>
                        <p class="fira-sans text-main-dark/60">Ask your friend to invite you to the next one.</p>
                        <a href="/trivia" class="mt-2 bg-white text-main-dark border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 russo uppercase tracking-wide">Back to Quizzes</a>
                    </div>

                    {{-- COUNTDOWN --}}
                    <div v-if="phase == 'countdown'" class="flex flex-col items-center gap-4 py-10 text-center">
                        <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">[[ players.length ]] players &middot; [[ total ]] questions</span>
                        <h1 class="russo text-main-dark text-3xl md:text-4xl">Battle starts in&hellip;</h1>
                        <span :key="Math.ceil(countdownLeft)" class="battle-pop russo text-7xl md:text-8xl text-main-accent">[[ Math.max(1, Math.ceil(countdownLeft)) ]]</span>
                    </div>

                    {{-- QUESTION / REVEAL --}}
                    <template v-if="phase == 'question' || phase == 'reveal'">
                        <div class="text-center">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Question [[ qIndex + 1 ]] of [[ total ]]</span>
                            <h1 class="russo text-main-dark text-2xl md:text-4xl leading-tight break-words mt-2">[[ current.question ]]</h1>
                        </div>

                        <div v-if="current.question_type == 'options'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button v-for="(answer, i) in current.answers" :key="answer.id" type="button" @click="pickOption(answer.id)"
                                    class="w-full flex flex-row items-stretch text-left border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] transition-all duration-100"
                                    :class="optionClass(answer.id)">
                                <span class="flex items-center justify-center w-12 shrink-0 russo text-xl border-r-4 border-main-dark" :class="letterClass(answer.id)">[[ 'ABCDEF'.charAt(i) ]]</span>
                                <span class="flex-1 min-w-0 px-4 py-3.5 fira-sans text-lg md:text-xl font-bold break-words">[[ answer.answer ]]</span>
                            </button>
                        </div>

                        <div v-else class="w-full max-w-xl mx-auto flex flex-col gap-4">
                            <input v-model="typed" @keyup.enter="submitTyped()" :disabled="locked || phase != 'question'" type="text" placeholder="Type your answer" autocomplete="off"
                                   class="w-full bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 px-4 text-center text-2xl text-main-dark font-bold placeholder:text-base placeholder:font-medium placeholder:text-gray-400 focus:outline-none fira-sans disabled:bg-main-light">
                            <button v-if="!locked && phase == 'question'" @click="submitTyped()" type="button" class="w-full bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Submit Answer</button>
                            <div v-if="phase == 'reveal' && reveal" class="flex flex-row items-stretch border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]">
                                <span class="flex items-center bg-green-500 text-white px-3 russo text-xs uppercase tracking-widest shrink-0 border-r-4 border-main-dark">Correct</span>
                                <span class="flex-1 min-w-0 bg-white text-main-dark px-4 py-3 fira-sans font-bold text-xl break-words">[[ reveal.correct_answer_text ]]</span>
                            </div>
                        </div>

                        <div class="text-center min-h-[3.5rem] flex items-center justify-center">
                            <span v-if="phase == 'question' && locked" class="fira-sans text-main-dark/70 font-bold">Locked in! Waiting for the clock&hellip;</span>
                            <span v-else-if="phase == 'question'" class="fira-sans text-main-dark/60">Answer before the time runs out!</span>
                            <span v-else-if="!reveal" class="fira-sans text-main-dark/60">Revealing&hellip;</span>
                            <span v-else :key="'stamp' + qIndex" class="battle-stamp inline-block border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-5 py-1.5 russo text-2xl md:text-3xl uppercase tracking-wider"
                                  :class="myResult ? 'bg-green-500 text-white' : (myAnswer == null ? 'bg-pop text-main-dark' : 'bg-main-accent text-white')">
                                [[ myResult ? 'Correct!' : (myAnswer == null ? "Time's up!" : 'Wrong!') ]]
                            </span>
                        </div>
                    </template>

                    {{-- RESULTS --}}
                    <div v-if="phase == 'results'" class="flex flex-col items-center gap-6">
                        <div class="text-center">
                            <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Final standings</span>
                            <h1 class="battle-pop russo text-main-dark text-3xl md:text-4xl mt-1">[[ winnerLine ]]</h1>
                        </div>
                        <div class="w-full border-4 border-main-dark">
                            <div v-for="(p, i) in standings" :key="p.user_id" class="flex items-center gap-3 px-4 py-3" :class="[i == 0 ? 'bg-pop' : (i % 2 ? 'bg-white' : 'bg-main-light'), i < standings.length - 1 ? 'border-b-2 border-main-dark/20' : '']">
                                <span class="russo text-xl w-8 text-main-dark/60">#[[ p.rank ]]</span>
                                <img :src="p.avatar" alt="" class="w-9 h-9 border-2 border-main-dark object-cover" v-on:error="$event.target.src='/images/default-avatar.jpg'">
                                <span class="flex-1 min-w-0 fira-sans font-bold text-main-dark truncate">[[ p.username ]]<span v-if="p.user_id == me" class="text-main-dark/50 font-normal"> (you)</span></span>
                                <span class="russo text-xl text-main-dark">[[ p.score ]]<span class="text-main-dark/40 text-base"> / [[ total ]]</span></span>
                            </div>
                        </div>

                        {{-- Your recap --}}
                        <div v-if="isPlayer" class="w-full border-4 border-main-dark">
                            <div class="bg-main-dark px-4 py-2 russo uppercase tracking-widest text-white text-xs">Your answers</div>
                            <div v-for="(q, i) in questions" :key="q.order_nr" class="flex flex-row items-start gap-3 px-4 py-3" :class="[i % 2 ? 'bg-white' : 'bg-main-light']">
                                <span class="shrink-0 w-7 h-7 flex items-center justify-center border-2 border-main-dark russo text-sm" :class="resultFor(q.order_nr) ? 'bg-green-500 text-white' : 'bg-main-accent text-white'">[[ resultFor(q.order_nr) ? '✓' : '✗' ]]</span>
                                <div class="flex-1 min-w-0">
                                    <p class="fira-sans font-bold text-main-dark break-words">[[ i + 1 ]]. [[ q.question ]]</p>
                                    <p class="fira-sans text-sm text-main-dark/70 break-words">
                                        <span v-if="!resultFor(q.order_nr)">You: <span class="font-bold">[[ givenText(q) || 'no answer' ]]</span> &middot; </span>Answer: <span class="font-bold text-green-700">[[ (reveals[q.order_nr] || {}).correct_answer_text ]]</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                            <form method="POST" action="/trivia/battle">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="category" value="{{ $battle->settings['category'] ?? 23 }}">
                                <input type="hidden" name="difficulty" value="{{ $battle->settings['difficulty'] ?? 'any' }}">
                                <input type="hidden" name="question_count" value="{{ $battle->settings['question_count'] ?? 10 }}">
                                <button type="submit" class="w-full sm:w-64 bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">New Battle</button>
                            </form>
                            <a href="/trivia" class="sm:w-64 text-center bg-white text-main-dark border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Back to Quizzes</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===================== SCOREBOARD ===================== --}}
            <div v-if="phase == 'question' || phase == 'reveal' || phase == 'countdown'" class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] overflow-hidden">
                <div class="bg-brand border-b-4 border-main-dark px-4 py-2.5 russo uppercase tracking-widest text-white text-xs">Scoreboard</div>
                <div v-for="p in standings" :key="p.user_id" class="flex items-center gap-2.5 px-3 py-2.5 border-b-2 border-main-dark/15 last:border-b-0" :class="p.user_id == me ? 'bg-pop/30' : ''">
                    <img :src="p.avatar" alt="" class="w-8 h-8 border-2 border-main-dark object-cover" v-on:error="$event.target.src='/images/default-avatar.jpg'">
                    <span class="flex-1 min-w-0 fira-sans font-bold text-sm text-main-dark truncate">[[ p.username ]]</span>
                    {{-- This question: locked in / thinking, or right / wrong once revealed --}}
                    <span v-if="phase == 'reveal' && reveal" class="w-6 h-6 flex items-center justify-center border-2 border-main-dark russo text-xs" :class="reveal.results[p.user_id] ? 'bg-green-500 text-white' : 'bg-main-accent text-white'">[[ reveal.results[p.user_id] ? '✓' : '✗' ]]</span>
                    <span v-else-if="phase == 'question'" class="fira-sans text-[10px] uppercase tracking-wide font-bold" :class="p.answered.includes(currentNr) ? 'text-green-600' : 'text-main-dark/40'">[[ p.answered.includes(currentNr) ? 'Locked ✓' : 'Thinking…' ]]</span>
                    <span class="russo text-lg text-main-dark w-8 text-right">[[ p.score ]]</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    code: @json($battle->code),
                    joinUrl: @json($joinUrl),
                    questions: @json($questions),
                    csrf: '{{ csrf_token() }}',
                    loaded: false,
                    status: 'lobby',
                    startsAt: null,
                    timeLimit: {{ (int) $battle->time_limit }},
                    revealSeconds: {{ (int) $battle->reveal_seconds }},
                    total: {{ count($questions) }},
                    hostId: null,
                    me: null,
                    isPlayer: false,
                    players: [],
                    myAnswers: {},
                    reveals: {},
                    offset: 0,             // server clock - local clock
                    nowMs: Date.now(),
                    // current question, local
                    activeNr: null,
                    picked: null,
                    typed: '',
                    locked: false,
                    // lobby
                    friends: [], friendsLoaded: false, invited: {}, copied: false, busy: false, error: '',
                };
            },
            delimiters: ['[[', ']]'],
            computed: {
                isHost() { return this.me != null && this.me == this.hostId; },
                slotMs() { return (this.timeLimit + this.revealSeconds) * 1000; },
                elapsed() { return this.startsAt ? this.nowMs - this.startsAt : -1; },
                qIndex() { return this.startsAt ? Math.max(0, Math.min(this.total - 1, Math.floor(this.elapsed / this.slotMs))) : 0; },
                currentNr() { return this.qIndex + 1; },
                current() { return this.questions[this.qIndex] || {}; },
                phase() {
                    if (!this.loaded) return 'loading';
                    if (this.status == 'lobby') return 'lobby';
                    if (!this.isPlayer && this.status != 'finished') return 'spectating';
                    if (this.status == 'finished' || this.elapsed >= this.total * this.slotMs) return 'results';
                    if (this.elapsed < 0) return 'countdown';
                    return (this.elapsed % this.slotMs) < this.timeLimit * 1000 ? 'question' : 'reveal';
                },
                countdownLeft() { return this.startsAt ? (this.startsAt - this.nowMs) / 1000 : 0; },
                timeLeft() { return Math.max(0, this.timeLimit - (this.elapsed % this.slotMs) / 1000); },
                timerPct() { return Math.max(0, (this.timeLeft / this.timeLimit) * 100); },
                timerClass() {
                    const p = this.timerPct;
                    return p < 20 ? 'bg-main-accent' : (p < 35 ? 'bg-secondly-accent' : (p < 65 ? 'bg-pop' : 'bg-green-500'));
                },
                reveal() { return this.reveals[this.currentNr] || null; },
                myAnswer() { const a = this.myAnswers[this.currentNr]; return a === undefined ? null : a; },
                myResult() { return this.reveal ? !!this.reveal.results[this.me] : false; },
                standings() {
                    const sorted = this.players.slice().sort((a, b) => b.score - a.score || a.username.localeCompare(b.username));
                    let rank = 0, last = null;
                    return sorted.map((p, i) => { if (p.score !== last) { rank = i + 1; last = p.score; } return Object.assign({ rank }, p); });
                },
                winnerLine() {
                    const s = this.standings;
                    if (!s.length) return 'Battle over';
                    const top = s.filter(p => p.rank === 1);
                    if (top.length > 1) return "It's a tie!";
                    return top[0].user_id == this.me ? 'You win! 🏆' : top[0].username + ' wins!';
                },
            },
            watch: {
                // New question: reset the local answer state (or restore it after a reload).
                currentNr: { handler() { this.syncActive(); } },
                phase() { this.syncActive(); },
            },
            methods: {
                api(path, method, body) {
                    return fetch('/trivia/battle/' + this.code + path, {
                        method: method || 'GET',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                        body: body ? JSON.stringify(body) : undefined,
                    }).then(r => r.json()).catch(() => ({ success: false, message: 'Connection problem — try again.' }));
                },
                poll() {
                    this.api('/state').then(res => {
                        if (!res || !res.success) return;
                        const d = res.data;
                        this.offset = d.server_now - Date.now();
                        Object.assign(this, {
                            status: d.status, startsAt: d.starts_at, total: d.total, hostId: d.host_user_id, me: d.me,
                            isPlayer: d.is_player, players: d.players, myAnswers: Array.isArray(d.my_answers) ? {} : d.my_answers,
                            reveals: d.reveals || {}, timeLimit: d.time_limit, revealSeconds: d.reveal_seconds,
                        });
                        this.loaded = true;
                        this.syncActive();
                        if (this.status == 'finished') clearInterval(this.pollTimer);
                    });
                },
                syncActive() {
                    if (this.phase != 'question' && this.phase != 'reveal') return;
                    if (this.activeNr !== this.currentNr) {
                        this.activeNr = this.currentNr; this.picked = null; this.typed = ''; this.locked = false;
                    }
                    const given = this.myAnswer;
                    if (given !== null && !this.locked) {
                        this.locked = true;
                        if (this.current.question_type == 'options') this.picked = Number(given); else this.typed = String(given);
                    }
                },
                lockIn(body) {
                    if (this.locked || this.phase != 'question') return;
                    this.locked = true;
                    const nr = this.currentNr;
                    this.api('/answer', 'POST', Object.assign({ order_nr: nr }, body)).then(res => {
                        if (res && res.success) { this.myAnswers = Object.assign({}, this.myAnswers, { [nr]: body.answer_id ?? body.answer_text }); }
                    });
                },
                pickOption(id) { if (!this.locked && this.phase == 'question') { this.picked = id; this.lockIn({ answer_id: id }); } },
                submitTyped() { if (this.typed.trim()) this.lockIn({ answer_text: this.typed.trim() }); },
                optionClass(id) {
                    const isCorrect = this.phase == 'reveal' && this.reveal && (this.reveal.correct_answer_ids || []).includes(id);
                    const isPicked = this.picked === id;
                    if (isCorrect) return 'bg-green-500 text-white';
                    if (this.phase == 'reveal' && isPicked) return 'bg-main-accent text-white';
                    if (isPicked) return 'bg-brand text-white';
                    if (this.locked || this.phase == 'reveal') return 'bg-white text-main-dark opacity-50';
                    return 'bg-white text-main-dark hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]';
                },
                letterClass(id) {
                    const isCorrect = this.phase == 'reveal' && this.reveal && (this.reveal.correct_answer_ids || []).includes(id);
                    return (isCorrect || this.picked === id) ? 'bg-main-dark text-white' : 'bg-pop text-main-dark';
                },
                resultFor(nr) { const r = this.reveals[nr]; return r ? !!r.results[this.me] : false; },
                givenText(q) {
                    const g = this.myAnswers[q.order_nr];
                    if (g === undefined || g === null) return '';
                    if (q.question_type != 'options') return String(g);
                    const a = (q.answers || []).find(x => x.id == g);
                    return a ? a.answer : '';
                },
                // ----- lobby -----
                join() {
                    this.busy = true; this.error = '';
                    this.api('/join', 'POST', {}).then(res => { this.busy = false; if (!res.success) this.error = res.message; this.poll(); });
                },
                start() {
                    this.busy = true; this.error = '';
                    this.api('/start', 'POST', {}).then(res => { this.busy = false; if (!res.success) this.error = res.message; this.poll(); });
                },
                loadFriends() {
                    fetch('/friends/list', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                        .then(r => r.json()).then(res => { this.friends = (res && res.success) ? res.data : []; this.friendsLoaded = true; })
                        .catch(() => { this.friendsLoaded = true; });
                },
                inBattle(userId) { return this.players.some(p => p.user_id == userId); },
                // The invite is a normal chat message carrying the battle link; the
                // friends widget shows it as an invite card with a Join button.
                invite(friend) {
                    const message = '⚔️ Join my Trivia Battle! ' + this.joinUrl;
                    this.invited = Object.assign({}, this.invited, { [friend.id]: true });
                    fetch('/friends/messages', {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                        body: JSON.stringify({ to_user_id: friend.id, message }),
                    }).then(r => r.json()).then(res => {
                        if (!res || !res.success) { this.invited = Object.assign({}, this.invited, { [friend.id]: false }); this.error = (res && res.message) || 'Could not send the invite.'; return; }
                        const m = res.data;
                        // Same live relay the friends widget uses, so it pops up instantly.
                        if (window.socket && window.socket.connected) {
                            window.socket.emit('friendsMessage', { toUserId: friend.id, payload: { id: m.id, from_user_id: m.from_user_id, from_username: m.from_username, from_avatar: m.from_avatar, message: m.message, created_at: m.created_at } });
                        }
                    }).catch(() => { this.invited = Object.assign({}, this.invited, { [friend.id]: false }); });
                },
                copyLink() {
                    if (!navigator.clipboard) return;
                    navigator.clipboard.writeText(this.joinUrl).then(() => { this.copied = true; setTimeout(() => { this.copied = false; }, 2000); });
                },
            },
            mounted() {
                this.poll();
                this.pollTimer = setInterval(() => this.poll(), 1000);
                setInterval(() => { this.nowMs = Date.now() + this.offset; }, 100);
                this.loadFriends();
            },
        }).mount('#battle-app');
    </script>

@endsection
