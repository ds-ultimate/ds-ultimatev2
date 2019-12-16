<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $fillable = [
        'user_id',
        'birthday',
        'show_birthday',
        'skype',
        'show_skype',
        'discord_id',
        'show_discord',
        'github_id',
        'facebook_id',
        'google_id',
        'twitter_id',
        'created_at',
        'updated_at',
    ];

    public function checkOauth($driver){
        $name = $driver.'_id';
        return isset($this->$name);
    }

}
