<?php

namespace App;

class AllyChanges extends CustomModel
{

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = "ally_changes";
        }
        parent::__construct($arg1, $arg2);
    }
    
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
     * @param World $world
     * @param int $playerID
     * @return int
     */
    public static function playerAllyChangeCount(World $world, $playerID){
        $allyChangesModel = new AllyChanges($world);
        
        $allyChanges = [];
        $allyChanges['total'] = $allyChangesModel->where('player_id', $playerID)->count();
        return $allyChanges;
    }

    /**
     * @param World $world
     * @param int $allyID
     * @return \Illuminate\Support\Collection
     */
    public static function allyAllyChangeCounts(World $world, $allyID){
        $allyChangesModel = new AllyChanges($world);
        
        $allyChanges = [];
        $allyChanges['old'] = $allyChangesModel->where('old_ally_id', $allyID)->count();
        $allyChanges['new'] = $allyChangesModel->where('new_ally_id', $allyID)->count();
        $allyChanges['total'] = $allyChanges['old'] + $allyChanges['new'];
        return $allyChanges;
    }
}
