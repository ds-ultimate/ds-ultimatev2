<?php

namespace App;

use App\Util\BasicFunctions;

class AllyChanges extends CustomModel
{
    public function oldAlly()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'old_ally_id', 'allyID', $table[0].'.ally_latest');
    }

    public function newAlly()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'new_ally_id', 'allyID', $table[0].'.ally_latest');
    }

    public function player()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'player_id', 'playerID', $table[0].'.player_latest');
    }

    public static function playerAllyChangeCount($server, $world, $playerID){
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');
        
        return $allyChangesModel->where('player_id', $playerID)->count();
    }
    
    public static function allyAllyChangeCounts($server, $world, $allyID){
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');
        
        $allyChanges = collect();
        $allyChanges->put('old', $allyChangesModel->where('old_ally_id', $allyID)->count());
        $allyChanges->put('new', $allyChangesModel->where('new_ally_id', $allyID)->count());
        $allyChanges->put('total', $allyChanges->get('old')+$allyChanges->get('new'));

        return $allyChanges;
    }
}
