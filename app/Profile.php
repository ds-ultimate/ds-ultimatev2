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
        'discord',
        'show_discord',
        'created_at',
        'updated_at',
    ];
}
