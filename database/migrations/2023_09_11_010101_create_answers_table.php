<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnswersTable extends Migration
{
    public function up()
    {
        Schema::create(config('prefix') . '_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->foreign('question_id')
                ->references('id')
                ->on(config('prefix') . '_questions')
                ->onDelete('cascade');

            $table->string('answer', 255)->nullable();
            $table->boolean('is_correct')->default(false);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('prefix') . '_answers');
    }
}