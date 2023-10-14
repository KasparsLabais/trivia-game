<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeForCategoryId extends Migration
{
    public function up()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->change();
        });
    }

    public function down()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->renameColumn('category_id', 'categories');
        });
    }
}