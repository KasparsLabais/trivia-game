@extends('game-api::layout')
@section('body')

    @php
        $labelClass  = 'fira-sans text-xs uppercase tracking-widest text-main-dark/60 mb-1.5';
        $inputClass  = 'w-full bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-3 py-2.5 fira-sans text-main-dark placeholder:text-main-dark/40 focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] transition-all';
        $selectClass = 'w-full appearance-none bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] pl-3 pr-10 py-2.5 fira-sans font-bold text-main-dark focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] cursor-pointer transition-all';
        $isPro = $plan['events'] ?? false;
    @endphp

    <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-6xl mx-auto flex flex-col gap-8">

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <h1 class="relative self-start inline-block">
                        <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                        <span class="relative block russo text-main-dark text-lg md:text-2xl tracking-widest uppercase px-4 py-1.5">Events</span>
                    </h1>
                    <p class="fira-sans text-main-dark/60 mt-3 max-w-xl">Host a branded one-off — a corporate night, a charity quiz, a launch party. Put your name and logo on the big screen, and hand the winner a trophy they keep on their profile forever.</p>
                </div>
                <a href="/trv/management" class="shrink-0 self-start fira-sans font-bold uppercase text-xs tracking-wide bg-white text-main-dark border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">&larr; My Trivias</a>
            </div>

            @if($isPro)
                {{-- Create New Event --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Create New Event</span>
                    </div>
                    <div class="p-5 md:p-6">
                        <form action="/trv/events" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                            {{ csrf_field() }}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="name">Event name</label>
                                    <input class="{{ $inputClass }}" type="text" name="name" id="name" placeholder="Acme Corp Summer Showdown" required>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="trivia_id">Quiz</label>
                                    <div class="relative">
                                        <select name="trivia_id" id="trivia_id" class="{{ $selectClass }}">
                                            <option value="">— pick one of your quizzes —</option>
                                            @foreach($trivias as $trv)
                                                <option value="{{ $trv->id }}">{{ $trv->title }}</option>
                                            @endforeach
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="description">Description <span class="normal-case tracking-normal">(optional)</span></label>
                                    <input class="{{ $inputClass }}" type="text" name="description" id="description" placeholder="One night only — top of the leaderboard wins the cup">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="coin_pot">Coin prize pot <span class="normal-case tracking-normal">(optional)</span></label>
                                    <input class="{{ $inputClass }}" type="number" name="coin_pot" id="coin_pot" min="0" step="10" placeholder="0" value="0">
                                    <span class="fira-sans text-[11px] text-main-dark/50 mt-1">🪙 Split 50/30/20 across the top three scorers when you crown the winner.</span>
                                </div>
                            </div>

                            <div class="border-t-4 border-main-dark pt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="accent_color">Display accent</label>
                                    <input type="color" name="accent_color" id="accent_color" value="#E62727" class="w-full h-12 bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] cursor-pointer p-1">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="logo">Brand logo <span class="normal-case tracking-normal">(optional)</span></label>
                                    <div class="flex items-stretch bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B]">
                                        <label for="logo" class="flex items-center bg-main-dark text-white fira-sans font-bold uppercase text-xs tracking-wide px-3 py-2 cursor-pointer whitespace-nowrap">File</label>
                                        <input id="logo" class="hidden" type="file" name="logo" accept="image/*" onchange="document.getElementById('logo-name').textContent = this.files.length ? this.files[0].name : 'None';">
                                        <span id="logo-name" class="flex items-center px-2 py-2 fira-sans text-main-dark text-xs truncate min-w-0">None</span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="badge">Winner badge <span class="normal-case tracking-normal">(optional)</span></label>
                                    <div class="flex items-stretch bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B]">
                                        <label for="badge" class="flex items-center bg-main-dark text-white fira-sans font-bold uppercase text-xs tracking-wide px-3 py-2 cursor-pointer whitespace-nowrap">File</label>
                                        <input id="badge" class="hidden" type="file" name="badge" accept="image/*" onchange="document.getElementById('badge-name').textContent = this.files.length ? this.files[0].name : 'None';">
                                        <span id="badge-name" class="flex items-center px-2 py-2 fira-sans text-main-dark text-xs truncate min-w-0">None</span>
                                    </div>
                                </div>
                                <div class="flex flex-col sm:col-span-3">
                                    <label class="{{ $labelClass }}" for="badge_name">Badge title <span class="normal-case tracking-normal">(shown on the winner's profile)</span></label>
                                    <input class="{{ $inputClass }}" type="text" name="badge_name" id="badge_name" placeholder="Summer Showdown 2026 Champion">
                                </div>
                            </div>

                            <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-main-accent text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Create Event</button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Upgrade prompt --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-brand px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Events is a Pro feature</span>
                    </div>
                    <div class="p-6 md:p-8 flex flex-col gap-5">
                        <p class="fira-sans text-main-dark text-lg max-w-2xl">Run a branded night your guests won't forget. Put your event name, logo and colours on the big screen, then award the winner a custom trophy that lives on their profile forever.</p>
                        <ul class="fira-sans text-main-dark/80 flex flex-col gap-2">
                            <li class="flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 bg-green-500 border-2 border-main-dark"></span> Customisable branded display — name, logo, accent colour</li>
                            <li class="flex items-center gap-2"><span class="inline-block w-2.5 h-2.5 bg-pop border-2 border-main-dark"></span> Upload a winner badge, kept permanently on the champion's account</li>
                        </ul>
                        <a href="/premium" class="self-start fira-sans font-black uppercase tracking-wider bg-pop text-main-dark border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-3 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Upgrade to Pro</a>
                    </div>
                </div>
            @endif

            {{-- Listing --}}
            <div class="flex flex-col gap-4">
                <h2 class="russo text-main-dark text-base uppercase tracking-widest">Your Events</h2>

                @if($events->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($events as $event)
                            <a href="/trv/events/{{ $event->id }}" class="group bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 flex items-center gap-4 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0_0_#14323B]">
                                <div class="w-16 h-16 shrink-0 border-4 border-main-dark overflow-hidden flex items-center justify-center" style="background-color: {{ $event->accent_color }};">
                                    @if($event->badge_path)
                                        <img src="{{ asset($event->badge_path) }}" class="w-full h-full object-cover" alt="">
                                    @elseif($event->logo_path)
                                        <img src="{{ asset($event->logo_path) }}" class="w-full h-full object-contain bg-white" alt="">
                                    @else
                                        <span class="text-2xl">🎉</span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="russo text-main-dark text-base leading-tight truncate">{{ $event->name }}</span>
                                        <span class="shrink-0 inline-block border-2 border-main-dark px-2 py-0.5 russo text-[9px] uppercase tracking-wide {{ $event->status == 'completed' ? 'bg-main-dark text-white' : ($event->status == 'live' ? 'bg-green-500 text-white' : 'bg-surface text-main-dark/70') }}">{{ $event->status }}</span>
                                    </div>
                                    @if($event->status == 'completed' && $event->winner_name)
                                        <div class="fira-sans text-sm text-main-dark/70 mt-1">🏆 {{ $event->winner_name }}</div>
                                    @elseif($event->badge_name)
                                        <div class="fira-sans text-sm text-main-dark/50 mt-1 truncate">{{ $event->badge_name }}</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-10 text-center">
                        <p class="russo text-main-dark/60 text-xl">No events yet</p>
                        <p class="fira-sans text-main-dark/60 mt-1">{{ $isPro ? 'Create your first branded event above.' : 'Upgrade to Pro to host branded events.' }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection
