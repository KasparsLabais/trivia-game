<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrvTmpQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('trv_tmp_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->integer('order_nr')->nullable();

            $table->unsignedBigInteger('original_question_id');
            $table->unsignedBigInteger('tmp_trivia_id');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_tmp_questions');
    }
}