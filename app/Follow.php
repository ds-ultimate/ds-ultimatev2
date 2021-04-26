<?php

namespace App;

use App\Notifications\DiscordNotificationQueueElement;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    public $timestamps = false;
    protected $fillable =[
        'worlds_id'
    ];

    public static function conquereNotification($follow, $worldUpdate, $conquerArr){
        foreach($follow as $user){
            DiscordNotificationQueueElement::conquerNotification(User::find($user->user_id), $worldUpdate, $conquerArr);
        }
    }
}
