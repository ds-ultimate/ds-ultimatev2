<?php

namespace App;

use App\Util\BasicFunctions;

class PlayerTop extends CustomModel
{
    protected $primaryKey = 'playerID';
    protected $fillable = [
        'playerID',
        'name',
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
        'supBash_top',
        'supBash_date',
        'supBashRank_top',
        'supBashRank_date',
        'gesBash_top',
        'gesBash_date',
        'gesBashRank_top',
        'gesBashRank_date',
    ];

    protected $casts = [
        'village_count_date' => 'datetime',
        'points_date' => 'datetime',
        'rank_date' => 'datetime',
        'offBash_date' => 'datetime',
        'offBashRank_date' => 'datetime',
        'defBash_date' => 'datetime',
        'defBashRank_date' => 'datetime',
        'supBash_date' => 'datetime',
        'supBashRank_date' => 'datetime',
        'gesBash_date' => 'datetime',
        'gesBashRank_date' => 'datetime',
        'updated_at' => 'datetime',
        'created_at' => 'datetime',
    ];
    
    protected $defaultTableName = "player_top";

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
    public function allyLatest()
    {
        return $this->mybelongsTo('App\AllyTop', 'ally_id', 'allyID', $this->getRelativeTable("ally_latest"));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allyChanges()
    {
        return $this->myhasMany('App\AllyChanges', 'player_id', 'playerID', $this->getRelativeTable("ally_changes"));
    }

    /**
     * @param World $world
     * @param int $player
     * @return $this
     */
    public static function player(World $world, $player){
        $playerModel = new PlayerTop($world);
        return $playerModel->find($player);
    }

    public function linkIngame(World $world, $guest=false) {
        $guestPart = "game";
        if($guest) {
            $guestPart = "guest";
        }

        return "{$world->url}/$guestPart.php?screen=info_player&id={$this->playerID}";
    }
    
    public function getDate($variable) {
        $variable .= "_date";
        if(!in_array($variable, $this->fillable)) return "";
        
        $data = $this->$variable->format('d.m.Y');
        return " (" . __("ui.topAt") . ' ' . $data . ")";
    }

    public function signature() {
        return $this->morphMany('App\Signature', 'element');
    }

    public function getSignature(World $worldData) {
        $sig = $this->morphOne('App\Signature', 'element')->where('world_id', $worldData->id)->first();
        if($sig != null) {
            return $sig;
        }

        $sig = new Signature();
        $sig->world_id = $worldData->id;
        $this->signature()->save($sig);
        return $sig;
    }
}
