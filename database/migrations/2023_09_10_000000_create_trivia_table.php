<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriviaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('trv_trivia', function (Blueprint $table) {
            $table->id();

            $table->string('title', 255);
            $table->string('category', 255);
            $table->string('difficulty', 255);
            $table->string('type', 255);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('trv_trivia');
    }
};