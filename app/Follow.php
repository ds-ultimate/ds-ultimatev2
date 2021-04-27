<?php

namespace App;

use App\Notifications\DiscordNotificationQueueElement;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public $timestamps = false;
    protected $fillable =[
        'worlds_id',
        'followable_id',
        'followable_type',
    ];

    public static function conquereNotification($follows, $worldUpdate, $conquerArr){
        foreach($follows as $follow){
            DiscordNotificationQueueElement::conquere($follow->user_id, $worldUpdate, $conquerArr);
        }
    }
}
