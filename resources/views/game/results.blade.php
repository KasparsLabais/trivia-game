@extends('game-api::layout')
@section('page-bg', 'bg-pop')
@section('body')

    <style>
        .dotted-canvas {
            background-color: #FFB900;
            background-image: radial-gradient(#14323B 2.5px, transparent 2.6px);
            background-size: 26px 26px;
        }

        /* Reveal sequence (see script below). Classes are only added by JS, so
           without JS / with reduced motion everything is simply visible. */
        .reveal-hidden { visibility: hidden; }
        .row-in { animation: row-in 0.35s ease-out both; }
        @keyframes row-in {
            from { opacity: 0; transform: translateX(-24px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .spot-pop { animation: spot-pop 0.45s cubic-bezier(.2, 1.6, .4, 1) both; }
        @keyframes spot-pop {
            from { opacity: 0; transform: scale(0.6) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .drumroll { animation: drumroll 0.12s linear infinite; }
        @keyframes drumroll {
            0%, 100% { transform: translateX(0) rotate(0); }
            25% { transform: translateX(-3px) rotate(-2deg); }
            75% { transform: translateX(3px) rotate(2deg); }
        }
        .mystery-pulse { animation: mystery-pulse 1s ease-in-out infinite; }
        @keyframes mystery-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.45; }
        }
    </style>

    @php
        // Podium order: 2nd | 1st | 3rd on desktop; on mobile the winner comes first.
        $podium = [
            ['place' => 2, 'player' => $winners['second'] ?? [], 'label' => '2nd', 'colour' => 'bg-surface text-main-dark', 'height' => 'md:h-28', 'order' => 'order-2 md:order-1'],
            ['place' => 1, 'player' => $winners['winner'] ?? [], 'label' => '1st', 'colour' => 'bg-pop text-main-dark', 'height' => 'md:h-40', 'order' => 'order-1 md:order-2'],
            ['place' => 3, 'player' => $winners['third'] ?? [], 'label' => '3rd', 'colour' => 'bg-secondly-accent text-white', 'height' => 'md:h-20', 'order' => 'order-3'],
        ];
        $avatar = fn ($player) => empty($player['avatar']) ? '/images/default-avatar.jpg' : $player['avatar'];
    @endphp

    <div class="dotted-canvas min-h-screen flex justify-center items-start px-4 py-8 md:py-12">
        <div class="w-full max-w-3xl flex flex-col gap-8">

            {{-- Everyone outside the top three — shown first --}}
            @if(!empty($winners['winners']))
                <div class="w-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                    <div class="flex items-center gap-2.5 bg-brand border-b-4 border-main-dark px-5 py-3">
                        <span class="russo uppercase tracking-widest text-white text-xs md:text-sm">Happy To Participate</span>
                    </div>
                    <div class="flex flex-col">
                        @foreach($winners['winners'] as $runnerUp)
                            <div class="participant-row flex flex-row items-center gap-4 px-5 py-3 {{ $loop->last ? '' : 'border-b-4 border-main-dark' }} {{ $loop->odd ? 'bg-main-light' : 'bg-white' }}">
                                <span class="russo text-main-dark/50 text-lg w-8 shrink-0">#{{ $loop->iteration + 3 }}</span>
                                <img class="w-10 h-10 object-cover bg-white border-2 border-main-dark shrink-0" src="{{ $avatar($runnerUp) }}" alt="{{ $runnerUp['username'] }}">
                                <span class="flex-1 min-w-0 fira-sans font-bold text-main-dark break-words">{{ $runnerUp['username'] }}</span>
                                <span class="shrink-0 fira-sans font-bold text-main-dark">{{ $runnerUp['points'] }} <span class="text-main-dark/60 text-xs uppercase tracking-widest">pts</span></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Results window: podium, revealed 3rd -> 2nd -> 1st --}}
            <div id="podium-window" class="w-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">

                {{-- Title bar --}}
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm">Results</span>
                    <button id="skip-reveal" type="button" class="hidden ml-auto fira-sans text-xs uppercase tracking-widest text-white/60 hover:text-white">Skip &raquo;</button>
                </div>

                <div class="p-6 md:p-10 flex flex-col items-center gap-8">
                    <div class="text-center">
                        <span class="fira-sans text-xs uppercase tracking-widest text-main-dark/60">Final standings</span>
                        <h1 class="russo text-3xl md:text-4xl text-main-dark leading-tight break-words mt-1">{{ $gameInstance['title'] }}</h1>
                    </div>

                    @if(empty($winners['winner']))
                        <div class="w-full bg-main-light border-4 border-main-dark px-5 py-6 text-center">
                            <p class="fira-sans text-main-dark text-lg font-bold">No one scored in this game.</p>
                        </div>
                    @else
                        <div class="w-full flex flex-col md:flex-row md:items-end gap-6 md:gap-4">
                            @foreach($podium as $spot)
                                @if(!empty($spot['player']))
                                    <div class="podium-spot {{ $spot['order'] }} flex-1 min-w-0 flex flex-col items-center gap-3" data-place="{{ $spot['place'] }}">

                                        {{-- Placeholder shown until this place is revealed --}}
                                        <div class="spot-mystery hidden flex-col items-center gap-3">
                                            <div class="{{ $spot['place'] == 1 ? 'w-28 h-28' : 'w-20 h-20' }} bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] flex items-center justify-center">
                                                <span class="mystery-pulse russo text-main-dark/40 {{ $spot['place'] == 1 ? 'text-6xl' : 'text-4xl' }}">?</span>
                                            </div>
                                            <span class="russo text-main-dark/40 text-lg tracking-widest">???</span>
                                        </div>

                                        <div class="spot-player flex flex-col items-center gap-3 w-full">
                                            @if($spot['place'] == 1)
                                                <span class="russo text-main-accent text-xl uppercase tracking-widest">Winner!</span>
                                            @endif
                                            <img class="{{ $spot['place'] == 1 ? 'w-28 h-28' : 'w-20 h-20' }} object-cover bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]"
                                                 src="{{ $avatar($spot['player']) }}" alt="{{ $spot['player']['username'] }}">
                                            <div class="text-center w-full">
                                                <p class="fira-sans font-bold text-main-dark {{ $spot['place'] == 1 ? 'text-2xl' : 'text-lg' }} break-words">{{ $spot['player']['username'] }}</p>
                                                <span class="inline-block mt-1 bg-white text-main-dark border-2 border-main-dark px-2 py-0.5 russo text-xs uppercase tracking-wide">{{ $spot['player']['points'] }} Points</span>
                                            </div>
                                        </div>

                                        <div class="w-full h-14 {{ $spot['height'] }} {{ $spot['colour'] }} border-4 border-main-dark flex items-start justify-center pt-2">
                                            <span class="russo text-2xl md:text-3xl">{{ $spot['label'] }}</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div id="results-actions" class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/trivia"
                   class="sm:w-64 text-center bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Play Another</a>
                <a href="/"
                   class="sm:w-64 text-center bg-white text-main-dark border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Back Home</a>
            </div>

        </div>
    </div>

    <script>
        // Suspense reveal: participants slide in first, then the podium is
        // revealed 3rd -> 2nd -> (drum roll) -> 1st.
        (function () {
            if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }
            // ?instant=1: players already watched the reveal on the Display Screen.
            if (new URLSearchParams(window.location.search).has('instant')) {
                return;
            }

            const ROW_STAGGER = 150;   // ms between participant rows
            const ROWS_MAX = 2500;     // cap on total row stagger for big games
            const PAUSE = 1500;        // before 3rd place
            const BETWEEN = 2000;      // 3rd -> 2nd
            const DRUMROLL = 2800;     // 2nd -> 1st (1st spot shakes meanwhile)

            const rows = Array.from(document.querySelectorAll('.participant-row'));
            const spots = {};
            document.querySelectorAll('.podium-spot').forEach(el => { spots[el.dataset.place] = el; });
            const actions = document.getElementById('results-actions');
            const skip = document.getElementById('skip-reveal');
            const timers = [];

            const hideSpot = (spot) => {
                spot.querySelector('.spot-player').classList.add('hidden');
                spot.querySelector('.spot-mystery').classList.remove('hidden');
                spot.querySelector('.spot-mystery').classList.add('flex');
            };
            const showSpot = (spot) => {
                if (!spot) { return; }
                spot.classList.remove('drumroll');
                spot.querySelector('.spot-mystery').classList.add('hidden');
                spot.querySelector('.spot-mystery').classList.remove('flex');
                const player = spot.querySelector('.spot-player');
                player.classList.remove('hidden');
                player.classList.add('spot-pop');
                // Only scroll when the spot is off-screen (mainly mobile, where
                // the podium is stacked).
                const rect = spot.getBoundingClientRect();
                if (rect.top < 0 || rect.bottom > window.innerHeight) {
                    spot.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            };
            const finish = () => {
                timers.forEach(clearTimeout);
                rows.forEach(r => r.classList.remove('reveal-hidden'));
                ['3', '2', '1'].forEach(p => { if (spots[p] && !spots[p].querySelector('.spot-player.spot-pop')) { showSpot(spots[p]); } });
                actions.classList.remove('reveal-hidden');
                actions.classList.add('row-in');
                skip.classList.add('hidden');
            };
            const at = (ms, fn) => timers.push(setTimeout(fn, ms));

            // Initial state: hide everything that gets revealed.
            rows.forEach(r => r.classList.add('reveal-hidden'));
            Object.values(spots).forEach(hideSpot);
            actions.classList.add('reveal-hidden');
            if (Object.keys(spots).length) {
                skip.classList.remove('hidden');
                skip.addEventListener('click', finish);
            }

            // 1. Participants, one by one.
            const step = rows.length ? Math.min(ROW_STAGGER, ROWS_MAX / rows.length) : 0;
            rows.forEach((row, i) => at(i * step, () => {
                row.classList.remove('reveal-hidden');
                row.classList.add('row-in');
            }));

            // 2. Podium: 3rd, 2nd, drum roll, 1st.
            let t = rows.length ? rows.length * step + PAUSE : 600;
            at(t - 600, () => {
                const podium = document.getElementById('podium-window');
                if (podium.getBoundingClientRect().bottom > window.innerHeight) {
                    podium.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
            if (spots['3']) { at(t, () => showSpot(spots['3'])); t += BETWEEN; }
            if (spots['2']) { at(t, () => showSpot(spots['2'])); t += 600; }
            if (spots['1']) {
                at(t, () => spots['1'].classList.add('drumroll'));
                t += DRUMROLL;
                at(t, () => showSpot(spots['1']));
            }
            at(t + 800, finish);
        })();
    </script>

@endsection
