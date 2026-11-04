<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Actual game instances played under a season. The season leaderboard
        // aggregates player points across every game linked here.
        Schema::create('season_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id')->index();
            $table->unsignedBigInteger('game_instance_id')->index();
            $table->unsignedBigInteger('trivia_id')->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'game_instance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_games');
    }
};
