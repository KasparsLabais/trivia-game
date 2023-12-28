<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {

        Schema::create('scrabble_players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('game_id');
            $table->integer('points');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('scrabble_players');
    }



};