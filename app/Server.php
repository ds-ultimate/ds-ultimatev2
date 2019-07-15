<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Server extends Model
{

    use SoftDeletes;

    protected $table = 'server';
    protected $connection = 'mysql';

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

    /**
     * Verbindet die server Tabelle mit der world Tabelle
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function worlds()
    {
        return $this->hasMany('App\World', 'server_id', 'id');
    }

    /**
     * Gibt alle Server zurück
     *
     * @return Collection
     */
    public static function getServer(){
        $serverModel = new Server();
        return $serverModel->get();
    }

    /**
     * Giebt einen bestimmten Server zurüch.
     *
     * @param string $code
     * @return $this
     */
    public static function getServerByCode($code){
        return Server::where('code', $code)->first();
    }

    /**
     * Gibt alle Welten eines bestimmten Server's zurück
     *
     * @param string $code
     * @return Collection
     */
    public static function getWorldsByCode($code){
        $server = Server::where('code', $code)->first();
        $collect = $server->worlds;
        return $collect->sortBy('name');
    }

    /**
     * @return $this
     */
    public static function getQuery(){
        $serverModel = new Server();
        return $serverModel;
    }
}
