<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SubmitedAnswersTable extends Migration
{

    public function up()
    {
        Schema::create(config('prefix') . '_submited_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_instance_id');
            $table->integer('question_id');
            $table->integer('answer_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('prefix') . '_submited_answers');
    }

}