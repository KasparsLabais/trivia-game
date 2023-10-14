@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col">
            <div>
                <h1>Winner</h1>
                <hr>
                {{ $winners['winner']->user->username }} | Points: {{ $winners['winner']['points'] }}
            </div>
            <div>
                <h1>Runner Up</h1>
                @foreach($winners['winners'] as $runnerUp)
                    @if($runnerUp->user->id != $winners['winner']->user->id)
                        <hr>
                        {{ $runnerUp->user->username }} | Points: {{ $runnerUp['points'] }}
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection