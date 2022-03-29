<?php

namespace App;

class DsConnection extends CustomModel
{
    protected $fillable = [
        'user_id', 'world_id', 'player_id', 'key',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    protected $cache = [
        'world',
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function world(){
        return $this->belongsTo('App\World','world_id');
    }
}
