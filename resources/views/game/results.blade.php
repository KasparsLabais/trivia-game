@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <div class="flex flex-col w-5/6 md:w-1/2">
            <div class="flex flex-col justify-center">
                <h1 class="text-center fire-sans font-semibold text-4xl py-4">Winner</h1>
                <hr>
                <div class="flex flex-row justify-center py-4">
                    <div class="flex flex-col justify-center text-center">
                        <div class="flex flex-row justify-center">
                            <img class="w-32 rounded-lg shadow-lg border-yellow-400	border-4" src="@if(is_null($winners['winner']->user->avatar)) /images/default-avatar.jpg @else{{ $winners['winner']->user->avatar }}@endif" alt="Game Image"/>
                        </div>
                        <span class="raleway text-2xl pt-4">{{ $winners['winner']->user->username }}</span>
                        <span class="raleway text-2xl font-semibold">{{ $winners['winner']['points'] }} points</span>
                    </div>
                </div>
            </div>
            <div>
                <h1>Runner Up's</h1>
                <div class="flex flex-row flex-row justify-center md:justify-start">
                    @foreach($winners['winners'] as $runnerUp)
                        @if($runnerUp->user->id != $winners['winner']->user->id)
                            <div class="flex flex-col w-1/2 md:w-1/6 px-2">
                                <div class="flex flex-col bg-slate-100 shadow-lg rounded-md py-2">
                                    <div class="flex flex-row justify-center">
                                        <img class="w-20 h-20 rounded-lg shadow border-2 border-slate-400" src="@if(is_null($runnerUp->user->avatar)) /images/default-avatar.jpg @else{{ $runnerUp->user->avatar }}@endif" alt="Game Image"/>
                                    </div>
                                    <span class="text-center raleway">
                                        {{ $runnerUp->user->username }}
                                    </span>
                                    <span class="text-center raleway">
                                        {{ $runnerUp['points'] }} Points
                                    </span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection