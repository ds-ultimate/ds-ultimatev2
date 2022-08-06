<?php

namespace App;

class AllyChanges extends CustomModel
{
    
    protected $defaultTableName = "ally_changes";

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = $this->defaultTableName;
        }
        parent::__construct($arg1, $arg2);
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldAlly()
    {
        return $this->mybelongsTo('App\Ally', 'old_ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newAlly()
    {
        return $this->mybelongsTo('App\Ally', 'new_ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function player()
    {
        return $this->mybelongsTo('App\Player', 'player_id', 'playerID', $this->getRelativeTable("player_latest"));
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function oldAllyTop()
    {
        return $this->mybelongsTo('App\AllyTop', 'old_ally_id', 'allyID', $this->getRelativeTable("ally_top"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function newAllyTop()
    {
        return $this->mybelongsTo('App\AllyTop', 'new_ally_id', 'allyID', $this->getRelativeTable("ally_top"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerTop()
    {
        return $this->mybelongsTo('App\PlayerTop', 'player_id', 'playerID', $this->getRelativeTable("player_top"));
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
