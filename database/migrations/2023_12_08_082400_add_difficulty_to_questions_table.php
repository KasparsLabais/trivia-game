<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('difficulty', 20)->default('easy');
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('difficulty');
        });
    }
};