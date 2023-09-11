<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create(config('prefix') . '_questions', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('trivia_id');
            $table->foreign('trivia_id')
                ->references('id')
                ->on(config('prefix') . '_trivia')
                ->onDelete('cascade');

            $table->text('question');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('prefix') . '_questions');
    }
}