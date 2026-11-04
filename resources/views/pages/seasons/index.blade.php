@extends('game-api::layout')
@section('body')

    @php
        $labelClass  = 'fira-sans text-xs uppercase tracking-widest text-main-dark/60 mb-1.5';
        $inputClass  = 'w-full bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-3 py-2.5 fira-sans text-main-dark placeholder:text-main-dark/40 focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] transition-all';
        $isPro = $plan['seasons'] ?? false;
    @endphp

    <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-6xl mx-auto flex flex-col gap-8">

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <h1 class="relative self-start inline-block">
                        <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                        <span class="relative block russo text-main-dark text-lg md:text-2xl tracking-widest uppercase px-4 py-1.5">Seasons</span>
                    </h1>
                    <p class="fira-sans text-main-dark/60 mt-3 max-w-xl">Run a recurring event — a pub quiz, a league, a tournament. Group your quizzes, keep one leaderboard across every night, and crown an overall champion.</p>
                </div>
                <a href="/trv/management" class="shrink-0 self-start fira-sans font-bold uppercase text-xs tracking-wide bg-white text-main-dark border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">&larr; My Trivias</a>
            </div>

            @if($isPro)
                {{-- Create New Season --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Create New Season</span>
                    </div>
                    <div class="p-5 md:p-6">
                        <form action="/trv/seasons" method="POST" class="flex flex-col gap-4">
                            {{ csrf_field() }}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="name">Season name</label>
                                    <input class="{{ $inputClass }}" type="text" name="name" id="name" placeholder="Tuesday Pub Quiz — Winter League" required>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="prize">Prize for the winner <span class="normal-case tracking-normal">(optional)</span></label>
                                    <input class="{{ $inputClass }}" type="text" name="prize" id="prize" placeholder="£50 bar tab">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="description">Description <span class="normal-case tracking-normal">(optional)</span></label>
                                    <input class="{{ $inputClass }}" type="text" name="description" id="description" placeholder="8 weeks, best 6 scores count toward the title">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="coin_pot">Coin prize pot <span class="normal-case tracking-normal">(optional)</span></label>
                                    <input class="{{ $inputClass }}" type="number" name="coin_pot" id="coin_pot" min="0" step="10" placeholder="0" value="0">
                                    <span class="fira-sans text-[11px] text-main-dark/50 mt-1">🪙 Split 50/30/20 across the top three when the season ends.</span>
                                </div>
                            </div>
                            <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-main-accent text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Create Season</button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Upgrade prompt (also advertises the feature) --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-brand px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Seasons is a Pro feature</span>
                    </div>
                    <div class="p-6 md:p-8 flex flex-col gap-5">
                        <p class="fira-sans text-main-dark text-lg max-w-2xl">Turn your one-off quizzes into a recurring event. Seasons chain multiple quizzes together, keep a single running leaderboard across every night, and award a prize to the overall champion — perfect for pub quizzes and leagues.</p>
                        <ul class="fira-sans text-main-dark/80 flex flex-col gap-2">
                            <li class="flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 bg-green-500 border-2 border-main-dark"></span> One leaderboard across every quiz in the season</li>
                            <li class="flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 bg-pop border-2 border-main-dark"></span> An individual winner each night, plus an overall champion</li>
                            <li class="flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 bg-brand border-2 border-main-dark"></span> A shareable live scoreboard for the big screen</li>
                        </ul>
                        <a href="/premium" class="self-start fira-sans font-black uppercase tracking-wider bg-pop text-main-dark border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-3 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Upgrade to Pro</a>
                    </div>
                </div>
            @endif

            {{-- Listing --}}
            <div class="flex flex-col gap-4">
                <h2 class="russo text-main-dark text-base uppercase tracking-widest">Your Seasons</h2>

                @if($seasons->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($seasons as $season)
                            <a href="/trv/seasons/{{ $season->id }}" class="group bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 flex flex-col gap-4 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B]">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="russo text-main-dark text-lg leading-tight">{{ $season->name }}</span>
                                    <span class="shrink-0 inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide {{ $season->status == 'completed' ? 'bg-main-dark text-white' : 'bg-green-500 text-white' }}">{{ $season->status }}</span>
                                </div>
                                @if($season->prize)
                                    <div class="fira-sans text-sm text-main-dark/70 flex items-center gap-2">
                                        <span class="inline-block border-2 border-main-dark bg-pop px-2 py-0.5 russo text-[10px] uppercase tracking-wide">Prize</span>
                                        {{ $season->prize }}
                                    </div>
                                @endif
                                <div class="flex gap-3 mt-auto">
                                    <div class="flex-1 border-2 border-main-dark px-3 py-2 text-center">
                                        <div class="russo text-xl text-main-dark leading-none">{{ $season->trivias_count }}</div>
                                        <div class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mt-1">Quizzes</div>
                                    </div>
                                    <div class="flex-1 border-2 border-main-dark px-3 py-2 text-center">
                                        <div class="russo text-xl text-brand leading-none">{{ $season->games_count }}</div>
                                        <div class="fira-sans text-[10px] uppercase tracking-widest text-main-dark/60 mt-1">Games</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-10 text-center">
                        <p class="russo text-main-dark/60 text-xl">No seasons yet</p>
                        <p class="fira-sans text-main-dark/60 mt-1">{{ $isPro ? 'Create your first season above to get started.' : 'Upgrade to Pro to run your first season.' }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection
