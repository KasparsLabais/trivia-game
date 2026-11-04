@extends('game-api::layout')
@section('body')

    @php
        $top3 = array_slice($leaderboard, 0, 3);
        $rest = array_slice($leaderboard, 3);
        $podiumOrder = [1, 0, 2]; // silver, gold, bronze across the stage
        $podiumHeights = [0 => 'h-40', 1 => 'h-28', 2 => 'h-20'];
        $podiumBg = [0 => 'bg-pop', 1 => 'bg-surface', 2 => 'bg-brand'];
    @endphp

    <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-5xl mx-auto flex flex-col gap-8">

            {{-- Header --}}
            <div class="text-center flex flex-col items-center gap-3">
                <div class="inline-flex items-center gap-2 bg-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-3 py-1.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-green-500"></span>
                    </span>
                    <span class="fira-sans text-main-dark text-xs font-bold uppercase tracking-widest">Live scoreboard</span>
                </div>
                <h1 class="relative inline-block mt-1">
                    <span aria-hidden="true" class="absolute inset-0 -rotate-1 bg-green-300"></span>
                    <span class="relative block russo text-main-dark text-2xl md:text-4xl tracking-widest uppercase px-5 py-2">{{ $season->name }}</span>
                </h1>
                @if($season->prize || $season->coin_pot > 0)
                    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
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
                @endif
                @if($season->status == 'completed' && $season->winner_name)
                    <div class="mt-2 inline-flex items-center gap-3 bg-pop border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-5 py-3">
                        <span class="text-3xl">🏆</span>
                        <div class="text-left">
                            <div class="fira-sans text-[11px] uppercase tracking-widest text-main-dark/70">Champion</div>
                            <div class="russo text-xl text-main-dark leading-tight">{{ $season->winner_name }}</div>
                        </div>
                    </div>
                @endif
            </div>

            @if(count($leaderboard))
                {{-- Podium (top 3) --}}
                @if(count($top3))
                    <div class="flex items-end justify-center gap-3 md:gap-5">
                        @foreach($podiumOrder as $slot)
                            @continue(!isset($top3[$slot]))
                            @php $p = $top3[$slot]; @endphp
                            <div class="flex flex-col items-center gap-2 w-24 md:w-36">
                                <div class="w-14 h-14 md:w-20 md:h-20 bg-white border-4 overflow-hidden {{ $p['border'] ? '' : 'border-main-dark' }}" @if($p['border']) style="border-color: {{ $p['border'] }}; box-shadow: 4px 4px 0 0 {{ $p['border'] }};" @else style="box-shadow: 4px 4px 0 0 #14323B;" @endif>
                                    <img src="{{ $p['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                                </div>
                                <div class="fira-sans font-bold text-main-dark text-sm text-center truncate w-full">{{ $p['username'] }}</div>
                                <div class="w-full {{ $podiumHeights[$slot] }} {{ $podiumBg[$slot] }} border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] flex flex-col items-center justify-center">
                                    <span class="russo text-2xl md:text-3xl text-main-dark leading-none">{{ $p['points'] }}</span>
                                    <span class="russo text-3xl md:text-4xl text-main-dark/30 leading-none mt-1">{{ $slot + 1 }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Everyone else --}}
                @if(count($rest))
                    <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] overflow-hidden">
                        @foreach($rest as $i => $row)
                            <div class="flex items-center gap-3 px-4 py-2.5 {{ !$loop->last ? 'border-b-2 border-main-dark/20' : '' }}">
                                <span class="russo text-lg text-main-dark/40 w-8 shrink-0 text-center">{{ $i + 4 }}</span>
                                <div class="w-8 h-8 shrink-0 bg-white border-2 overflow-hidden {{ $row['border'] ? '' : 'border-main-dark' }}" @if($row['border']) style="border-color: {{ $row['border'] }};" @endif>
                                    <img src="{{ $row['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                                </div>
                                <span class="fira-sans font-bold text-main-dark flex-1 min-w-0 truncate">{{ $row['username'] }}@if($row['is_guest'])<span class="ml-1 fira-sans text-[10px] uppercase tracking-wide text-main-dark/40">guest</span>@endif</span>
                                <span class="fira-sans text-[11px] text-main-dark/50 shrink-0">{{ $row['games_played'] }}g</span>
                                <span class="russo text-lg text-main-dark shrink-0">{{ $row['points'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-16 text-center">
                    <p class="russo text-main-dark/60 text-2xl">The season hasn't started</p>
                    <p class="fira-sans text-main-dark/60 mt-2">Scores will appear here once the first quiz is played.</p>
                </div>
            @endif

            <p class="text-center fira-sans text-xs text-main-dark/40">{{ count($events) }} {{ count($events) == 1 ? 'game' : 'games' }} played · scoreboard refreshes automatically</p>
        </div>
    </div>

    {{-- Keep the big screen current without manual refresh --}}
    <script>
        setTimeout(function () { window.location.reload(); }, 20000);
    </script>

@endsection
