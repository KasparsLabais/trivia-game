<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('answer_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('answer_id');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending');
            $table->integer('user_id')->nullable();
            $table->integer('admin_id')->nullable();
            $table->string('admin_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('answer_reports');
    }
};