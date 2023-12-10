<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trv_tmp_questions', function (Blueprint $table) {
            $table->string('question_type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trv_tmp_questions', function (Blueprint $table) {
            $table->dropColumn('question_type');
        });
    }

};