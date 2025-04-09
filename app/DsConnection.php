<?php

namespace App;

class DsConnection extends CustomModel
{
    protected $fillable = [
        'user_id', 'world_id', 'player_id', 'key',
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
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
