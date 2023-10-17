<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToTrvTriviaTable extends Migration
{
    public function up()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
        });
    }

    public function down()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
}