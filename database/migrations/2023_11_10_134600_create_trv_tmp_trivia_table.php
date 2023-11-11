<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrvTmpTriviaTable extends Migration
{
    public function up()
    {
        Schema::create('trv_tmp_trivia', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->unsignedBigInteger('category_id');
            $table->string('difficulty');
            $table->boolean('private')->default(false);
            $table->integer('question_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('trv_tmp_trivia');
    }

}