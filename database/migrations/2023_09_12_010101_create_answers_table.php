<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    public function up()
    {
        Schema::create('trv_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')
                ->references('id')
                ->on('trv_questions')
                ->onDelete('cascade');

            $table->string('answer', 255)->nullable();
            $table->boolean('is_correct')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_answers');
    }
}