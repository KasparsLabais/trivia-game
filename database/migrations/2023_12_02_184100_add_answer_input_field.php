<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trv_submitted_answers', function (Blueprint $table) {
            $table->string('answer_custom_input')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trv_submitted_answers', function (Blueprint $table) {
            $table->dropColumn('answer_custom_input');
        });
    }
};