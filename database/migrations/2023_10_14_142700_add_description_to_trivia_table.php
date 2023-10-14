<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToTriviaTable extends Migration
{
    public function up()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->string('description')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
}