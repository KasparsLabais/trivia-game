<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trv_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('original_question_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trv_questions', function (Blueprint $table) {
            $table->dropColumn('original_question_id');
        });
    }
};