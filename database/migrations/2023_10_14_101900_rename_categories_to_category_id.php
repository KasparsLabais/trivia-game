<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCategoriesToCategoryId extends Migration
{
    public function up()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->renameColumn('categories', 'category_id');
        });
    }

    public function down()
    {
        Schema::table('trv_trivia', function (Blueprint $table) {
            $table->renameColumn('category_id', 'categories');
        });
    }
}