@extends('game-api::layout')
@section('body')

    <x-section title="Preparing Your Game">
        <x-card title="{{ $tmpTrivia['title'] }}" addHeader="{{ true }}">
            <h1>Your game will be ready in a moment...</h1>
        </x-card>
    </x-section>


    <script>

        //{"title":"History","game_id":1,"user_id":1,"status":"created","token":"JmUrQ4sGtiNJKjr","remote_data":"{\"trivia_id\":4,\"is_temporary\":0}
        function startTmpTriviaGame() {
            GameApi.addGameInstance('{{ $token }}', { 'title': '{{ $gameInstance['title'] }}', 'game_id' : {{ $gameInstance['game_id'] }}, 'user_id': {{ $gameInstance['user_id'] }}, 'status' : '{{ $gameInstance['status'] }}', 'token': '{{ $gameInstance['token'] }}', 'remote_data': { @foreach($remoteData as $key => $val) '{{ $key }}' : '{{ $val }}',  @endforeach}  });
            window.location.href = '/trv/trivia/{{$token}}';
        }

        $(document).ready(function() {
            setTimeout(startTmpTriviaGame, 5000);
        })

    </script>
@endsection