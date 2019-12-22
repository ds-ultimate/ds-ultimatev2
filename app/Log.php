<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function routeNotificationFor()
    {
        return config('services.discord.bot_channel');
    }
}
