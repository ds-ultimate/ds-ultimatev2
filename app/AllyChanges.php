<?php

namespace App;

use App\Util\BasicFunctions;

class AllyChanges extends CustomModel
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldAlly()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'old_ally_id', 'allyID', $table[0].'.ally_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newAlly()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'new_ally_id', 'allyID', $table[0].'.ally_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Player', 'player_id', 'playerID', $table[0].'.player_latest');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldAllyTop()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\AllyTop', 'old_ally_id', 'allyID', $table[0].'.ally_top');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newAllyTop()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\AllyTop', 'new_ally_id', 'allyID', $table[0].'.ally_top');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerTop()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\PlayerTop', 'player_id', 'playerID', $table[0].'.player_top');
    }

    /**
     * @param string $server
     * @param $world
     * @param int $playerID
     * @return int
     */
    public static function playerAllyChangeCount($server, $world, $playerID){
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');
        
        $allyChanges = [];
        $allyChanges['total'] = $allyChangesModel->where('player_id', $playerID)->count();
        return $allyChanges;
    }

    /**
     * @param string $server
     * @param $world
     * @param int $allyID
     * @return \Illuminate\Support\Collection
     */
    public static function allyAllyChangeCounts($server, $world, $allyID){
        $allyChangesModel = new AllyChanges();
        $allyChangesModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_changes');
        
        $allyChanges = [];
        $allyChanges['old'] = $allyChangesModel->where('old_ally_id', $allyID)->count();
        $allyChanges['new'] = $allyChangesModel->where('new_ally_id', $allyID)->count();
        $allyChanges['total'] = $allyChanges['old'] + $allyChanges['new'];
        return $allyChanges;
    }
}
