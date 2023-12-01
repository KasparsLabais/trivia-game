<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trv_questions', function (Blueprint $table) {
            $table->string('question_type')->default('options');
        });
    }

    public function down()
    {
        Schema::table('trv_questions', function (Blueprint $table) {
            $table->dropColumn('question_type');
        });
    }
};