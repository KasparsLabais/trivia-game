<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsTemporaryColumnToOpenTriviaTable extends Migration
{
    public function up()
    {
        Schema::table('trv_open_trivias', function (Blueprint $table) {
            $table->boolean('is_temporary')->default(false);
        });
    }

    public function down()
    {
        Schema::table('trv_open_trivias', function (Blueprint $table) {
            $table->dropColumn('is_temporary');
        });
    }
}