<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrvOpenTriviasTable extends Migration
{
    public function up()
    {
        Schema::create('trv_open_trivias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trivia_id');
            $table->unsignedBigInteger('game_instance_id');
            $table->boolean('status')->default(1);
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_open_trivias');
    }
}