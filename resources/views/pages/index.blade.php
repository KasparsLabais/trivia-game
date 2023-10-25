@extends('game-api::layout')
@section('body')
    <div class="flex flex-col  mt-2 px-4 md:px-12">
        <div class="flex flex-row">
            <h1 class="fira-sans font-semibold text-2xl">Trivia's by categories</h1>
            <hr>
        </div>
        <div class="flex flex-col bg-slate-200">
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
                    @foreach($cat->availableTrivia as $trivia)
                        <div class="hidden md:flex flex-row mx-2 py-4 border-b border-b-slate-300">
                            <div class="fire-sans font-semibold flex flex-col justify-center px-4 w-1/6">
                                {{ $trivia['title'] }}
                                <div class="font-normal fira-sans flex flex-row">
                                    @for( $i = 1; $i <= 5; $i++)
                                        <svg @if(Auth::check()) onclick="rateTrivia({{ $trivia->id }}, {{ $i + 1 }})" @endif xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="@if($i <= ($trivia->getRating())['rating'] && ($trivia->getRating())['rating'] != 0 ) fill-amber-500 stroke-amber-500 @endif w-6 h-6">
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
                                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="startTriviaGame({{ $trivia['id'] }})">Play</button>
                                @else
                                    <div>
                                        <span class="">You Need To Login To Play</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="md:hidden flex flex-row mx-2 py-4 border-b border-b-slate-300">
                            <div class="raleway flex flex-col justify-center px-4 w-3/6">
                                <span class="font-semibold ">{{ $trivia['title'] }} ({{ $trivia->questions->count() }})</span>
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
                                    <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="startTriviaGame({{ $trivia['id'] }})">Play</button>
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
    </div>

    @if(Auth::check())
    <div class="flex flex-col  mt-4 px-4 md:px-12">
        <div class="flex flex-row">
            <h1 class="fira-sans font-semibold text-2xl">Your Trivia's</h1>
            <hr>
        </div>
        <div class="flex flex-col bg-slate-100">
            <div class="flex flex-col px-2 py-4 bg-slate-200 shadow">
                <p class="font-semibold">Stats</p>
            </div>
            <div class="flex flex-row raleway py-2 px-2">
                <div>Total Trivia's: <span class="pr-4 pl-2">{{ $usersTrivias->count() }}</span></div>
                <div>Active Trivia's: <span class="pr-4 pl-2">{{ $usersTrivias->where('is_active', 1)->count() }}</span></div>
                <div>Total Times Played: <span class="pr-4 pl-2">0</span></div>
            </div>
            <div class="flex flex-row py-2 px-2">
                <a href="/trv/management" class="w-48 py-2 px-4 shadow-md bg-cyan-500 text-slate-100 font-semibold">Manage Trivia's</a>
            </div>
        </div>
    </div>
    @endif

    <script>
        function startTriviaGame(triviaId) {

            //create POST fetch request to /trv/trivia with passing triviaID in post body
            fetch('/trv/trivia', {
                'method': 'POST',
                'headers': {'Content-Type': 'application/json', 'X-CSRF-TOKEN' : '{{ csrf_token() }}' },
                'body': JSON.stringify({'trivia_id': triviaId})
            })
                .then(response => response.json())
                .then(data => {

                    if (data.status == false) {
                        alert(data.message);
                        return;
                    }
                    console.log(typeof GameApi)
                    console.log(GameApi);
                    GameApi.addGameInstance(data.data.token, data.data);

                    console.log(data);
                    window.location.href = '/trv/trivia/' + data.data.token;
                })
                .catch(error => console.log(error));

            /*
            fetch('/trv/trivia' + triviaId, {'method': 'POST', 'headers': {'Content-Type': 'application/json'}, 'data'})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    alert('Trivia Started');
                    //window.location.href = '/trv/' + triviaId;
                })
                .catch(error => console.log(error));

             */
        }

        function expandCategory(categoryId) {

            //hide expand button and show collapse button
            document.getElementById('expanded-cat-' + categoryId).classList.remove('hidden');
            document.getElementById('collapsed-cat-' + categoryId).classList.add('hidden');

            //show all trivia's in category
            document.getElementById('trivia-holder-' + categoryId).classList.remove('hidden');
        }

        function hideCategory(categoryId) {

            //hide collapse button and show expand button
            document.getElementById('expanded-cat-' + categoryId).classList.add('hidden');
            document.getElementById('collapsed-cat-' + categoryId).classList.remove('hidden');

            //hide all trivia's in category
            document.getElementById('trivia-holder-' + categoryId).classList.add('hidden');
        }

        function rateTrivia(triviaId, rating) {
            fetch('/trv/trivia/rating', {'method' : 'POST', 'body': JSON.stringify({'trivia_id' : triviaId, 'rating' : rating}), 'headers' : {'Content-Type' : 'application/json', 'X-CSRF-TOKEN' : '{{ csrf_token() }}'}})
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    if(data.success) {
                        alert(data.message);
                    } else {
                        alert(data.message);
                    }
                });
        }

    </script>
@endsection