<?php

namespace PartyGames\TriviaGame\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationAction extends Model
{
    protected $table = 'trv_notification_actions';

    protected $fillable = [
        'message',
        'level',
        'colour',
    ];
}
