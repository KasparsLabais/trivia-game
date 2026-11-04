<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Trivia Battles: a Random Quiz played together by invited friends, with no
// game master. The server fixes one start time and every question's window is
// derived from it, so all players see the same question at the same moment.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trv_quiz_battles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->unsignedBigInteger('tmp_trivia_id');
            $table->unsignedBigInteger('host_user_id');
            $table->string('status', 16)->default('lobby'); // lobby | running | finished
            $table->json('settings')->nullable();            // category / difficulty / question_count
            $table->unsignedSmallInteger('time_limit')->default(20);
            $table->unsignedSmallInteger('reveal_seconds')->default(5);
            $table->timestamp('starts_at')->nullable();      // first question starts here
            $table->timestamps();
        });

        Schema::create('trv_quiz_battle_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('battle_id');
            $table->unsignedBigInteger('user_id');
            $table->json('answers')->nullable();               // { order_nr: { given, correct } }
            $table->timestamps();
            $table->unique(['battle_id', 'user_id']);
            $table->index('battle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trv_quiz_battle_players');
        Schema::dropIfExists('trv_quiz_battles');
    }
};
