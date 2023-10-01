<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddQuestionOrderNrFieldToQuestionsTable extends Migration
{

    public function up()
    {
        Schema::table('trv_questions', function (Blueprint $table) {
            $table->integer('order_nr')->after('trivia_id');
        });
    }

    public function down()
    {
        Schema::table('trv_questions', function (Blueprint $table) {
            $table->dropColumn('order_nr');
        });
    }

}