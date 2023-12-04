<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trv_answers', function (Blueprint $table) {
            $table->string('file_url')->nullable();
            $table->string('file_url_type')->nullable();
        });
    }

    public function down()
    {
        Schema::table('trv_answers', function (Blueprint $table) {
            $table->dropColumn('file_url');
            $table->dropColumn('file_url_type');
        });
    }
};