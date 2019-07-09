<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{

    use SoftDeletes;

    protected $table = 'server';
    protected $connection = 'main';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'id',
        'code',
        'flag',
        'url',
        'active',
    ];

    /*
     * Verbindet die server Tabelle mit der world Tabelle
     */
    public function worlds()
    {
        return $this->hasMany('App\World', 'server_id', 'id');
    }

    public static function getServer(){
        $serverModel = new Server();
        return $serverModel->get();
    }

    public static function getServerByCode($code){
        return Server::where('code', $code)->first();
    }

    public static function getWorldsByCode($code){
        $server = Server::where('code', $code)->first();
        return $server->worlds;
    }
    
    public static function getQuery(){
        $serverModel = new Server();
        return $serverModel;
    }
}
