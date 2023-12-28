<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {

        Schema::create('scrabble', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('token');
            //$table->string('word');
            //$table->string('letters');
            //$table->string('points');
            $table->timestamps();
        });

    }

    public function down()
    {
        Schema::dropIfExists('scrabble');
    }

};