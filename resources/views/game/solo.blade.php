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
        .solo-pop { animation: solo-pop 0.45s cubic-bezier(.2, 1.6, .4, 1) both; }
        @keyframes solo-pop { from { opacity: 0; transform: scale(0.6); } to { opacity: 1; transform: scale(1); } }
        .solo-stamp { animation: solo-stamp 0.5s cubic-bezier(.2, 1.4, .4, 1) both; }
        @keyframes solo-stamp { from { opacity: 0; transform: scale(1.8) rotate(-8deg); } to { opacity: 1; transform: scale(1) rotate(-3deg); } }
        @media (prefers-reduced-motion: reduce) { .solo-pop, .solo-stamp { animation: none; } }
    </style>

    {{-- Random Quiz: no game master. Each question is timed, the correct answer is
         revealed when time runs out, and the total score is shown at the end. --}}
    <div id="solo-app" class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
        <div class="w-full max-w-2xl lg:max-w-5xl bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">

            {{-- Title bar: title + progress + score --}}
            <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm truncate">{{ $tmpTrivia->title }}</span>
                <div class="ml-auto flex items-baseline gap-4 shrink-0">
                    <span v-if="view != 'results'" class="fira-sans text-xs uppercase tracking-widest text-white/60">Q <span class="russo text-white text-base">[[ Math.min(index + 1, total) ]]</span>/[[ total ]]</span>
                    <span class="fira-sans text-xs uppercase tracking-widest text-white/60">Score <span class="russo text-pop text-xl md:text-2xl">[[ score ]]</span></span>
                </div>
            </div>

            {{-- Timer bar --}}
            <div v-if="view == 'question'" class="w-full bg-main-light border-b-4 border-main-dark">
                <div class="flex flex-row items-center px-5 py-3 gap-4">
                    <div class="flex-1 h-4 bg-white border-2 border-main-dark overflow-hidden">
                        <div class="h-full" :class="timerClass" :style="{ width: timerPct + '%', transition: 'width 0.25s linear' }"></div>
                    </div>
                    <span class="russo text-xl text-main-dark w-12 text-right">[[ Math.ceil(timeLeft) ]]</span>
                </div>
            </div>

            <div class="p-6 md:p-10 lg:p-12 flex flex-col gap-6 lg:gap-8">

                {{-- Countdown --}}
                <div v-if="view == 'countdown'" class="flex flex-col items-center gap-4 py-10 text-center">
                    <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">[[ total ]] questions &middot; [[ timeLimit ]]s each &middot; no game master</span>
                    <h1 class="russo text-main-dark text-3xl md:text-4xl">Get ready&hellip;</h1>
                    <span :key="countdown" class="solo-pop russo text-7xl md:text-8xl text-main-accent">[[ countdown ]]</span>
                </div>

                {{-- Question / reveal --}}
                <template v-if="view == 'question' || view == 'reveal'">
                    <div class="text-center">
                        <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Question [[ index + 1 ]] of [[ total ]]</span>
                        <h1 class="russo text-main-dark text-2xl md:text-4xl lg:text-5xl leading-tight break-words mt-2">[[ current.question ]]</h1>
                    </div>

                    {{-- Multiple choice --}}
                    <div v-if="current.question_type == 'options'" class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-5">
                        <button v-for="(answer, i) in current.answers" :key="answer.id" type="button" @click="pickOption(answer.id)"
                                class="w-full flex flex-row items-stretch text-left border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] transition-all duration-100"
                                :class="optionClass(answer.id)">
                            <span class="flex items-center justify-center w-14 shrink-0 russo text-2xl border-r-4 border-main-dark" :class="letterClass(answer.id)">[[ 'ABCDEF'.charAt(i) ]]</span>
                            <span class="flex-1 min-w-0 px-4 py-4 fira-sans text-xl md:text-2xl font-bold break-words">[[ answer.answer ]]</span>
                        </button>
                    </div>

                    {{-- Typed answer --}}
                    <div v-else class="w-full max-w-2xl mx-auto flex flex-col gap-4">
                        <input v-model="typed" @keyup.enter="submitTyped()" :disabled="locked" type="text" placeholder="Type your answer" autocomplete="off"
                               class="w-full bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 px-4 text-center text-2xl text-main-dark font-bold placeholder:text-base placeholder:font-medium placeholder:text-gray-400 focus:outline-none fira-sans disabled:bg-main-light">
                        <button v-if="!locked" @click="submitTyped()" type="button" class="w-full bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-2xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Submit Answer</button>
                        <div v-if="view == 'reveal'" class="flex flex-row items-stretch border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]">
                            <span class="flex items-center bg-green-500 text-white px-3 russo text-xs uppercase tracking-widest shrink-0 border-r-4 border-main-dark">Correct</span>
                            <span class="flex-1 min-w-0 bg-white text-main-dark px-4 py-3 fira-sans font-bold text-xl break-words">[[ reveal.correct_answer_text ]]</span>
                        </div>
                    </div>

                    {{-- Status line --}}
                    <div class="text-center min-h-[3.5rem] flex items-center justify-center">
                        <span v-if="view == 'question' && locked" class="fira-sans text-main-dark/70 font-bold">Answer locked in &mdash; revealing in [[ Math.ceil(timeLeft) ]]&hellip;</span>
                        <span v-else-if="view == 'question'" class="fira-sans text-main-dark/60">Pick before the time runs out!</span>
                        <span v-else :key="'stamp' + index" class="solo-stamp inline-block border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-5 py-1.5 russo text-2xl md:text-3xl uppercase tracking-wider"
                              :class="reveal.correct ? 'bg-green-500 text-white' : (reveal.timedOut ? 'bg-pop text-main-dark' : 'bg-main-accent text-white')">
                            [[ reveal.correct ? 'Correct!' : (reveal.timedOut ? "Time's up!" : 'Wrong!') ]]
                        </span>
                    </div>
                </template>

                {{-- Results --}}
                <div v-if="view == 'results'" class="flex flex-col items-center gap-6">
                    <div class="text-center">
                        <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Final score</span>
                        <div class="solo-pop russo text-6xl md:text-7xl text-main-dark mt-1">[[ score ]]<span class="text-main-dark/40 text-4xl md:text-5xl"> / [[ total ]]</span></div>
                        <p class="fira-sans font-bold text-main-dark mt-2">[[ verdict ]]</p>
                    </div>
                    <div class="flex flex-row flex-wrap justify-center gap-3">
                        <span class="border-4 border-main-dark bg-pop px-4 py-1.5 russo text-sm uppercase tracking-wide">[[ accuracy ]]% correct</span>
                        <span class="border-4 border-main-dark bg-main-light px-4 py-1.5 russo text-sm uppercase tracking-wide">[[ avgTime ]]s avg answer</span>
                    </div>

                    {{-- Recap --}}
                    <div class="w-full border-4 border-main-dark">
                        <div v-for="(r, i) in recap" :key="i" class="flex flex-row items-start gap-3 px-4 py-3" :class="[i % 2 ? 'bg-white' : 'bg-main-light', i < recap.length - 1 ? 'border-b-2 border-main-dark/20' : '']">
                            <span class="shrink-0 w-7 h-7 flex items-center justify-center border-2 border-main-dark russo text-sm" :class="r.correct ? 'bg-green-500 text-white' : 'bg-main-accent text-white'">[[ r.correct ? '✓' : '✗' ]]</span>
                            <div class="flex-1 min-w-0">
                                <p class="fira-sans font-bold text-main-dark break-words">[[ i + 1 ]]. [[ r.question ]]</p>
                                <p class="fira-sans text-sm text-main-dark/70 break-words">
                                    <span v-if="!r.correct">You: <span class="font-bold">[[ r.given || 'no answer' ]]</span> &middot; </span>Answer: <span class="font-bold text-green-700">[[ r.answer ]]</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                        <form method="POST" action="/trivia/solo">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="category" value="{{ $settings['category'] }}">
                            <input type="hidden" name="difficulty" value="{{ $settings['difficulty'] }}">
                            <input type="hidden" name="question_count" value="{{ $settings['question_count'] }}">
                            <button type="submit" class="w-full sm:w-64 bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Play Again</button>
                        </form>
                        <a href="/trivia" class="sm:w-64 text-center bg-white text-main-dark border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Back to Quizzes</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;
        createApp({
            data() {
                return {
                    questions: @json($questions),
                    timeLimit: {{ (int) $timeLimit }},
                    answerUrl: '/trivia/solo/{{ $tmpTrivia->id }}/answer',
                    view: 'countdown',     // countdown | question | reveal | results
                    countdown: 3,
                    index: 0,
                    timeLeft: 0,
                    deadline: 0,
                    tick: null,
                    locked: false,         // answer sent for this question
                    picked: null,          // chosen option id
                    typed: '',
                    pending: null,         // the answer request in flight
                    reveal: {},            // server verdict for the current question
                    score: 0,
                    recap: [],
                    answerTimes: [],
                    shownAt: 0,
                };
            },
            delimiters: ['[[', ']]'],
            computed: {
                total() { return this.questions.length; },
                current() { return this.questions[this.index] || {}; },
                timerPct() { return Math.max(0, (this.timeLeft / this.timeLimit) * 100); },
                timerClass() {
                    const p = this.timerPct;
                    return p < 20 ? 'bg-main-accent' : (p < 35 ? 'bg-secondly-accent' : (p < 65 ? 'bg-pop' : 'bg-green-500'));
                },
                accuracy() { return this.total ? Math.round((this.score / this.total) * 100) : 0; },
                avgTime() {
                    if (!this.answerTimes.length) return '–';
                    return (this.answerTimes.reduce((a, b) => a + b, 0) / this.answerTimes.length).toFixed(1);
                },
                verdict() {
                    const a = this.accuracy;
                    return a === 100 ? 'Flawless! Every single one.' : a >= 80 ? 'Brilliant — quiz master material.'
                        : a >= 50 ? 'Solid round. Go again?' : a > 0 ? 'Tough one. Practice makes perfect!' : 'Ouch. The next one is yours!';
                },
            },
            methods: {
                post(body) {
                    return fetch(this.answerUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify(Object.assign({ order_nr: this.current.order_nr }, body)),
                    }).then(r => r.json()).then(res => (res && res.success) ? res.data : null).catch(() => null);
                },
                startCountdown() {
                    const step = () => {
                        if (this.countdown <= 1) { this.showQuestion(0); return; }
                        this.countdown--; setTimeout(step, 800);
                    };
                    setTimeout(step, 800);
                },
                showQuestion(i) {
                    this.index = i;
                    this.locked = false; this.picked = null; this.typed = ''; this.pending = null; this.reveal = {};
                    this.view = 'question';
                    this.shownAt = Date.now();
                    this.setDeadline(this.timeLimit);
                    clearInterval(this.tick);
                    this.tick = setInterval(() => {
                        this.timeLeft = Math.max(0, (this.deadline - Date.now()) / 1000);
                        if (this.timeLeft <= 0) { clearInterval(this.tick); this.revealAnswer(); }
                    }, 100);
                },
                setDeadline(seconds) {
                    this.deadline = Date.now() + seconds * 1000;
                    this.timeLeft = seconds;
                },
                // After answering, the reveal still comes when the clock runs out, but
                // there's no point waiting more than a moment when you play alone.
                lockIn(body) {
                    if (this.locked || this.view != 'question') return;
                    this.locked = true;
                    this.answerTimes.push((Date.now() - this.shownAt) / 1000);
                    this.pending = this.post(body);
                    if (this.timeLeft > 2) this.setDeadline(2);
                },
                pickOption(id) {
                    if (this.locked) return;
                    this.picked = id;
                    this.lockIn({ answer_id: id });
                },
                submitTyped() {
                    if (this.locked || !this.typed.trim()) return;
                    this.lockIn({ answer_text: this.typed.trim() });
                },
                async revealAnswer() {
                    const timedOut = !this.locked;
                    this.locked = true;
                    const data = await (this.pending || this.post({}));
                    const result = data || { correct: false, correct_answer_ids: [], correct_answer_text: '' };
                    this.reveal = Object.assign({ timedOut }, result);
                    this.score = data ? data.score : this.score;
                    const q = this.current;
                    const pickedAnswer = (q.answers || []).find(a => a.id === this.picked);
                    this.recap.push({
                        question: q.question,
                        correct: !!result.correct,
                        given: q.question_type == 'options' ? (pickedAnswer ? pickedAnswer.answer : '') : this.typed.trim(),
                        answer: result.correct_answer_text,
                    });
                    this.view = 'reveal';
                    setTimeout(() => {
                        if (this.index + 1 < this.total) this.showQuestion(this.index + 1);
                        else this.view = 'results';
                    }, 2600);
                },
                optionClass(id) {
                    const isCorrect = this.view == 'reveal' && (this.reveal.correct_answer_ids || []).includes(id);
                    const isPicked = this.picked === id;
                    if (isCorrect) return 'bg-green-500 text-white';
                    if (this.view == 'reveal' && isPicked) return 'bg-main-accent text-white';
                    if (isPicked) return 'bg-brand text-white';
                    if (this.locked || this.view == 'reveal') return 'bg-white text-main-dark opacity-50';
                    return 'bg-white text-main-dark hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]';
                },
                letterClass(id) {
                    const isCorrect = this.view == 'reveal' && (this.reveal.correct_answer_ids || []).includes(id);
                    return (isCorrect || this.picked === id) ? 'bg-main-dark text-white' : 'bg-pop text-main-dark';
                },
            },
            mounted() {
                if (this.total) this.startCountdown(); else this.view = 'results';
            },
        }).mount('#solo-app');
    </script>

@endsection
