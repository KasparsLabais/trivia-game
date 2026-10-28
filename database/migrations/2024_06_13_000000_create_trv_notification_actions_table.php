<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTrvNotificationActionsTable extends Migration
{
    public function up()
    {
        Schema::create('trv_notification_actions', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            // Importance level: info, warning, fatal, other.
            $table->string('level')->default('info');
            // CSS colour used to render the message for receivers.
            $table->string('colour')->default('#2563eb');
            $table->timestamps();
        });

        DB::table('trv_notification_actions')->insert([
            ['message' => 'Hurry up!', 'level' => 'fatal', 'colour' => '#dc2626', 'created_at' => now(), 'updated_at' => now()],
            ['message' => 'We are taking a break', 'level' => 'info', 'colour' => '#2563eb', 'created_at' => now(), 'updated_at' => now()],
            ['message' => 'Everyone is waiting.', 'level' => 'warning', 'colour' => '#ca8a04', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('trv_notification_actions');
    }
}
