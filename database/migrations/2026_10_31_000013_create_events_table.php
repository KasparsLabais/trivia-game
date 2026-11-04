<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A branded one-off game with a custom winner trophy (Tier-3).
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();   // host
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('trivia_id')->nullable();
            $table->unsignedBigInteger('game_instance_id')->nullable();
            $table->string('accent_color')->default('#E62727'); // branded display accent
            $table->string('logo_path')->nullable();            // branded display logo
            $table->string('badge_name')->nullable();           // winner trophy label
            $table->string('badge_path')->nullable();           // winner trophy image
            $table->unsignedBigInteger('achievement_id')->nullable(); // the awarded badge definition
            $table->string('status')->default('draft');         // draft | live | completed
            $table->string('winner_user_id')->nullable();
            $table->string('winner_name')->nullable();
            $table->timestamp('awarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
