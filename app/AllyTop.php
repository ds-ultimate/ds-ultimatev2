<?php

namespace App;

class AllyTop extends CustomModel
{
    protected $primaryKey = 'allyID';
    protected $fillable = [
        'allyID',
        'name',
        'tag',
        'member_count_top',
        'member_count_date',
        'village_count_top',
        'village_count_date',
        'points_top',
        'points_date',
        'rank_top',
        'rank_date',
        'offBash_top',
        'offBash_date',
        'offBashRank_top',
        'offBashRank_date',
        'defBash_top',
        'defBash_date',
        'defBashRank_top',
        'defBashRank_date',
        'gesBash_top',
        'gesBash_date',
        'gesBashRank_top',
        'gesBashRank_date',
    ];

    protected $dates = [
        'member_count_date',
        'village_count_date',
        'points_date',
        'rank_date',
        'offBash_date',
        'offBashRank_date',
        'defBash_date',
        'defBashRank_date',
        'gesBash_date',
        'gesBashRank_date',
        'updated_at',
        'created_at',
    ];
    
    protected $defaultTableName = "ally_top";

    public function __construct($arg1 = [], $arg2 = null)
    {
        if($arg1 instanceof World && $arg2 == null) {
            //allow calls without table name
            $arg2 = $this->defaultTableName;
        }
        parent::__construct($arg1, $arg2);
    }

    /**
     * @return Ally
     */
    public function allyLatest()
    {
        return $this->myhasMany('App\Player', 'ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
    }

    /**
     * @return AllyChanges
     */
    public function allyChangesOld()
    {
        return $this->myhasMany('App\AllyChanges', 'old_ally_id', 'allyID', $this->getRelativeTable("ally_changes"));
    }

    /**
     * @return AllyChanges
     */
    public function allyChangesNew()
    {
        return $this->myhasMany('App\AllyChanges', 'new_ally_id', 'allyID', $this->getRelativeTable("ally_changes"));
    }

    /**
     * @param World $world
     * @param  int $ally
     * @return $this
     */
    public static function ally(World $world, $ally){
        $allyModel = new AllyTop($world);
        return $allyModel->find($ally);
    }

    public function linkIngame(World $world, $guest=false) {
        $guestPart = "game";
        if($guest) {
            $guestPart = "guest";
        }

        return "{$world->url}/$guestPart.php?screen=info_ally&id={$this->playerID}";
    }
    
    public function getDate($variable) {
        $variable .= "_date";
        if(!in_array($variable, $this->fillable)) return "";
        
        $data = $this->$variable->format('d.m.Y');
        return " (" . __("ui.topAt") . ' ' . $data . ")";
    }
}
