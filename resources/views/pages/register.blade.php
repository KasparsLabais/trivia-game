@extends('game-api::layout')
@section('body')
    <div class="flex flex-row justify-center">
        <h1>{{ $response['message'] }} | @if($response['status']) <span class="text-lime-600 font-semibold">Registered</span> @else <span class="text-red-600 font-semibold">Error</span> @endif</h1>
    </div>
@endsection