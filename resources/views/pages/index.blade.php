@extends('game-api::layout')
@section('body')
    <div class="flex flex-col  mt-2 px-12">
        <div class="flex flex-row">
            <h1 class="fira-sans font-semibold text-2xl">Trivia's by categories</h1>
            <hr>
        </div>
        <div class="flex flex-col bg-slate-200">
            @foreach($categories as $cat)
            <div class="">
                <div class="bg-slate-300 py-4 px-8 fira-sans shadow-md border-b border-b-slate-400">
                    <h2 class="text-semibold">{{ $cat['name'] }} <span class="text-normal">({{ $cat->trivia->count() }})</span></h2>
                    <p>{{ $cat['description'] }}</p>
                </div>
                <div class="flex flex-row">
                    @foreach($cat->load('trivia')->trivia as $trivia)
                        <div class="flex flex-row mx-2 py-4">
                            <div class="raleway flex flex-col justify-center px-4">{{ $trivia['title'] }}</div>
                            <div class="flex flex-col justify-center px-4">
                                {{ $trivia['description'] }}
                            </div>
                            <div class="flex flex-col justify-center px-4">
                                <div>
                                    Difficulty: <span class="capitalize text-semibold fira-sans @if($trivia['difficulty'] == 'medium') text-amber-700 @elseif($trivia['difficulty'] == 'hard') text-red-700 @else text-lime-600	 @endif">{{ $trivia['difficulty'] }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col justify-center px-4">
                                <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="startTriviaGame({{ $trivia['id'] }})">Play</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

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
    </script>
@endsection