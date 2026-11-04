<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // host / owner
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('prize')->nullable();          // prize for the season's top player
            $table->string('code')->unique();             // shareable public leaderboard code
            $table->string('status')->default('active');  // active | completed
            $table->string('winner_user_id')->nullable(); // snapshot on completion
            $table->string('winner_name')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seasons');
    }
};
