<?php

namespace PartyGames\TriviaGame\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PartyGames\TriviaGame\Models\Scrabble;
use PartyGames\TriviaGame\Models\ScrabblePlayers;

class ScrabbleController
{
    public function index()
    {
        return view('trivia-game::scribble.index');
    }

    public function createGame(Request $request)
    {
        $game = new Scrabble();
        $game->name = $request->name;
        $game->token = Str::random(6);
        $game->save();


        $playersList = explode(',', $request->players);
        foreach ($playersList as $player) {
            $player = trim($player);
            $game->players()->create([
                'name' => $player,
                'points' => 0,
            ]);
        }

        return redirect('/scrabble/' . $game->token);

        /*
        return response()->json([
            'status' => 'success',
            'game' => $game,
        ]);
        */
    }

    public function game($token)
    {
        $game = Scrabble::where('token', $token)->first();
        return view('trivia-game::scribble.game', [
            'game' => $game,
            'players' => $game->players,
        ]);
    }

    public function addPlayersPoints(Request $request)
    {
        //$game = Scrabble::where('token', $request->gameToken)->first();
        //$player = $game->players()->where('id', $request->playerId)->first();
        $player = ScrabblePlayers::where('id', $request->player_id)->first();
        $player->points = $player->points + $request->points;
        $player->save();


        return redirect()->back();
        /*
        return response()->json([
            'status' => 'success',
            'message' => 'Points added',
        ]);
        */
    }

    public function removePlayersPoints(Request $request) {

        $player = ScrabblePlayers::where('id', $request->player_id)->first();
        $player->points = $player->points - $request->points;
        $player->save();

        return redirect()->back();
    }
}