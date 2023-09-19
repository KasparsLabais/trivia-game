<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmittedAnswerTable extends Migration
{

    public function up()
    {
        Schema::create('trv_submitted_answers', function (Blueprint $table) {
            $table->id('id');
            $table->integer('game_instance_id');
            $table->integer('question_id');
            $table->integer('answer_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_submitted_answers');
    }

}