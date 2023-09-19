<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('trv_questions', function (Blueprint $table) {
            $table->id('id');

            $table->unsignedBigInteger('trivia_id');
            $table->foreign('trivia_id')
                ->references('id')
                ->on('trv_trivia')
                ->onDelete('cascade');

            $table->text('question');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_questions');
    }
}