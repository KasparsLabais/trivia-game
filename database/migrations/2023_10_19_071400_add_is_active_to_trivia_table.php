<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveToTriviaTable extends Migration
{
    public function up()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
        });
    }

    public function down()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}