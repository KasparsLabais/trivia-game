
<x-section title="Available Trivia's">
    <div>
        <!-- navigation between categories and open trivia's -->
        <div class="flex flex-row">
            <div id="all-categories-nav" onclick="selectedBox('categories')" class="bg-slate-300 px-2 py-2 shadow-md">
                All Categories
            </div>
            <div id="open-trivias-nav" onclick="selectedBox('opentrivias')" class="bg-slate-100 px-2 py-2 shadow-md">
                Public Access Trivia's
            </div>
        </div>
    </div>
    <div id="category-holder" class="flex flex-col bg-slate-200">
        @foreach($categories as $cat)
            <div class="">
                <div class="flex flex-row justify-between bg-slate-300 fira-sans shadow-md border-b @if($cat->availableTrivia->count() == 0) border-b-slate-400 @else border-b-2 border-b-lime-500 @endif">
                    <div class="flex flex-row">
                        <div class="shadow-md px-4 py-2 @if($cat->availableTrivia->count() == 0) bg-stone-300	 @else shadow-lime-500 bg-lime-500 @endif">
                            <span class="@if($cat->availableTrivia->count() > 0) font-semibold @endif">{{ $cat->availableTrivia->count() }}</span>
                        </div>
                        <div class="flex flex-col justify-center">
                            <h2 class="text-semibold px-2">{{ $cat['name'] }}</h2>
                        </div>
                    </div>
                    <div class="flex flex-row">
                        <div onclick="hideCategory({{ $cat['id'] }})" class="hidden flex flex-row" id="expanded-cat-{{$cat['id']}}">
                            <div class="flex flex-col justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-rose-600 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div class="flex flex-col justify-center">
                                <button class="px-4">Hide All</button>
                            </div>
                        </div>
                        <div onclick="expandCategory({{ $cat['id'] }})" class="flex flex-row"  id="collapsed-cat-{{$cat['id']}}">
                            <div class="flex flex-col justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-slate-600 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                            <div class="flex flex-col justify-center">
                                <button class="px-4">Show All</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="trivia-holder-{{$cat['id']}}" class="hidden flex flex-col transition-all duration-200">

                    @if($cat->availableTrivia->count() > 0 && Auth::check())
                        <div class="flex flex-row mx-2 py-4 px-4 border-b border-b-slate-300">
                            <div>
                                <x-btn-alternative isALink="{{ false }}" onClick="openRandomTriviaModal({{ $cat['id'] }}, '{{ $cat['name'] }}')" type="button">Play Random Quiz</x-btn-alternative>
                                <p>We will auto generate trivia from all available questions in this category</p>
                            </div>
                        </div>
                    @endif

                    @foreach($cat->availableTrivia as $trivia)
                        <div class="hidden md:flex flex-row mx-2 py-4 border-b border-b-slate-300">
                            <div class="fire-sans font-semibold flex flex-col justify-center px-4 w-1/6">
                                <span>{{ $trivia['title'] }} @if($trivia['private']) - <span class="text-rose-600">Private</span> @endif </span>
                                <div class="font-normal fira-sans flex flex-row">
                                    @for( $i = 1; $i <= 5; $i++)
                                        <svg @if(Auth::check()) onclick="rateTrivia({{ $trivia->id }}, {{ $i }})" @endif xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="@if($i <= ($trivia->getRating())['rating'] && ($trivia->getRating())['rating'] != 0 ) fill-amber-500 stroke-amber-500 @endif w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                        </svg>
                                    @endfor
                                    ({{ ($trivia->getRating())['count'] }})
                                </div>
                                <div class="flex flex-row fira-sans"><span class="font-normal">Author: </span><span class="font-semibold">&nbsp;{{ $trivia->user->username }}</span></div>
                            </div>
                            <div class="flex flex-col justify-center px-4 w-2/6">
                                {{ Str::limit($trivia['description'], 50, '...') }}
                            </div>
                            <div class="flex flex-col justify-center px-4 w-1/6">
                                <div>
                                    Difficulty: <span class="capitalize text-semibold fira-sans @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600	 @endif">{{ $trivia['difficulty'] }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold">{{ $trivia->questions->count() }}</span> Questions
                                </div>
                            </div>
                            <div class="flex flex-col justify-center px-4 w-2/6">

                                @if(Auth::check())
                                    @if($trivia->is_premium && (Auth::user()->id != $trivia->user_id) )
                                        <x-btn-premium isALink="{{ true  }}" onClick="" type="submit" link="/premium">Get Premium</x-btn-premium>
                                    @else
                                        <x-btn-primary isALink="{{ false  }}" onClick="startTriviaGame({{ $trivia['id'] }})" type="submit" link="">Play</x-btn-primary>
                                    @endif
                                @else
                                    <div>
                                        <span class="">You Need To Login To Play</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="md:hidden flex flex-row mx-2 py-4 border-b border-b-slate-300">
                            <div class="raleway flex flex-col justify-center px-4 w-3/6">
                                <span class="font-semibold ">{{ $trivia['title'] }} ({{ $trivia->questions->count() }})   @if($trivia['private']) <span class="text-rose-600">Private</span> @endif </span>
                                <div class="font-normal fira-sans flex flex-row">
                                    @for( $i = 0; $i < 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="@if($i <= ($trivia->getRating())['rating'] && ($trivia->getRating())['rating'] != 0 ) fill-amber-500 stroke-amber-500 @endif w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                        </svg>
                                    @endfor
                                    ({{ ($trivia->getRating())['count'] }})
                                </div>
                                <div class="flex flex-row"><span class="font-normal">Author: </span><span class="font-semibold">&nbsp;{{ $trivia->user->username }}</span></div>
                            </div>
                            <div class="flex flex-col justify-center px-4 w-3/6">
                                <div>
                                    Difficulty: <span class="capitalize text-semibold fira-sans @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600	 @endif">{{ $trivia['difficulty'] }}</span>
                                </div>
                                <div>
                                    <span  class="font-semibold">{{ $trivia->questions->count() }}</span> Questions
                                </div>
                                @if(Auth::check())
                                    @if($trivia->is_premium && (Auth::user()->id != $trivia->user_id) )
                                        <x-btn-premium isALink="{{ true  }}" onClick="" type="submit" link="/premium">Get Premium</x-btn-premium>
                                    @else
                                        <x-btn-primary isALink="{{ false  }}" onClick="startTriviaGame({{ $trivia['id'] }})" type="submit" link="">Play</x-btn-primary>
                                    @endif
                                @else
                                    <div>
                                        <div>
                                            <span class="">You Need To Login To Play</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    <div id="open-trivias-holder" class="hidden flex flex-col bg-slate-300">
        @foreach($openTrivias as $trivia)
            <div>
                <div class="py-4 px-2 flex flex-row justify-between bg-slate-300 fira-sans shadow-md border-b rounded border-b-slate-400">
                        <span class="flex flex-col px-4 w-3/6">
                            <span class="font-semibold">{{ $trivia->trivia->title }}</span>
                            <span class="hidden md:flex">{{ $trivia->trivia->description }}</span>
                            <span class="flex md:hidden">{{ Str::limit($trivia->trivia->description, 30, '...') }}</span>
                        </span>
                    <div class="flex flex-col">
                        <span>Date Created: {{ $trivia->created_at->format('d/m/Y') }}</span>
                        <span>Time: {{ $trivia->created_at->format('H:i') }}</span>
                    </div>
                </div>
                <div class="flex flex-col md:flex-row justify-between py-4 border-b border-b-slate-300 bg-slate-200 px-2 fira-sans font-normal">
                    <div class="flex flex-row w-full md:w-5/6">
                        <div class="hidden md:flex">
                            <div class="px-4 flex flex-col justify-center">
                                <div>
                                    Created By: <span class="font-semibold">{{ $trivia->user->username }}</span>
                                </div>
                            </div>
                            <div class="px-4 flex flex-col justify-center">
                                <div>
                                    Difficulty: <span class="font-semibold capitalize @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600 @endif">{{ $trivia->trivia->difficulty }}</span>
                                </div>
                            </div>
                            <div class="px-4 flex flex-col justify-center">
                                <div>
                                    <span class="font-semibold">{{ $trivia->trivia->questions->count() }}</span> Questions
                                </div>
                            </div>
                            <div class="px-4 flex flex-col justify-center">
                                <div>
                                    <span class="font-semibold">{{ $trivia->gameInstance->playerInstances->count() }} / @if((int)GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit_enabled') == 1 ) {{ GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit') }} @else &infin; @endif</span> Players
                                </div>
                            </div>
                        </div>
                        <div class="flex md:hidden">
                            <div class="px-4 flex flex-col justify-center w-2/6">
                                <div>
                                    Created By: <span class="font-semibold">{{ $trivia->user->username }}</span>
                                </div>
                            </div>
                            <div class="px-4 flex flex-col justify-center w-2/6">
                                <div>
                                    Difficulty: <span class="font-semibold capitalize @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600 @endif">{{ $trivia->trivia->difficulty }}</span>
                                </div>
                            </div>
                            <div class="px-4 flex flex-col justify-center w-2/6">
                                <div>
                                    <span class="font-semibold">{{ $trivia->trivia->questions->count() }}</span> Questions
                                </div>
                                <div>
                                    <span class="font-semibold">{{ $trivia->gameInstance->playerInstances->count() }} / @if((int)GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit_enabled') == 1 ) {{ GameApi::getGameInstanceSettings($trivia->gameInstance->token, 'player_limit') }} @else &infin; @endif </span> Players
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row w-full md:w-1/6">
                        @if(Auth::check())
                            <x-btn-primary isALink="{{ true }}"  link="/join/{{ $trivia->gameInstance->token }}">Join</x-btn-primary>
                        @else
                            <span>You Need To Login To Play</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-section>
