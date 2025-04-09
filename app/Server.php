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

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $fillable = [
        'code',
        'flag',
        'url',
        'active',
        'speed_active',
        'classic_active',
        'locale',
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
     * Schaut ob der Server existiert und falls ja gibt diesen zurück,
     * sonst wird ein Error 404 zurück gegeben
     *
     * @param string $server
     * @return World
     */
    public static function getAndCheckServerByCode($server){
        $serverData = Server::getServerByCode($server);
        abort_if($serverData == null, 404, __("ui.errors.404.noServer", ["server" => $server]));
        return $serverData;
    }

    /**
     * Gibt einen bestimmten Server zurück.
     *
     * @param string $code
     * @return $this
     */
    public static function getServerByCode($code){
        return Server::where('code', $code)->first();
    }

    /**
     * Gibt alle Welten eines bestimmten Server's zurück
     * falls der Server nicht existiert wird ein 404 geworfen
     *
     * @param string $code
     * @return Collection
     */
    public static function getAndCheckWorldsByCode($code){
        $server = static::getAndCheckServerByCode();
        $collect = $server->worlds;
        return $collect->sortByDesc('id');
    }

    /**
     * Gibt alle Welten eines bestimmten Server's zurück
     *
     * @param \App\Server $server
     * @return Collection
     */
    public static function getWorlds(Server $server){
        $collect = $server->worlds;
        return $collect->sortByDesc('id');
    }

    /**
     * @return $this
     */
    public static function getQuery(){
        $serverModel = new Server();
        return $serverModel;
    }
}
