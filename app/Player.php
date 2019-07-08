<?php

namespace App;

use App\Util\BasicFunctions;

class Player extends CustomModel
{
    private $hash = 59;
    protected $primaryKey = 'playerID';
    protected $fillable =[
            'id', 'name', 'ally', 'village', 'points', 'rank', 'off', 'offR', 'def', 'defR', 'tot', 'totR',
    ];

    public $timestamps = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->hash = env('HASH_PLAYER', 59);

    }

    /**
     *@return int
     */
    public function getHash(){
        return $this->hash;
    }

    public function allyLatest()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'ally_id', 'allyID', $table[0].'.ally_latest');
    }

    public function allyChanges()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'player_id', 'playerID', $table[0].'.ally_changes');
    }

    public static function getAllPlayer($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->orderBy('rank')->get();
    }

    public static function getAllPlayer20($server, $world, $order, $page){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->where($order, '>', $page*20-20)->orderBy($order)->limit(20)->get();
    }

    /*
     * Gibt die Top 10 Spieler zurück
     * */
    public static function top10Player($server, $world){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->orderBy('rank')->limit(10)->get();

    }

    public static function player($server, $world, $player){
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        return $playerModel->find($player);

    }

    public static function playerDataChart($server, $world, $playerID){
        $tabelNr = $playerID % env('HASH_PLAYER');

        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_'.$tabelNr);

        $playerDataArray = $playerModel->where('playerID', $playerID)->orderBy('updated_at', 'DESC')->get();
        $playerDatas = collect();

        foreach ($playerDataArray as $player){
            $playerData = collect();
            $playerData->put('timestamp', (int)$player->updated_at->timestamp);
            $playerData->put('points', $player->points);
            $playerData->put('rank', $player->rank);
            $playerData->put('village', $player->village_count);
            $playerData->put('gesBash', $player->gesBash);
            $playerData->put('offBash', $player->offBash);
            $playerData->put('defBash', $player->defBash);
            $playerData->put('utBash', $player->gesBash-$player->offBash-$player->defBash);
            $playerDatas->push($playerData);
        }

        return $playerDatas;

    }
    
    public function nameWithAlly() {
        if ($this->ally_id != 0) {
            return $this->name . " [" . $this->allyLatest->tag . "]";
        }
        else {
            return $this->name . " [-]";
        }
    }
}
