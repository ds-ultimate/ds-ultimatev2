<?php

namespace App;

class BuildTime extends CustomModel
{
    protected $connection = 'mysql';
    protected $table = 'buildtimes';

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'rawdata',
        'booster',
        'user_id',
        'world_id',
    ];
    
    protected $cache = [
        'world',
    ];
    
    public function world(){
        return $this->belongsTo('App\World','world_id');
    }
    
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
