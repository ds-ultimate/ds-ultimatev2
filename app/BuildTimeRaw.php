<?php

namespace App;

class BuildTimeRaw extends CustomModel
{
    protected $connection = 'mysql';
    protected $table = 'buildtimesraw';

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $fillable = [
        'id',
        'building',
        'level',
        'wood',
        'clay',
        'iron',
        'buildtime',
        'pop',
        'mainLevel',
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
