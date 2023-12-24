@extends('game-api::layout')
@section('body')

    <div class="flex flex-col justify-center py-4">
        <div class="flex flex-col mt-10">
            <h1 class="text-center josefin-sans font-semibold text-6xl text-yellow-500">Winner</h1>
            <p class="text-center text-slate-500 text-2xl font-semibold">{{ $winners['winner']['points'] }} Points</p>
        </div>

        <div class="flex flex-row justify-center">
            <div class="flex flex-row py-2 px-2 w-2/4">
                <div class="flex flex-col w-full bg-zinc-700 rounded shadow-zinc-700">
                    <div class="flex flex-row w-full justify-center relative rounded bg-yellow-500 h-20">
                        <img class="w-28 h-28 rounded-full shadow-lg absolute border-4 border-zinc-700 -bottom-9" src="@if(is_null($winners['winner']['avatar'])) /images/default-avatar.jpg @else{{ $winners['winner']['avatar'] }}@endif" alt="Game Image"/>
                    </div>
                    <div class="flex flex-col px-2 py-2 justify-end" style="overflow-wrap: anywhere;">
                        <div class="raleway text-center text-slate-200 font-bold text-xl mt-10">{{ $winners['winner']['username'] }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class=" bg-zinc-700 py-4">

        <div class="flex flex-col py-2 w-full">
            <p class="text-center text-slate-500 text-3xl font-semibold">Runners Up</p>
        </div>

        <div class="flex flex-row justify-around">
            @if(!empty($winners['second']))
                <div class="w-2/4 py-2 px-2">
                    <div class="flex flex-col">
                        <h2 class="josefin-sans font-semibold text-2xl text-gray-400 text-center">2nd Place</h2>
                        <p class="text-center text-slate-500 text-lg font-semibold">{{ $winners['second']['points'] }} Points</p>
                    </div>
                    <div class="flex flex-row justify-center w-full">
                        <div class="flex flex-row w-full">
                            <div class="flex flex-col w-full bg-gray-700 rounded shadow-md">
                                <div class="flex flex-row w-full justify-center relative rounded bg-gray-400 h-20">
                                    <img class="w-28 h-28 rounded-full shadow-lg absolute border-4 border-zinc-700 -bottom-9" src="@if(is_null($winners['second']['avatar'])) /images/default-avatar.jpg @else{{ $winners['winner']['avatar'] }}@endif" alt="Game Image"/>
                                </div>
                                <div class="flex flex-col px-2 py-2 justify-end" style="overflow-wrap: anywhere;">
                                    <div class="raleway text-center text-slate-200 font-bold text-xl mt-8">{{ $winners['second']['username'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($winners['third']))
                <div class="w-2/4 py-2 px-2 ">
                    <div class="flex flex-col">
                        <h2 class="josefin-sans font-semibold text-2xl text-amber-700 text-center">3rd Place</h2>
                        <p class="text-center text-slate-500 text-lg font-semibold">{{ $winners['third']['points'] }} Points</p>
                    </div>

                    <div class="flex flex-row justify-center w-full">
                        <div class="flex flex-row w-full">
                            <div class="flex flex-col w-full bg-gray-700 rounded shadow-md">
                                <div class="flex flex-row w-full justify-center relative rounded bg-amber-700 h-20">
                                    <img class="w-28 h-28 rounded-full shadow-lg absolute border-4 border-zinc-700 -bottom-9" src="@if(is_null($winners['third']['avatar'])) /images/default-avatar.jpg @else{{ $winners['winner']['avatar'] }}@endif" alt="Game Image"/>
                                </div>
                                <div class="flex flex-col px-2 py-2 justify-end" style="overflow-wrap: anywhere;">
                                    <div class="raleway text-center text-slate-200 font-bold text-xl mt-8">{{ $winners['third']['username'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>


    <div class="flex flex-col justify-around bg-zinc-800 py-4">
        <div class="flex flex-col py-2">
            <p class="text-center text-slate-500 text-2xl font-semibold">Happy To Participate</p>
        </div>
        <div class="flex flex-row">
            @foreach($winners['winners'] as $runnerUp)
                <div class="flex flex-col w-1/3">
                    <div class="flex flex-row justify-center">
                        <div class="flex flex-col w-5/6">
                            <div class="flex flex-col w-full bg-gray-600 rounded shadow-md">
                                <div class="flex flex-row w-full justify-center relative rounded bg-gray-700 h-14">
                                    <img class="w-20 h-20 rounded-full shadow-lg absolute border-4 border-zinc-700 -bottom-9" src="@if(is_null($runnerUp['avatar'])) /images/default-avatar.jpg @else{{ $winners['winner']['avatar'] }}@endif" alt="Game Image"/>
                                </div>
                                <div class="flex flex-col px-1 py-2 justify-end" style="overflow-wrap: anywhere;">
                                    <div class="raleway text-center text-slate-200 font-bold text-normal mt-6">{{ $runnerUp['username'] }}</div>
                                    <div class="raleway text-center text-slate-400 font-bold text-sm">{{ $runnerUp['points'] }} Points</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

@endsection