<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A host-set coin prize pot for seasons and events. On completion the pot is
 * split 50/30/20 across the top three (registered) finishers and paid into the
 * shared game_currency ledger. 0 = no coin prize.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->unsignedBigInteger('coin_pot')->default(0)->after('prize');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('coin_pot')->default(0)->after('badge_name');
        });
    }

    public function down(): void
    {
        Schema::table('seasons', function (Blueprint $table) {
            $table->dropColumn('coin_pot');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('coin_pot');
        });
    }
};
