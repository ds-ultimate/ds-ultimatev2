<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BuildTime extends Model
{
    protected $connection = 'mysql';
    protected $table = 'buildtimes';

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $fillable = [
        'id',
        'rawdata',
        'booster',
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
