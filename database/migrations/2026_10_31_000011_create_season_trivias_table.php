<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The planned line-up of quizzes (rounds / weeks) for a season.
        Schema::create('season_trivias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id')->index();
            $table->unsignedBigInteger('trivia_id')->index();
            $table->integer('order_nr')->default(0);
            $table->timestamps();

            $table->unique(['season_id', 'trivia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('season_trivias');
    }
};
