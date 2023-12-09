<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trv_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('original_answer_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trv_answers', function (Blueprint $table) {
            $table->dropColumn('original_answer_id');
        });
    }
};