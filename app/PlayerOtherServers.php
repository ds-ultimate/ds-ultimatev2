<?php

namespace App;

use App\World;
use App\Util\BasicFunctions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PlayerOtherServers extends Model
{
    protected $primaryKey = 'playerID';
    protected $fillable = [
        'playerID',
        'name',
        'worlds'
    ];

    protected $casts = [
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public $timestamps = true;

    /**
     * Player constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
    
    public function getWorldIds() {
        if(!isset($this->worlds) || $this->worlds === null) return [];
        return explode(";", $this->worlds);
    }
    
    public function isWorldActive($worldID) {
        return in_array($worldID, $this->getWorldIds());
    }
    
    public function addWorld($worldID) {
        if($this->isWorldActive($worldID)) return;
        if(!isset($this->worlds) || $this->worlds === null) {
            $this->worlds = $worldID;
        } else {
            $curWorlds = $this->getWorldIds();
            $curWorlds[] = $worldID;
            sort($curWorlds);
            $this->worlds = "";
            $first = true;
            foreach($curWorlds as $world) {
                if(!$first) $this->worlds .= ";";
                $this->worlds .= $world;
                $first = false;
            }
        }
    }
    
    public function removeWorld($worldID) {
        if(! $this->isWorldActive($worldID)) return;
        
        $curWorlds = $this->getWorldIds();
        $this->worlds = null;
        $first = true;
        foreach($curWorlds as $w) {
            if($w == $worldID) {
                continue;
            }
            if(!$first) $this->worlds .= ";";
            $this->worlds .= $w;
            $first = false;
        }
    }

    public function massRemoveWorlds($worlds) {
        $curWorlds = $this->getWorldIds();
        $filtered = array_diff($curWorlds, $worlds);
        $this->worlds = null;
        if(count($filtered) > 0) {
            $this->worlds = implode(";", $filtered);
        }
    }
    
    public function getWorlds() {
        $worldIds = $this->getWorldIds();
        return (new World())->query()->whereIn("id", $worldIds)->get();
    }

    /**
     * @param string $server
     * @return $this
     */
    public static function prepareModel($server){
        $playerModel = new PlayerOtherServers();
        $playerModel->setTable('other_servers_' . $server->code);
        return $playerModel;
    }

    /**
     * @param string $server
     * @param int $player
     * @return $this
     */
    public static function player($server, $player){
        return static::prepareModel($server)->find($player);
    }
}
