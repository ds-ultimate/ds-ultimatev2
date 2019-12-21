<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuildTimeRaw extends Model
{
    protected $connection = 'mysql';
    protected $table = 'buildtimesraw';

    protected $dates = [
        'created_at',
        'updated_at',
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
    
    public function world(){
        return $this->belongsTo('App\World','world_id');
    }
    
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }
}
