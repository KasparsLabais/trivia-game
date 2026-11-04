@extends('game-api::layout')
@section('body')

    @php
        $labelClass  = 'fira-sans text-xs uppercase tracking-widest text-main-dark/60 mb-1.5';
        $selectClass = 'w-full appearance-none bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] pl-3 pr-10 py-2.5 fira-sans font-bold text-main-dark focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] cursor-pointer transition-all';
        $pressBtn = 'fira-sans font-black uppercase tracking-wider border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-5 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]';
        $isActive = $season->status === 'active';
    @endphp

    <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-6xl mx-auto flex flex-col gap-8">

            <a href="/trv/seasons" class="self-start fira-sans font-bold uppercase text-xs tracking-wide text-main-dark/70 hover:text-main-dark">&larr; All seasons</a>

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
                <div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="relative self-start inline-block">
                            <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                            <span class="relative block russo text-main-dark text-lg md:text-2xl tracking-widest uppercase px-4 py-1.5">{{ $season->name }}</span>
                        </h1>
                        <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide {{ $season->status == 'completed' ? 'bg-main-dark text-white' : 'bg-green-500 text-white' }}">{{ $season->status }}</span>
                    </div>
                    @if($season->description)
                        <p class="fira-sans text-main-dark/60 mt-3 max-w-xl">{{ $season->description }}</p>
                    @endif
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-2">
                        @if($season->prize)
                            <p class="fira-sans text-main-dark/80 flex items-center gap-2">
                                <span class="inline-block border-2 border-main-dark bg-pop px-2 py-0.5 russo text-[10px] uppercase tracking-wide">Prize</span>
                                {{ $season->prize }}
                            </p>
                        @endif
                        @if($season->coin_pot > 0)
                            <p class="fira-sans text-main-dark/80 flex items-center gap-2">
                                <span class="inline-block border-2 border-main-dark bg-brand text-white px-2 py-0.5 russo text-[10px] uppercase tracking-wide">Coin pot</span>
                                🪙 {{ number_format($season->coin_pot) }}
                            </p>
                        @endif
                    </div>
                </div>
                @if($isActive)
                    <form action="/trv/seasons/{{ $season->id }}/complete" method="POST" onsubmit="return confirm('End this season and lock in the overall winner? Games already played still count, but you won\'t be able to launch more.');">
                        {{ csrf_field() }}
                        <button type="submit" class="{{ $pressBtn }} bg-main-dark text-white">End Season</button>
                    </form>
                @endif
            </div>

            {{-- Winner banner (completed) --}}
            @if($season->status == 'completed' && $season->winner_name)
                <div class="bg-pop border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-6 py-5 flex items-center gap-4">
                    <span class="text-4xl">🏆</span>
                    <div>
                        <div class="fira-sans text-xs uppercase tracking-widest text-main-dark/70">Season champion</div>
                        <div class="russo text-2xl text-main-dark leading-tight">{{ $season->winner_name }}</div>
                        @if($season->prize)<div class="fira-sans text-sm text-main-dark/80 mt-0.5">Wins: {{ $season->prize }}</div>@endif
                    </div>
                </div>
            @endif

            {{-- Coin pot payout (completed) --}}
            @if($season->status == 'completed' && $season->coin_pot > 0 && count($coinPodium))
                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5">
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="russo text-main-dark text-sm uppercase tracking-widest">Coin pot paid out</span>
                        <span class="russo text-main-dark text-lg leading-none">🪙 {{ number_format($season->coin_pot) }}</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        @foreach($coinPodium as $slot)
                            <div class="flex items-center gap-3 border-4 border-main-dark px-3 py-2 {{ $slot['rank'] == 1 ? 'bg-pop' : 'bg-white' }}">
                                <span class="russo text-lg w-6 shrink-0 text-center {{ $slot['rank'] == 1 ? 'text-main-dark' : 'text-main-dark/40' }}">{{ $slot['rank'] }}</span>
                                <div class="w-8 h-8 shrink-0 bg-white border-4 overflow-hidden {{ $slot['border'] ? '' : 'border-main-dark' }}" @if($slot['border']) style="border-color: {{ $slot['border'] }};" @endif>
                                    <img src="{{ $slot['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                                </div>
                                <span class="fira-sans font-bold text-main-dark flex-1 min-w-0 truncate">{{ $slot['username'] }}</span>
                                <span class="russo text-main-dark shrink-0">🪙 {{ number_format($slot['amount']) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Share link --}}
            <div class="bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <span class="russo text-[11px] uppercase tracking-widest text-main-dark/60 shrink-0">Live scoreboard</span>
                <input id="share-url" type="text" readonly value="{{ $publicUrl }}" class="flex-1 min-w-0 bg-main-light border-2 border-main-dark px-3 py-2 fira-sans text-sm text-main-dark">
                <div class="flex gap-2 shrink-0">
                    <button type="button" onclick="copyShare()" class="fira-sans font-black uppercase text-xs tracking-wide bg-brand text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]"><span id="copy-label">Copy</span></button>
                    <a href="{{ $publicUrl }}" target="_blank" class="fira-sans font-black uppercase text-xs tracking-wide bg-white text-main-dark border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Open</a>
                </div>
            </div>

            {{-- Prize & coin pot editor (active) --}}
            @if($isActive)
                <div class="bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] p-4">
                    <form action="/trv/seasons/{{ $season->id }}/pot" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-3">
                        {{ csrf_field() }}
                        <div class="flex flex-col flex-1">
                            <label class="{{ $labelClass }}" for="prize">Prize for the winner</label>
                            <input class="w-full bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-2 fira-sans text-main-dark placeholder:text-main-dark/40 focus:outline-none" type="text" name="prize" id="prize" value="{{ $season->prize }}" placeholder="£50 bar tab">
                        </div>
                        <div class="flex flex-col sm:w-44">
                            <label class="{{ $labelClass }}" for="coin_pot">Coin pot 🪙</label>
                            <input class="w-full bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-2 fira-sans text-main-dark focus:outline-none" type="number" name="coin_pot" id="coin_pot" min="0" step="10" value="{{ $season->coin_pot }}">
                        </div>
                        <button type="submit" class="shrink-0 fira-sans font-black uppercase text-xs tracking-wide bg-brand text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Save</button>
                    </form>
                    <p class="fira-sans text-[11px] text-main-dark/50 mt-2">The coin pot is split 50/30/20 across the top three registered players when you end the season.</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Line-up manager --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Quiz Line-up</span>
                    </div>
                    <div class="p-5 flex flex-col gap-4">
                        @if($lineup->count())
                            <div class="flex flex-col gap-3">
                                @foreach($lineup as $i => $st)
                                    <div class="flex items-center gap-3 border-4 border-main-dark bg-main-light px-3 py-2.5">
                                        <span class="russo text-main-dark/40 text-lg w-6 shrink-0">{{ $i + 1 }}</span>
                                        <span class="fira-sans font-bold text-main-dark flex-1 min-w-0 truncate">{{ $st->trivia->title }}</span>
                                        @if($isActive)
                                            <form action="/trv/seasons/{{ $season->id }}/launch" method="POST" class="shrink-0">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="trivia_id" value="{{ $st->trivia_id }}">
                                                <button type="submit" class="fira-sans font-black uppercase text-xs tracking-wide bg-green-500 text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-1.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Launch</button>
                                            </form>
                                            <form action="/trv/seasons/{{ $season->id }}/trivia/{{ $st->trivia_id }}" method="POST" class="shrink-0" onsubmit="return confirm('Remove this quiz from the line-up?');">
                                                {{ csrf_field() }} @method('DELETE')
                                                <button type="submit" class="fira-sans font-black uppercase text-xs tracking-wide bg-white text-main-accent border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-1.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">&times;</button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="fira-sans text-main-dark/50 text-center py-4">No quizzes in the line-up yet.</p>
                        @endif

                        @if($isActive)
                            @if($availableTrivias->count())
                                <form action="/trv/seasons/{{ $season->id }}/trivia" method="POST" class="flex flex-col gap-3 border-t-4 border-main-dark pt-4">
                                    {{ csrf_field() }}
                                    <label class="{{ $labelClass }}" for="add-trivia">Add one of your quizzes</label>
                                    <div class="flex gap-2">
                                        <div class="relative flex-1">
                                            <select name="trivia_id" id="add-trivia" class="{{ $selectClass }}" required>
                                                @foreach($availableTrivias as $trv)
                                                    <option value="{{ $trv->id }}">{{ $trv->title }}</option>
                                                @endforeach
                                            </select>
                                            <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                            </span>
                                        </div>
                                        <button type="submit" class="shrink-0 fira-sans font-black uppercase text-xs tracking-wide bg-main-accent text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Add</button>
                                    </div>
                                </form>
                            @else
                                <p class="fira-sans text-xs text-main-dark/50 border-t-4 border-main-dark pt-4">All your quizzes are in the line-up. <a href="/trv/management" class="underline font-bold text-main-accent">Create another</a> to add more.</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Season leaderboard --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-brand px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Season Leaderboard</span>
                    </div>
                    <div class="p-5">
                        @if(count($leaderboard))
                            <div class="flex flex-col gap-2.5">
                                @foreach($leaderboard as $i => $row)
                                    <div class="flex items-center gap-3 border-4 border-main-dark px-3 py-2 {{ $i == 0 ? 'bg-pop' : 'bg-white' }}">
                                        <span class="russo text-lg w-7 shrink-0 text-center {{ $i == 0 ? 'text-main-dark' : 'text-main-dark/40' }}">{{ $i + 1 }}</span>
                                        <div class="w-9 h-9 shrink-0 bg-white border-4 overflow-hidden {{ $row['border'] ? '' : 'border-main-dark' }}" @if($row['border']) style="border-color: {{ $row['border'] }};" @endif>
                                            <img src="{{ $row['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="fira-sans font-bold text-main-dark truncate">{{ $row['username'] }}@if($row['is_guest'])<span class="ml-1 fira-sans text-[10px] uppercase tracking-wide text-main-dark/40">guest</span>@endif</div>
                                            <div class="fira-sans text-[11px] text-main-dark/50">{{ $row['games_played'] }} {{ $row['games_played'] == 1 ? 'game' : 'games' }}</div>
                                        </div>
                                        <span class="russo text-xl text-main-dark shrink-0">{{ $row['points'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="fira-sans text-main-dark/50 text-center py-6">No scores yet. Launch a quiz from the line-up to get the season rolling.</p>
                        @endif

                        @if($isActive && $season->coin_pot > 0 && count($coinPodium))
                            <div class="border-t-4 border-main-dark mt-4 pt-4">
                                <div class="flex items-center justify-between gap-2 mb-2">
                                    <span class="fira-sans text-[11px] uppercase tracking-widest text-main-dark/60">Coin pot (projected)</span>
                                    <span class="russo text-main-dark leading-none">🪙 {{ number_format($season->coin_pot) }}</span>
                                </div>
                                <div class="flex flex-col gap-1">
                                    @foreach($coinPodium as $slot)
                                        <div class="flex items-center gap-2 fira-sans text-sm text-main-dark">
                                            <span class="russo text-main-dark/40 w-4 shrink-0">{{ $slot['rank'] }}</span>
                                            <span class="flex-1 min-w-0 truncate">{{ $slot['username'] }}</span>
                                            <span class="russo shrink-0">🪙 {{ number_format($slot['amount']) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <p class="fira-sans text-[11px] text-main-dark/50 mt-2">Paid to the top three registered players when the season ends.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Games played --}}
            <div class="flex flex-col gap-4">
                <h2 class="russo text-main-dark text-base uppercase tracking-widest">Games Played</h2>
                @if(count($events))
                    <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-main-dark text-white russo text-[11px] uppercase tracking-wider">
                                    <th class="px-4 py-3 text-left">Quiz</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Players</th>
                                    <th class="px-4 py-3 text-left">Winner</th>
                                    <th class="px-4 py-3 text-right">Open</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $ev)
                                    <tr class="border-t-4 border-main-dark fira-sans text-main-dark">
                                        <td class="px-4 py-3 font-bold">{{ $ev['title'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide capitalize {{ $ev['status'] == 'completed' ? 'bg-main-dark text-white' : ($ev['status'] == 'started' ? 'bg-green-500 text-white' : 'bg-surface text-main-dark/70') }}">{{ $ev['status'] }}</span>
                                        </td>
                                        <td class="px-4 py-3">{{ $ev['player_count'] }}</td>
                                        <td class="px-4 py-3">
                                            @if($ev['winner_name'])
                                                <span class="font-bold">{{ $ev['winner_name'] }}</span> <span class="text-main-dark/50">· {{ $ev['winner_points'] }} pts</span>
                                            @else
                                                <span class="text-main-dark/40">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <a href="{{ $ev['status'] == 'completed' ? '/trivia/' . $ev['token'] . '/results' : '/trv/trivia/' . $ev['token'] }}" class="fira-sans font-black uppercase text-xs tracking-wide bg-brand text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-1.5 inline-block transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">{{ $ev['status'] == 'completed' ? 'Results' : 'Open' }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-8 text-center">
                        <p class="fira-sans text-main-dark/60">No games played yet.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <script>
        function copyShare() {
            var input = document.getElementById('share-url');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(function () {
                var label = document.getElementById('copy-label');
                label.textContent = 'Copied!';
                setTimeout(function () { label.textContent = 'Copy'; }, 1500);
            });
        }
    </script>

@endsection
