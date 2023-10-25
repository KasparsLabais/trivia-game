<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrvTriviaRatingsTable extends Migration
{

    public function up()
    {
        Schema::create('trv_trivia_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('trivia_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('rating')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_trivia_ratings');
    }
}