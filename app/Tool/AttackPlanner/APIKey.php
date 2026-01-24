<?php
/**
 * Created by IntelliJ IDEA.
 * User: crams
 * Date: 18.08.2019
 * Time: 16:05
 */

namespace App\Tool\AttackPlanner;


use App\CustomModel;

class APIKey extends CustomModel
{
    protected $table = "attackplanner_api_keys";

    protected $fillable = [
        'discord_name',
        'discord_id',
        'key',
    ];

    protected $hidden = [
        'key',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
