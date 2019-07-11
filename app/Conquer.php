<?php

namespace App;

use App\Util\BasicFunctions;

class Conquer extends CustomModel
{
    protected $table = 'conquer';

    public function oldPlayer()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'old_owner', 'playerID', $table[0].'.player_latest');
    }

    public function newPlayer()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'new_owner', 'playerID', $table[0].'.player_latest');
    }

    public function village()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Village', 'village_id', 'villageID', $table[0].'.village_latest');
    }
    
    public static function playerConquerCounts($server, $world, $playerID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $conquer = collect();
        $conquer->put('old', $conquerModel->where('old_owner', $playerID)->count());
        $conquer->put('new', $conquerModel->where('new_owner', $playerID)->count());
        $conquer->put('total', $conquer->get('old')+$conquer->get('new'));

        return $conquer;
    }
    
    public static function allyConquerCounts($server, $world, $allyID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');
        
        $playerModel = new Player();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_latest');

        $allyPlayers = array();
        foreach ($playerModel->newQuery()->where('ally_id', $allyID)->get() as $player) {
            $allyPlayers[] = $player->playerID;
        }
        
        $conquer = collect();
        $conquer->put('old', $conquerModel->whereIn('old_owner', $allyPlayers)->count());
        $conquer->put('new', $conquerModel->whereIn('new_owner', $allyPlayers)->count());
        $conquer->put('total', $conquer->get('old')+$conquer->get('new'));

        return $conquer;
    }
    
    public static function villageConquerCounts($server, $world, $villageID){
        $conquerModel = new Conquer();
        $conquerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.conquer');

        $conquer = collect();
        $conquer->put('total', $conquerModel->where('village_id', $villageID)->count());

        return $conquer;
    }
}
