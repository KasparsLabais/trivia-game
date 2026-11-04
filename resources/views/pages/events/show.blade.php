@extends('game-api::layout')
@section('body')

    @php
        $labelClass  = 'fira-sans text-xs uppercase tracking-widest text-main-dark/60 mb-1.5';
        $inputClass  = 'w-full bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-3 py-2.5 fira-sans text-main-dark placeholder:text-main-dark/40 focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] transition-all';
        $selectClass = 'w-full appearance-none bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] pl-3 pr-10 py-2.5 fira-sans font-bold text-main-dark focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] cursor-pointer transition-all';
        $token = optional($event->gameInstance)->token;
    @endphp

    <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-5xl mx-auto flex flex-col gap-8">

            <a href="/trv/events" class="self-start fira-sans font-bold uppercase text-xs tracking-wide text-main-dark/70 hover:text-main-dark">&larr; All events</a>

            {{-- Header --}}
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="relative self-start inline-block">
                    <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                    <span class="relative block russo text-main-dark text-lg md:text-2xl tracking-widest uppercase px-4 py-1.5">{{ $event->name }}</span>
                </h1>
                <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide {{ $event->status == 'completed' ? 'bg-main-dark text-white' : ($event->status == 'live' ? 'bg-green-500 text-white' : 'bg-surface text-main-dark/70') }}">{{ $event->status }}</span>
            </div>

            {{-- Branded display preview --}}
            <div class="flex flex-col gap-2">
                <span class="{{ $labelClass }}">Big-screen preview</span>
                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] overflow-hidden">
                    <div class="flex items-center gap-2.5 bg-main-dark px-5 py-3">
                        <span class="w-4 h-4 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-4 h-4 rounded-full bg-pop shrink-0"></span>
                        <span class="w-4 h-4 rounded-full bg-green-500 shrink-0"></span>
                        @if($event->logo_path)
                            <img src="{{ asset($event->logo_path) }}" class="w-9 h-9 object-contain bg-white border-2 border-white/40 ml-2 shrink-0" alt="">
                        @endif
                        <h2 class="russo text-white text-xl tracking-wide ml-2 truncate">{{ $event->name }}</h2>
                        <span class="hidden sm:inline-flex items-center fira-sans text-[11px] uppercase tracking-widest text-main-dark px-2 py-0.5 shrink-0" style="background-color: {{ $event->accent_color }};">Event</span>
                        <div class="ml-auto flex items-baseline gap-2 shrink-0">
                            <span class="fira-sans text-xs uppercase tracking-widest text-white/60">PIN</span>
                            <span class="russo text-pop text-2xl tracking-widest">{{ $event->gameInstance->pin ?? '— — — —' }}</span>
                        </div>
                    </div>
                    <div class="h-2" style="background-color: {{ $event->accent_color }};"></div>
                    <div class="px-5 py-6 flex items-center gap-5">
                        <div class="w-20 h-20 shrink-0 border-4 border-main-dark bg-pop overflow-hidden flex items-center justify-center">
                            @if($event->badge_path)
                                <img src="{{ asset($event->badge_path) }}" class="w-full h-full object-cover" alt="">
                            @else
                                <span class="text-4xl">🏆</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <div class="fira-sans text-xs uppercase tracking-widest text-main-dark/50">Winner trophy</div>
                            <div class="russo text-main-dark text-lg leading-tight">{{ $event->badge_name ?: 'No badge uploaded yet' }}</div>
                            @if($event->description)<div class="fira-sans text-sm text-main-dark/60 mt-1">{{ $event->description }}</div>@endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- COMPLETED: champion --}}
            @if($event->status == 'completed')
                <div class="bg-pop border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] px-6 py-5 flex items-center gap-5">
                    <div class="w-20 h-20 shrink-0 border-4 border-main-dark bg-white overflow-hidden flex items-center justify-center">
                        @if($event->badge_path)<img src="{{ asset($event->badge_path) }}" class="w-full h-full object-cover" alt="">@else<span class="text-4xl">🏆</span>@endif
                    </div>
                    <div>
                        <div class="fira-sans text-xs uppercase tracking-widest text-main-dark/70">Event champion</div>
                        <div class="russo text-2xl text-main-dark leading-tight">{{ $event->winner_name }}</div>
                        @if($event->badge_path)<div class="fira-sans text-sm text-main-dark/80 mt-0.5">Awarded: {{ $event->badge_name ?: 'Champion badge' }} — now permanent on their profile</div>@endif
                    </div>
                </div>
                @if($event->coin_pot > 0 && count($coinPodium))
                    <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <span class="russo text-main-dark text-sm uppercase tracking-widest">Coin pot paid out</span>
                            <span class="russo text-main-dark text-lg leading-none">🪙 {{ number_format($event->coin_pot) }}</span>
                        </div>
                        <div class="flex flex-col gap-2">
                            @foreach($coinPodium as $slot)
                                <div class="flex items-center gap-3 border-4 border-main-dark px-3 py-2 {{ $slot['rank'] == 1 ? 'bg-pop' : 'bg-white' }}">
                                    <span class="russo text-lg w-6 shrink-0 text-center {{ $slot['rank'] == 1 ? 'text-main-dark' : 'text-main-dark/40' }}">{{ $slot['rank'] }}</span>
                                    <div class="w-8 h-8 shrink-0 bg-white border-2 border-main-dark overflow-hidden">
                                        <img src="{{ $slot['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                                    </div>
                                    <span class="fira-sans font-bold text-main-dark flex-1 min-w-0 truncate">{{ $slot['username'] }}</span>
                                    <span class="russo text-main-dark shrink-0">🪙 {{ number_format($slot['amount']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if($token)
                    <a href="/trivia/{{ $token }}/results" class="self-start fira-sans font-black uppercase tracking-wider text-sm bg-brand text-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-5 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">View game results</a>
                @endif
            @endif

            {{-- LIVE: open game + award the trophy --}}
            @if($event->status == 'live')
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                        <div class="flex items-center gap-2.5 bg-green-500 px-4 py-2.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-white shrink-0"></span>
                            <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Game is live</span>
                        </div>
                        <div class="p-5 flex flex-col gap-3">
                            <p class="fira-sans text-main-dark/70">Run the game from the control screen; put the branded display on the big screen.</p>
                            <div class="flex flex-wrap gap-3">
                                <a href="/trv/trivia/{{ $token }}" class="fira-sans font-black uppercase text-xs tracking-wide bg-main-dark text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Control screen</a>
                                <a href="/trv/trivia/{{ $token }}/display" target="_blank" class="fira-sans font-black uppercase text-xs tracking-wide bg-brand text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Branded display</a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                        <div class="flex items-center gap-2.5 bg-pop px-4 py-2.5">
                            <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-white shrink-0"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                            <span class="ml-2 russo uppercase tracking-widest text-main-dark text-xs">Award the trophy</span>
                        </div>
                        <div class="p-5">
                            @if($event->coin_pot > 0)
                                <div class="border-4 border-main-dark bg-main-light p-3 mb-4">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="fira-sans text-[11px] uppercase tracking-widest text-main-dark/60">Coin prize pot</span>
                                        <span class="russo text-main-dark text-lg leading-none">🪙 {{ number_format($event->coin_pot) }}</span>
                                    </div>
                                    @if(count($coinPodium))
                                        <div class="flex flex-col gap-1 mt-2.5">
                                            @foreach($coinPodium as $slot)
                                                <div class="flex items-center gap-2 fira-sans text-sm text-main-dark">
                                                    <span class="russo text-main-dark/40 w-4 shrink-0">{{ $slot['rank'] }}</span>
                                                    <span class="flex-1 min-w-0 truncate">{{ $slot['username'] }}</span>
                                                    <span class="russo shrink-0">🪙 {{ number_format($slot['amount']) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <p class="fira-sans text-[11px] text-main-dark/50 mt-2">Paid to the top scorers (50/30/20) when you crown the winner.</p>
                                    @else
                                        <p class="fira-sans text-[11px] text-main-dark/50 mt-2">Split 50/30/20 across the top three scorers once registered players have points.</p>
                                    @endif
                                </div>
                            @endif
                            @if(count($candidates))
                                <form action="/trv/events/{{ $event->id }}/award" method="POST" onsubmit="return confirm('Award the trophy to the selected player? This finishes the event and adds a permanent badge to their profile.');">
                                    {{ csrf_field() }}
                                    <div class="flex flex-col gap-2 mb-4 max-h-64 overflow-y-auto">
                                        @foreach($candidates as $i => $c)
                                            <label class="flex items-center gap-3 border-4 border-main-dark px-3 py-2 cursor-pointer {{ $i == 0 ? 'bg-main-light' : 'bg-white' }}">
                                                <input type="radio" name="winner_user_id" value="{{ $c['user_id'] }}" {{ $i == 0 ? 'checked' : '' }} class="w-4 h-4 shrink-0 accent-main-accent">
                                                <div class="w-8 h-8 shrink-0 bg-white border-2 border-main-dark overflow-hidden">
                                                    <img src="{{ $c['avatar'] }}" onerror="this.src='/images/default-avatar.jpg'" class="w-full h-full object-cover">
                                                </div>
                                                <span class="fira-sans font-bold text-main-dark flex-1 min-w-0 truncate">{{ $c['username'] }}</span>
                                                <span class="russo text-main-dark shrink-0">{{ $c['points'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @if(!$event->badge_path)
                                        <p class="fira-sans text-[11px] text-main-dark/50 mb-3">No badge image uploaded — the winner will be recorded, but no permanent trophy is granted.</p>
                                    @endif
                                    <button type="submit" class="fira-sans font-black uppercase tracking-wider text-sm bg-main-accent text-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-5 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Crown the champion</button>
                                </form>
                            @else
                                <p class="fira-sans text-main-dark/50 text-center py-6">No registered players yet. Once people join and play, pick the winner here.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- DRAFT: set up + launch --}}
            @if($event->status == 'draft')
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Set Up Event</span>
                    </div>
                    <div class="p-5 md:p-6">
                        <form action="/trv/events/{{ $event->id }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                            {{ csrf_field() }}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="name">Event name</label>
                                    <input class="{{ $inputClass }}" type="text" name="name" id="name" value="{{ $event->name }}" required>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="trivia_id">Quiz</label>
                                    <div class="relative">
                                        <select name="trivia_id" id="trivia_id" class="{{ $selectClass }}">
                                            <option value="">— pick one of your quizzes —</option>
                                            @foreach($trivias as $trv)
                                                <option value="{{ $trv->id }}" {{ $event->trivia_id == $trv->id ? 'selected' : '' }}>{{ $trv->title }}</option>
                                            @endforeach
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="description">Description</label>
                                    <input class="{{ $inputClass }}" type="text" name="description" id="description" value="{{ $event->description }}" placeholder="Optional">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="coin_pot">Coin prize pot</label>
                                    <input class="{{ $inputClass }}" type="number" name="coin_pot" id="coin_pot" min="0" step="10" value="{{ $event->coin_pot }}">
                                    <span class="fira-sans text-[11px] text-main-dark/50 mt-1">🪙 Split 50/30/20 across the top three scorers.</span>
                                </div>
                            </div>

                            <div class="border-t-4 border-main-dark pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="accent_color">Display accent</label>
                                    <input type="color" name="accent_color" id="accent_color" value="{{ $event->accent_color }}" class="w-full h-12 bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] cursor-pointer p-1">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="logo">Brand logo {{ $event->logo_path ? '(replace)' : '' }}</label>
                                    <div class="flex items-stretch bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B]">
                                        <label for="logo" class="flex items-center bg-main-dark text-white fira-sans font-bold uppercase text-xs tracking-wide px-3 py-2 cursor-pointer whitespace-nowrap">File</label>
                                        <input id="logo" class="hidden" type="file" name="logo" accept="image/*" onchange="document.getElementById('logo-name').textContent = this.files.length ? this.files[0].name : '{{ $event->logo_path ? 'Current kept' : 'None' }}';">
                                        <span id="logo-name" class="flex items-center px-2 py-2 fira-sans text-main-dark text-xs truncate min-w-0">{{ $event->logo_path ? 'Current kept' : 'None' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="badge">Winner badge {{ $event->badge_path ? '(replace)' : '' }}</label>
                                    <div class="flex items-stretch bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B]">
                                        <label for="badge" class="flex items-center bg-main-dark text-white fira-sans font-bold uppercase text-xs tracking-wide px-3 py-2 cursor-pointer whitespace-nowrap">File</label>
                                        <input id="badge" class="hidden" type="file" name="badge" accept="image/*" onchange="document.getElementById('badge-name').textContent = this.files.length ? this.files[0].name : '{{ $event->badge_path ? 'Current kept' : 'None' }}';">
                                        <span id="badge-name" class="flex items-center px-2 py-2 fira-sans text-main-dark text-xs truncate min-w-0">{{ $event->badge_path ? 'Current kept' : 'None' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:col-span-3">
                                    <label class="{{ $labelClass }}" for="badge_name">Badge title</label>
                                    <input class="{{ $inputClass }}" type="text" name="badge_name" id="badge_name" value="{{ $event->badge_name }}" placeholder="Summer Showdown 2026 Champion">
                                </div>
                            </div>

                            <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-brand text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Save Changes</button>
                        </form>
                    </div>
                </div>

                {{-- Launch --}}
                <div class="bg-main-light border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="russo text-main-dark text-sm uppercase tracking-widest">Ready to go?</div>
                        <p class="fira-sans text-main-dark/60 text-sm mt-1">Launching opens the branded game for players to join.</p>
                    </div>
                    @if($event->trivia_id)
                        <form action="/trv/events/{{ $event->id }}/launch" method="POST" class="shrink-0">
                            {{ csrf_field() }}
                            <button type="submit" class="fira-sans font-black uppercase tracking-wider bg-main-accent text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-3 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Launch Event</button>
                        </form>
                    @else
                        <span class="shrink-0 fira-sans text-sm text-main-accent font-bold">Pick a quiz above first.</span>
                    @endif
                </div>
            @endif

        </div>
    </div>

@endsection
