<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DsConnection extends Model
{
    protected $fillable = [
        'user_id', 'world_id', 'player_id', 'key',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

    public function world(){
        return $this->belongsTo('App\World','world_id');
    }
}
