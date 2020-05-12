<?php

namespace App;

use App\Notifications\DiscordNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class Follow extends Model
{
    public $timestamps = false;
    protected $fillable =[
        'worlds_id'
    ];

    public static function conquereNotification($follow, $input){
        //TODO: Queues
        foreach($follow as $user){
            Notification::send(User::find($user->user_id), new DiscordNotification('conquere', null, $input));
            sleep(0.5);
        }
    }
}
