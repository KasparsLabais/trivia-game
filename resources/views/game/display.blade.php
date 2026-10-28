<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $trivia->title ?? 'Trivia' }} — Display</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Honk&family=Russo+One&family=Space+Grotesk:wght@400;500;600;700&family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    <script src="https://cdn.socket.io/4.5.0/socket.io.min.js" integrity="sha384-7EyYLQZgWBi67fBtVxw60/OWl1kjsfrPFcaU0pp0nAh+i8FD068QogUvg85Ewy1k" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css'])

    <style>
        body { font-family: 'Space Mono', ui-monospace, monospace; }
        .raleway { font-family: 'Space Mono', ui-monospace, monospace; }
        .fira-sans { font-family: 'Space Grotesk', ui-sans-serif, sans-serif; }
        .josefin-sans { font-family: 'Space Grotesk', ui-sans-serif, sans-serif; }
        .honk { font-family: 'Honk', system-ui, cursive; font-weight: 400; }
        .russo { font-family: 'Russo One', ui-sans-serif, sans-serif; }
        html, body { height: 100%; }
        /* Results reveal */
        .reveal-hidden { visibility: hidden; }
        .chip-in { animation: chip-in 0.35s ease-out both; }
        @keyframes chip-in { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .spot-pop { animation: spot-pop 0.5s cubic-bezier(.2, 1.6, .4, 1) both; }
        @keyframes spot-pop { from { opacity: 0; transform: scale(0.5) translateY(30px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .drumroll { animation: drumroll 0.12s linear infinite; }
        @keyframes drumroll { 0%, 100% { transform: translateX(0) rotate(0); } 25% { transform: translateX(-4px) rotate(-2deg); } 75% { transform: translateX(4px) rotate(2deg); } }
        .mystery-pulse { animation: mystery-pulse 1s ease-in-out infinite; }
        @keyframes mystery-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
        .display-canvas {
            background-color: #FFB900;
            background-image: radial-gradient(#14323B 2px, transparent 2.1px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="display-canvas text-main-dark h-screen overflow-hidden p-[20px] lg:p-[30px]">
    <div class="flex flex-row h-full gap-[20px] lg:gap-[30px]">

        {{-- ================= QUESTION WINDOW ================= --}}
        <div id="question-window" class="flex flex-col w-2/3 h-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
            {{-- Window title bar: traffic lights + trivia title + PIN --}}
            <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3 shrink-0">
                <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                <h1 class="russo text-white text-xl lg:text-2xl tracking-wide ml-2 truncate">{{ $trivia->title ?? 'Trivia' }}</h1>
                <div class="ml-auto flex items-baseline gap-2 shrink-0">
                    <span class="fira-sans text-xs uppercase tracking-widest text-white/60">PIN</span>
                    <span class="russo text-pop text-2xl lg:text-3xl tracking-widest">{{ $gameInstance['pin'] }}</span>
                </div>
            </div>

            {{-- Timer bar --}}
            <div id="timer-wrap" class="w-full bg-main-light border-b-4 border-main-dark shrink-0" style="display:none;">
                <div class="flex flex-row items-center px-6 py-3 gap-4">
                    <div class="flex-1 h-4 bg-white border-2 border-main-dark overflow-hidden">
                        <div id="timer-bar" class="h-full bg-green-500" style="width:100%; transition: width 1s linear;"></div>
                    </div>
                    <div id="timer-count" class="russo text-2xl w-24 text-center text-main-dark">–</div>
                </div>
            </div>

            {{-- Active content --}}
            <div class="flex-1 flex flex-col justify-center items-center px-8 lg:px-12 py-6 overflow-y-auto">

                <div id="stage-waiting" class="text-center">
                    <div class="text-3xl lg:text-4xl russo text-main-dark/40">Waiting for the next question…</div>
                </div>

                <div id="stage-question" class="w-full max-w-5xl" style="display:none;">
                    <h2 id="question-text" class="russo text-main-dark text-4xl lg:text-5xl text-center leading-tight mb-8"></h2>
                    <div id="answer-grid" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>
                    <div id="answer-text-wrap" class="text-center mt-6" style="display:none;">
                        <div class="fira-sans uppercase tracking-widest text-main-dark/50 text-lg">Correct answer</div>
                        <div id="answer-text" class="russo text-4xl text-green-600"></div>
                    </div>
                </div>

                <div id="stage-winner" class="text-center" style="display:none;">
                    <div class="fira-sans uppercase tracking-widest text-main-dark/50 text-2xl mb-3">Question Winner</div>
                    <div id="winner-name" class="russo text-main-accent text-6xl lg:text-7xl"></div>
                </div>

                {{-- Final standings reveal (master: "Reveal Results on Display") --}}
                <div id="stage-results" class="w-full h-full" style="display:none;">
                    <div class="w-full h-full flex flex-col items-center gap-6">
                        <div class="fira-sans uppercase tracking-widest text-main-dark/50 text-xl">Final Standings</div>

                        <div id="results-others-wrap" class="w-full max-w-6xl" style="display:none;">
                            <div class="fira-sans uppercase tracking-widest text-main-dark/50 text-sm text-center mb-2">Happy To Participate</div>
                            <div id="results-others" class="flex flex-row flex-wrap justify-center gap-2 max-h-48 overflow-hidden"></div>
                        </div>

                        <div id="results-podium" class="flex-1 w-full max-w-5xl flex flex-row items-end justify-center gap-6 pb-2"></div>
                    </div>
                </div>

                <div id="stage-gameover" class="text-center" style="display:none;">
                    <div class="russo text-main-accent text-6xl lg:text-7xl">GAME OVER</div>
                    <div class="fira-sans text-main-dark/50 text-2xl mt-3">Thanks for playing!</div>
                </div>

            </div>
        </div>

        {{-- ================= LEADERBOARD WINDOW ================= --}}
        <div id="leaderboard-window" class="flex flex-col w-1/3 h-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
            {{-- Leaderboard --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="px-5 py-3 bg-brand border-b-4 border-main-dark shrink-0">
                    <h2 class="russo text-white text-xl lg:text-2xl tracking-wide">Leaderboard</h2>
                </div>
                <div id="leaderboard-list" class="flex-1 overflow-y-auto px-3 py-3 flex flex-col gap-2">
                    <p id="leaderboard-empty" class="fira-sans text-main-dark/50 text-center py-6">No scores yet.</p>
                </div>
            </div>

            {{-- Active players --}}
            <div class="border-t-4 border-main-dark flex flex-col shrink-0" style="max-height:38%;">
                <div class="px-5 py-3 bg-main-dark flex flex-row justify-between items-center shrink-0">
                    <h2 class="russo text-white text-lg tracking-wide">Active Players</h2>
                    <span id="players-count" class="fira-sans text-sm font-bold text-pop">0</span>
                </div>
                <div id="players-list" class="flex-1 overflow-y-auto px-3 py-3 flex flex-row flex-wrap gap-2 content-start">
                    <p id="players-empty" class="fira-sans text-main-dark/50 py-4">No players have joined yet.</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        var GAME_TOKEN = @json($token);
        var SOCKET_URL = @json(config('game-api.socket_url'));
        var LEADERBOARD_URL = '/trv/trivia/' + GAME_TOKEN + '/leaderboard';
        var seededQuestion = @json($currentQuestion);
        var seededPlayers = @json($players);
        var LETTERS = ['A','B','C','D','E','F'];

        var el = function (id) { return document.getElementById(id); };
        var stages = ['stage-waiting','stage-question','stage-winner','stage-results','stage-gameover'];
        function showStage(id) {
            // Host went back to the game after revealing results: restore the layout.
            if (resultsShown && id !== 'stage-results' && id !== 'stage-gameover') {
                resultsShown = false;
                resultsTimers.forEach(clearTimeout);
                el('leaderboard-window').style.display = '';
                el('question-window').classList.remove('w-full');
                el('question-window').classList.add('w-2/3');
            }
            stages.forEach(function (s) { el(s).style.display = (s === id ? 'block' : 'none'); });
        }

        // ---- Question / answers ----
        function renderQuestion(q) {
            if (!q) return;
            el('question-text').textContent = q.question || '';
            var grid = el('answer-grid');
            grid.innerHTML = '';
            el('answer-text-wrap').style.display = 'none';
            (q.answers || []).forEach(function (a, i) {
                var card = document.createElement('div');
                card.setAttribute('data-answer-id', a.id);
                card.className = 'answer-card flex flex-row items-center gap-3 px-5 py-4 bg-main-light border-4 border-main-dark';
                card.innerHTML = '<span class="russo text-2xl text-main-dark/50">' + (LETTERS[i] || '') + '</span>' +
                    '<span class="fira-sans text-2xl font-semibold">' + (a.answer || '') + '</span>';
                grid.appendChild(card);
            });
            showStage('stage-question');
        }

        // open_input: show everyone's submissions in the answer area.
        function renderSubmissions(subs) {
            showStage('stage-question');
            var grid = el('answer-grid');
            grid.innerHTML = '';
            el('answer-text-wrap').style.display = 'none';
            (subs || []).forEach(function (s) {
                var card = document.createElement('div');
                card.className = 'flex flex-row items-center gap-3 px-5 py-4 bg-main-light border-4 border-main-dark';
                var name = document.createElement('span');
                name.className = 'fira-sans text-xl text-brand font-bold whitespace-nowrap';
                name.textContent = s.username || 'Player';
                card.appendChild(name);
                if (s.is_image) {
                    var img = document.createElement('img');
                    img.src = s.value; img.className = 'h-24 border-2 border-main-dark';
                    card.appendChild(img);
                } else {
                    var val = document.createElement('span');
                    val.className = 'raleway text-2xl'; val.textContent = s.value || '';
                    card.appendChild(val);
                }
                grid.appendChild(card);
            });
        }

        function showCorrect(payload) {
            hideTimer();
            if (payload && payload.answer_id) {
                var card = document.querySelector('.answer-card[data-answer-id="' + payload.answer_id + '"]');
                if (card) { card.classList.remove('bg-main-light'); card.classList.add('bg-green-500'); }
            } else if (payload && payload.answer_text) {
                el('answer-text').textContent = payload.answer_text;
                el('answer-text-wrap').style.display = 'block';
            }
        }

        function showWinner(name) {
            hideTimer();
            el('winner-name').textContent = name || '—';
            showStage('stage-winner');
        }

        // Once the final standings are on screen, game over keeps them there.
        function showGameOver() { hideTimer(); if (!resultsShown) { showStage('stage-gameover'); } }

        // ---- Final standings reveal ----
        // Everyone outside the top three first, then 3rd -> 2nd -> drum roll -> 1st.
        // The leaderboard column is hidden so it can't spoil the result.
        var STANDINGS_URL = '/trv/trivia/' + GAME_TOKEN + '/standings';
        var resultsShown = false;
        var resultsTimers = [];
        function revealResults() {
            fetch(STANDINGS_URL).then(function (r) { return r.json(); }).then(function (res) {
                if (res && res.success) { playResults(res.data); }
            }).catch(function () {});
        }
        function playResults(data) {
            resultsTimers.forEach(clearTimeout);
            resultsTimers = [];
            var at = function (ms, fn) { resultsTimers.push(setTimeout(fn, ms)); };

            resultsShown = true;
            hideTimer();
            el('leaderboard-window').style.display = 'none';
            el('question-window').classList.remove('w-2/3');
            el('question-window').classList.add('w-full');
            showStage('stage-results');

            // Everyone else, as chips.
            var others = el('results-others');
            others.innerHTML = '';
            el('results-others-wrap').style.display = (data.others || []).length ? 'block' : 'none';
            var chips = (data.others || []).map(function (p, i) {
                var chip = document.createElement('div');
                chip.className = 'reveal-hidden flex flex-row items-center gap-2 pl-1 pr-3 py-1 bg-main-light border-2 border-main-dark';
                var rank = document.createElement('span');
                rank.className = 'russo text-main-dark/50 text-sm pl-1';
                rank.textContent = '#' + (i + 4);
                var img = document.createElement('img');
                img.src = p.avatar; img.className = 'w-8 h-8 object-cover border-2 border-main-dark';
                img.onerror = function () { this.src = '/images/default-avatar.jpg'; };
                var name = document.createElement('span');
                name.className = 'fira-sans text-base font-bold';
                name.textContent = p.username;
                var pts = document.createElement('span');
                pts.className = 'fira-sans text-sm text-main-dark/60';
                pts.textContent = p.points + ' pts';
                chip.append(rank, img, name, pts);
                others.appendChild(chip);
                return chip;
            });

            // Podium: 2nd | 1st | 3rd, each starting as a "?" placeholder.
            var podium = el('results-podium');
            podium.innerHTML = '';
            var spots = {};
            [
                { place: 2, player: data.second, label: '2nd', colour: 'bg-surface text-main-dark', height: 'h-32', size: 'w-28 h-28' },
                { place: 1, player: data.first, label: '1st', colour: 'bg-pop text-main-dark', height: 'h-48', size: 'w-40 h-40' },
                { place: 3, player: data.third, label: '3rd', colour: 'bg-secondly-accent text-white', height: 'h-20', size: 'w-24 h-24' }
            ].forEach(function (s) {
                if (!s.player) { return; }
                var spot = document.createElement('div');
                spot.className = 'flex-1 max-w-xs flex flex-col items-center gap-3';

                var mystery = document.createElement('div');
                mystery.className = 'flex flex-col items-center gap-3';
                mystery.innerHTML = '<div class="' + s.size + ' bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] flex items-center justify-center">' +
                    '<span class="mystery-pulse russo text-main-dark/40 text-6xl">?</span></div>' +
                    '<span class="russo text-main-dark/40 text-2xl tracking-widest">???</span>';

                var player = document.createElement('div');
                player.className = 'flex flex-col items-center gap-3 w-full';
                player.style.display = 'none';
                if (s.place === 1) {
                    var crown = document.createElement('span');
                    crown.className = 'russo text-main-accent text-3xl uppercase tracking-widest';
                    crown.textContent = 'Winner!';
                    player.appendChild(crown);
                }
                var img = document.createElement('img');
                img.src = s.player.avatar;
                img.className = s.size + ' object-cover bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]';
                img.onerror = function () { this.src = '/images/default-avatar.jpg'; };
                var name = document.createElement('div');
                name.className = 'fira-sans font-bold text-main-dark text-center break-words w-full ' + (s.place === 1 ? 'text-4xl' : 'text-2xl');
                name.textContent = s.player.username;
                var pts = document.createElement('span');
                pts.className = 'bg-white text-main-dark border-2 border-main-dark px-3 py-1 russo text-base uppercase tracking-wide';
                pts.textContent = s.player.points + ' Points';
                player.append(img, name, pts);

                var block = document.createElement('div');
                block.className = 'w-full ' + s.height + ' ' + s.colour + ' border-4 border-main-dark flex items-start justify-center pt-3';
                block.innerHTML = '<span class="russo text-4xl">' + s.label + '</span>';

                spot.append(mystery, player, block);
                podium.appendChild(spot);
                spots[s.place] = { spot: spot, mystery: mystery, player: player };
            });

            var show = function (place) {
                var s = spots[place];
                if (!s) { return; }
                s.spot.classList.remove('drumroll');
                s.mystery.style.display = 'none';
                s.player.style.display = 'flex';
                s.player.classList.add('spot-pop');
            };

            if (!data.first) {
                podium.innerHTML = '<div class="russo text-3xl text-main-dark/40 self-center">No one scored in this game.</div>';
                return;
            }

            // Timeline (ms) — a little slower than on phones, it's the main event.
            var step = chips.length ? Math.min(180, 3000 / chips.length) : 0;
            chips.forEach(function (chip, i) {
                at(i * step, function () { chip.classList.remove('reveal-hidden'); chip.classList.add('chip-in'); });
            });
            var t = chips.length ? chips.length * step + 2000 : 1500;
            if (spots[3]) { at(t, function () { show(3); }); t += 2500; }
            if (spots[2]) { at(t, function () { show(2); }); t += 800; }
            at(t, function () { spots[1].spot.classList.add('drumroll'); });
            at(t + 3500, function () { show(1); });
        }

        // ---- Timer ----
        var timerHandle = null, timerTotal = 0, timeLeft = 0;
        function startTimer(total, remaining) {
            clearTimeout(timerHandle);
            timerTotal = total;
            timeLeft = (typeof remaining === 'undefined') ? total : remaining;
            el('timer-wrap').style.display = 'block';
            tick();
        }
        function tick() {
            clearTimeout(timerHandle);
            var left = Math.max(0, timeLeft);
            el('timer-count').textContent = left > 0 ? left : "Time's up";
            var pct = timerTotal > 0 ? Math.max(0, (left / timerTotal) * 100) : 0;
            var bar = el('timer-bar');
            bar.style.width = pct + '%';
            bar.className = 'h-3 rounded ' + (pct < 25 ? 'bg-rose-500' : (pct < 55 ? 'bg-amber-400' : 'bg-green-400'));
            if (left <= 0) return;
            timerHandle = setTimeout(function () { timeLeft = timeLeft - 1; tick(); }, 1000);
        }
        function hideTimer() { clearTimeout(timerHandle); el('timer-wrap').style.display = 'none'; }

        // ---- Leaderboard ----
        function fetchLeaderboard() {
            fetch(LEADERBOARD_URL).then(function (r) { return r.json(); }).then(function (res) {
                if (res && res.success) renderLeaderboard(res.data || []);
            }).catch(function () {});
        }
        function renderLeaderboard(players) {
            var list = el('leaderboard-list');
            list.querySelectorAll('[data-lb]').forEach(function (n) { n.remove(); });
            el('leaderboard-empty').style.display = players.length ? 'none' : 'block';
            players.forEach(function (p, i) {
                var row = document.createElement('div');
                row.setAttribute('data-lb', '1');
                var medal = i === 0 ? 'text-yellow-500' : (i === 1 ? 'text-slate-500' : (i === 2 ? 'text-amber-700' : 'text-main-dark/40'));
                row.className = 'flex flex-row items-center justify-between px-4 py-2 bg-main-light border-2 border-main-dark';
                row.innerHTML =
                    '<div class="flex flex-row items-center gap-3">' +
                        '<span class="russo text-2xl ' + medal + ' w-8 text-center">' + (i + 1) + '</span>' +
                        '<span class="fira-sans text-xl font-semibold truncate">' + (p.username || '—') + '</span>' +
                    '</div>' +
                    '<span class="russo text-xl text-main-accent">' + (p.points || 0) + '</span>';
                list.appendChild(row);
            });
        }

        // ---- Active players ----
        var playersMap = {};
        function addPlayer(p) {
            if (!p || !p.username) return;
            if (playersMap[p.username]) return;
            playersMap[p.username] = { username: p.username, avatar: p.avatar || '/images/default-avatar.jpg' };
            renderPlayers();
        }
        function renderPlayers() {
            var list = el('players-list');
            list.querySelectorAll('[data-player]').forEach(function (n) { n.remove(); });
            var keys = Object.keys(playersMap);
            el('players-empty').style.display = keys.length ? 'none' : 'block';
            el('players-count').textContent = keys.length;
            keys.forEach(function (k) {
                var p = playersMap[k];
                var chip = document.createElement('div');
                chip.setAttribute('data-player', '1');
                chip.className = 'flex flex-row items-center gap-2 pl-1 pr-3 py-1 bg-main-light border-2 border-main-dark';
                chip.innerHTML =
                    '<img src="' + p.avatar + '" class="w-8 h-8 rounded-full border-2 border-main-dark" onerror="this.src=\'/images/default-avatar.jpg\'">' +
                    '<span class="fira-sans text-sm font-semibold">' + p.username + '</span>';
                list.appendChild(chip);
            });
        }

        // ---- Init ----
        (seededPlayers || []).forEach(function (p) { addPlayer(p); });
        if (seededQuestion) { renderQuestion(seededQuestion); }
        fetchLeaderboard();

        var socket = io(SOCKET_URL);
        socket.on('connect', function () {
            socket.emit('spectateRoom', { gameToken: GAME_TOKEN });
            socket.emit('getQuestionTimer', { gameToken: GAME_TOKEN }, function (timer) {
                if (timer && timer.active && timer.questionData) {
                    renderQuestion(timer.questionData);
                    if (timer.expired) { startTimer(timer.timeLimit, 0); }
                    else { startTimer(timer.timeLimit, timer.timeLeft); }
                }
            });
        });

        socket.on('notifyRoom', function (data) {
            var action = data && data.data ? data.data.action : null;
            var payload = data && data.data ? data.data.payload : {};
            switch (action) {
                case 'startQuestion': hideTimer(); renderQuestion(payload); break;
                case 'revealAnswers': renderQuestion(payload); break;
                case 'revealSubmissions': renderSubmissions(payload.submissions); break;
                case 'startTimer': renderQuestion(payload.questionData); startTimer(payload.timeLimit, payload.timeLimit); break;
                case 'showCorrectAnswer': showCorrect(payload); break;
                case 'showWinningTeam': showWinner(payload.winnerName); break;
                case 'updatePoints': fetchLeaderboard(); break;
                case 'revealResultsOnDisplay': revealResults(); break;
                case 'gameOverEvent': showGameOver(); break;
                case 'gameClosed': showGameOver(); break;
            }
        });

        socket.on('playerJoined', function (data) {
            if (data && data.player) addPlayer(data.player);
            fetchLeaderboard();
        });
    </script>
</body>
</html>
