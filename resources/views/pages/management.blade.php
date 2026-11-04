@extends('game-api::layout')
@section('body')

    @php
        // Shared field styling so every input/select on this page matches.
        $labelClass  = 'fira-sans text-xs uppercase tracking-widest text-main-dark/60 mb-1.5';
        $inputClass  = 'w-full bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-3 py-2.5 fira-sans text-main-dark placeholder:text-main-dark/40 focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] transition-all';
        $selectClass = 'w-full appearance-none bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] pl-3 pr-10 py-2.5 fira-sans font-bold text-main-dark focus:outline-none focus:shadow-[6px_6px_0_0_#14323B] cursor-pointer transition-all';
    @endphp

    <div class="w-full px-[15px] lg:px-[30px] pt-8 pb-12">
        <div class="max-w-6xl mx-auto flex flex-col gap-8">

            {{-- Header: highlighter title + quick stats --}}
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <h1 class="relative self-start inline-block">
                        <span aria-hidden="true" class="absolute inset-0 -rotate-2 bg-green-300"></span>
                        <span class="relative block russo text-main-dark text-lg md:text-2xl tracking-widest uppercase px-4 py-1.5">My Trivias</span>
                    </h1>
                    <p class="fira-sans text-main-dark/60 mt-3">Create, import and manage your own quizzes.</p>
                    <a href="/trv/seasons" class="inline-flex items-center gap-2 mt-3 fira-sans font-black uppercase text-xs tracking-wide bg-pop text-main-dark border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" class="w-4 h-4 stroke-main-dark shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                        Run a Season
                    </a>
                    <a href="/trv/events" class="inline-flex items-center gap-2 mt-3 ml-0 sm:ml-2 fira-sans font-black uppercase text-xs tracking-wide bg-main-accent text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-2 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" class="w-4 h-4 stroke-current shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.25-13.5h16.5a1.5 1.5 0 0 1 1.5 1.5v1.5a1.5 1.5 0 0 1-1.5 1.5H3.75a1.5 1.5 0 0 1-1.5-1.5V9a1.5 1.5 0 0 1 1.5-1.5Z" />
                        </svg>
                        Host an Event
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-5 py-3 text-center">
                        <div class="russo text-3xl text-main-dark leading-none">{{ $trivias->count() }}</div>
                        <div class="fira-sans text-[11px] uppercase tracking-widest text-main-dark/60 mt-1.5">Total</div>
                    </div>
                    <div class="bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-5 py-3 text-center">
                        <div class="russo text-3xl text-green-600 leading-none">{{ $trivias->where('is_active', 1)->count() }}</div>
                        <div class="fira-sans text-[11px] uppercase tracking-widest text-main-dark/60 mt-1.5">Active</div>
                    </div>
                    <div class="bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B] px-5 py-3 text-center">
                        <div class="russo text-3xl text-main-accent leading-none">0</div>
                        <div class="fira-sans text-[11px] uppercase tracking-widest text-main-dark/60 mt-1.5">Plays</div>
                    </div>
                </div>
            </div>

            {{-- Create tools: two application windows side by side --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Window: Create New Trivia (manual) --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Create New Trivia</span>
                    </div>
                    <div class="p-5 md:p-6">
                        <form action="/trv/management/trivia" method="POST" class="flex flex-col gap-4">
                            {{ csrf_field() }}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="title">Title</label>
                                    <input class="{{ $inputClass }}" type="text" name="title" id="title" placeholder="My awesome quiz" required>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="description">Description</label>
                                    <input class="{{ $inputClass }}" type="text" name="description" id="description" placeholder="What's it about?">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="difficulty">Difficulty</label>
                                    <div class="relative">
                                        <select class="{{ $selectClass }}" name="difficulty" id="difficulty">
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="category">Category</label>
                                    <div class="relative">
                                        <select class="{{ $selectClass }}" name="category" id="category">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-main-accent text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Create Trivia</button>
                        </form>
                    </div>
                </div>

                {{-- Window: Create From API (auto-generated questions) --}}
                <div class="bg-white border-4 border-main-dark shadow-[8px_8px_0_0_#14323B]">
                    <div class="flex items-center gap-2.5 bg-main-dark px-4 py-2.5">
                        <span class="w-3.5 h-3.5 rounded-full bg-main-accent shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-pop shrink-0"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-green-500 shrink-0"></span>
                        <span class="ml-2 russo uppercase tracking-widest text-white text-xs">Create From API</span>
                    </div>
                    <div class="p-5 md:p-6">
                        <p class="fira-sans text-sm text-main-dark/60 mb-4">Auto-fills questions from the trivia API for the chosen category &amp; difficulty.</p>
                        <form action="/trv/management/api-trivia" method="POST" class="flex flex-col gap-4">
                            {{ csrf_field() }}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="api-title">Title</label>
                                    <input class="{{ $inputClass }}" type="text" name="title" id="api-title" placeholder="Quiz title" required>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="api-description">Description</label>
                                    <input class="{{ $inputClass }}" type="text" name="description" id="api-description" placeholder="Optional">
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="api-difficulty">Difficulty</label>
                                    <div class="relative">
                                        <select class="{{ $selectClass }}" name="difficulty" id="api-difficulty">
                                            <option value="easy">Easy</option>
                                            <option value="medium">Medium</option>
                                            <option value="hard">Hard</option>
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="flex flex-col">
                                    <label class="{{ $labelClass }}" for="api-category">Category</label>
                                    <div class="relative">
                                        <select class="{{ $selectClass }}" name="category" id="api-category">
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                            @endforeach
                                        </select>
                                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-brand text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Generate From API</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Import tools: two compact cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Import from CSV --}}
                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 md:p-6 flex flex-col gap-4">
                    <h2 class="russo text-main-dark text-sm uppercase tracking-widest">Import from CSV</h2>
                    <form action="/csv-upload/trivia" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                        {{ csrf_field() }}
                        <div class="flex flex-col">
                            <label class="{{ $labelClass }}" for="trivia-csv">CSV File</label>
                            <div class="flex items-stretch bg-white border-4 border-main-dark shadow-[4px_4px_0_0_#14323B]">
                                <label for="trivia-csv" class="flex items-center bg-main-dark text-white fira-sans font-bold uppercase text-sm tracking-wide px-4 py-2 cursor-pointer whitespace-nowrap">Choose file</label>
                                <input id="trivia-csv" class="hidden" type="file" name="trivia-csv" accept=".csv"
                                       onchange="document.getElementById('csv-filename').textContent = this.files.length ? this.files[0].name : 'No file chosen';">
                                <span id="csv-filename" class="flex items-center px-3 py-2 fira-sans text-main-dark text-sm truncate min-w-0">No file chosen</span>
                            </div>
                        </div>
                        <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-pop text-main-dark border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Upload CSV</button>
                    </form>
                </div>

                {{-- Load single-answer questions --}}
                <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] p-5 md:p-6 flex flex-col gap-4">
                    <h2 class="russo text-main-dark text-sm uppercase tracking-widest">Load Single-Answer Questions</h2>
                    <form action="/trv/api-single-upload/trivia" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
                        {{ csrf_field() }}
                        <div class="flex flex-col">
                            <label class="{{ $labelClass }}" for="single-category">Category</label>
                            <div class="relative">
                                <select name="category" id="single-category" class="{{ $selectClass }}">
                                    <option value="arts_and_literature">Art and Literature</option>
                                    <option value="entertainment">Entertainment</option>
                                    <option value="food_and_drink">Food and Drink</option>
                                    <option value="geography">Geography</option>
                                    <option value="history">History</option>
                                    <option value="language">Language</option>
                                    <option value="mathematics">Mathematics</option>
                                    <option value="music">Music</option>
                                    <option value="people_and_places">People and Places</option>
                                    <option value="religion_and_mythology">Religion and Mythology</option>
                                    <option value="science_and_nature">Science and Nature</option>
                                    <option value="sport_and_leisure">Sport and Leisure</option>
                                    <option value="tech_an_video_games">Tech and Video Games</option>
                                    <option value="toys_and_games">Toys and Games</option>
                                </select>
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center justify-center w-9 bg-main-dark text-white border-4 border-main-dark" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" /></svg>
                                </span>
                            </div>
                        </div>
                        <button type="submit" class="self-start fira-sans font-black uppercase tracking-wider bg-main-dark text-white border-4 border-main-dark shadow-[5px_5px_0_0_#14323B] px-6 py-2.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[7px_7px_0_0_#14323B] active:translate-x-1 active:translate-y-1 active:shadow-[0px_0px_0_0_#14323B]">Load Questions</button>
                    </form>
                </div>
            </div>

            {{-- Listing --}}
            <div class="flex flex-col gap-4">
                <h2 class="russo text-main-dark text-base uppercase tracking-widest">Your Trivias</h2>

                @if($trivias->count())
                    <div class="bg-white border-4 border-main-dark shadow-[6px_6px_0_0_#14323B] overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-main-dark text-white russo text-[11px] uppercase tracking-wider">
                                    <th class="px-4 py-3 text-left">Title</th>
                                    <th class="px-4 py-3 text-left">Difficulty</th>
                                    <th class="px-4 py-3 text-left">Category</th>
                                    <th class="px-4 py-3 text-left">Status</th>
                                    <th class="px-4 py-3 text-left">Visibility</th>
                                    <th class="px-4 py-3 text-left">Tier</th>
                                    <th class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trivias as $trv)
                                    <tr class="border-t-4 border-main-dark fira-sans text-main-dark">
                                        <td class="px-4 py-3 font-bold">{{ $trv['title'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide capitalize
                                                {{ $trv['difficulty'] == 'easy' ? 'bg-green-500 text-main-dark' : ($trv['difficulty'] == 'medium' ? 'bg-amber-400 text-main-dark' : 'bg-main-accent text-white') }}">
                                                {{ $trv['difficulty'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">{{ $trv->category['name'] }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide {{ $trv['is_active'] ? 'bg-green-500 text-white' : 'bg-surface text-main-dark/60' }}">
                                                {{ $trv['is_active'] ? 'Active' : 'Off' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide {{ $trv['private'] ? 'bg-pop text-main-dark' : 'bg-white text-main-dark/60' }}">
                                                {{ $trv['private'] ? 'Private' : 'Public' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="inline-block border-2 border-main-dark px-2.5 py-0.5 russo text-[10px] uppercase tracking-wide {{ $trv['is_premium'] ? 'bg-brand text-white' : 'bg-white text-main-dark/60' }}">
                                                {{ $trv['is_premium'] ? 'Premium' : 'Free' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-row justify-end gap-2">
                                                <a href="/trv/management/trivia/{{ $trv['id'] }}" class="fira-sans font-black uppercase text-xs tracking-wide bg-brand text-white border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-1.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Edit</a>
                                                <button type="button" class="fira-sans font-black uppercase text-xs tracking-wide bg-white text-main-accent border-4 border-main-dark shadow-[3px_3px_0_0_#14323B] px-4 py-1.5 transition-all duration-100 hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[5px_5px_0_0_#14323B] active:translate-x-0.5 active:translate-y-0.5 active:shadow-[0px_0px_0_0_#14323B]">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="bg-main-light border-4 border-dashed border-main-dark/40 px-5 py-10 text-center">
                        <p class="russo text-main-dark/60 text-xl">No trivias yet</p>
                        <p class="fira-sans text-main-dark/60 mt-1">Create your first quiz with one of the tools above.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

@endsection
