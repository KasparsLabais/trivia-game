@extends('game-api::layout')
@section('page-bg', 'bg-pop')
@section('body')

    <style>
        .dotted-canvas {
            background-color: #FFB900;
            background-image: radial-gradient(#14323B 2.5px, transparent 2.6px);
            background-size: 26px 26px;
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

            {{-- Results window: podium --}}
            <div class="w-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">

                {{-- Title bar --}}
                <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                    <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                    <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                    <span class="ml-2 russo uppercase tracking-widest text-white text-xs md:text-sm">Results</span>
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
                                    <div class="{{ $spot['order'] }} flex-1 min-w-0 flex flex-col items-center gap-3">
                                        @if($spot['place'] == 1)
                                            <span class="russo text-main-accent text-xl uppercase tracking-widest">Winner!</span>
                                        @endif
                                        <img class="{{ $spot['place'] == 1 ? 'w-28 h-28' : 'w-20 h-20' }} object-cover bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B]"
                                             src="{{ $avatar($spot['player']) }}" alt="{{ $spot['player']['username'] }}">
                                        <div class="text-center w-full">
                                            <p class="fira-sans font-bold text-main-dark {{ $spot['place'] == 1 ? 'text-2xl' : 'text-lg' }} break-words">{{ $spot['player']['username'] }}</p>
                                            <span class="inline-block mt-1 bg-white text-main-dark border-2 border-main-dark px-2 py-0.5 russo text-xs uppercase tracking-wide">{{ $spot['player']['points'] }} Points</span>
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

            {{-- Everyone else --}}
            @if(!empty($winners['winners']))
                <div class="w-full bg-white border-4 border-main-dark shadow-[10px_10px_0_0_#14323B] overflow-hidden">
                    <div class="flex items-center gap-2.5 bg-brand border-b-4 border-main-dark px-5 py-3">
                        <span class="russo uppercase tracking-widest text-white text-xs md:text-sm">Happy To Participate</span>
                    </div>
                    <div class="flex flex-col">
                        @foreach($winners['winners'] as $runnerUp)
                            <div class="flex flex-row items-center gap-4 px-5 py-3 {{ $loop->last ? '' : 'border-b-4 border-main-dark' }} {{ $loop->odd ? 'bg-main-light' : 'bg-white' }}">
                                <span class="russo text-main-dark/50 text-lg w-8 shrink-0">#{{ $loop->iteration + 3 }}</span>
                                <img class="w-10 h-10 object-cover bg-white border-2 border-main-dark shrink-0" src="{{ $avatar($runnerUp) }}" alt="{{ $runnerUp['username'] }}">
                                <span class="flex-1 min-w-0 fira-sans font-bold text-main-dark break-words">{{ $runnerUp['username'] }}</span>
                                <span class="shrink-0 fira-sans font-bold text-main-dark">{{ $runnerUp['points'] }} <span class="text-main-dark/60 text-xs uppercase tracking-widest">pts</span></span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/trivia"
                   class="sm:w-64 text-center bg-main-accent text-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Play Another</a>
                <a href="/"
                   class="sm:w-64 text-center bg-white text-main-dark border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] py-3.5 text-xl font-black uppercase tracking-wider fira-sans transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Back Home</a>
            </div>

        </div>
    </div>

@endsection
