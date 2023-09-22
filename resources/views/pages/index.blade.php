@extends('game-api::layout')
@section('body')
    <div class="mt-2 px-12 flex flex-col">
        <div class="bg-slate-300 px-6 py-8">
            @foreach($allTrivia as $trivia)
                <div class="flex flex-row py-2">
                    <h2>{{ $trivia['title'] }}</h2>
                    <div class="mx-2">
                        <button class="py-2 px-4 shadow-md bg-lime-500 text-slate-100 font-semibold" onclick="startTriviaGame({{ $trivia['id'] }})">Play</button>
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
                'headers': {'Content-Type': 'application/json'},
                'body': JSON.stringify({'trivia_id': triviaId})
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    alert('Trivia Started');
                    //window.location.href = '/trv/' + triviaId;
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