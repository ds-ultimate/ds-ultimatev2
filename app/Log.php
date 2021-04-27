<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Dummy model for the "system" user that will log all errors into a dedicated discord channel
 */
class Log extends Model
{
    public function routeNotificationFor()
    {
        return config('services.discord.bot_channel');
    }
}
