<?php

namespace App;

class AllySupport extends CustomModel
{
    
    protected $defaultTableName = "ally_support";

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
    public function ally()
    {
        return $this->mybelongsTo('App\Ally', 'ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
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
    public function allyTop()
    {
        return $this->mybelongsTo('App\AllyTop', 'ally_id', 'allyID', $this->getRelativeTable("ally_top"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playerTop()
    {
        return $this->mybelongsTo('App\PlayerTop', 'player_id', 'playerID', $this->getRelativeTable("player_top"));
    }
    
    public static function getForAlly(World $worldData, $ally_id) {
        //todo add players that got deleted while beeing with that tribe
        //todo change deff bash everywhere to defBash
        $model = new AllySupport($worldData);
        $dat = $model->where("ally_id", $ally_id)->get();
        foreach($dat as $it) {
            if($it->player != null && $it->player->ally_id == $ally_id) {
                //find last switch of that player
                $allyChangesModel = new AllyChanges($worldData);
                $lastChange = $allyChangesModel->where('player_id', $it->player_id)->orderBy('created_at', 'desc')->first();
                $it->supBash += $it->player->supBash - $lastChange->supBash;
                $it->offBash += $it->player->offBash - $lastChange->offBash;
                $it->deffBash += $it->player->defBash - $lastChange->deffBash;
            }
        }
        
        return $dat;
    }
    
    public static function getForPlayer(World $worldData, $player_id) {
        $model = new AllySupport($worldData);
        return $model->where("player_id", $player_id)->get();
    }
}
