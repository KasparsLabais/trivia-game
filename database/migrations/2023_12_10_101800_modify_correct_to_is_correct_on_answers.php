<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->renameColumn('correct', 'is_correct');
        });
    }

    public function down()
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->renameColumn('is_correct', 'correct');
        });
    }
};