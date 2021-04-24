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

    protected $dates = [
        'village_count_date',
        'points_date',
        'rank_date',
        'offBash_date',
        'offBashRank_date',
        'defBash_date',
        'defBashRank_date',
        'supBash_date',
        'supBashRank_date',
        'gesBash_date',
        'gesBashRank_date',
        'updated_at',
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function allyLatest()
    {
        $table = explode('.', $this->table);
        return $this->mybelongsTo('App\Ally', 'ally_id', 'allyID', $table[0].'.ally_latest');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allyChanges()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'player_id', 'playerID', $table[0].'.ally_changes');
    }

    /**
     * @param string $server
     * @param $world
     * @param int $player
     * @return $this
     */
    public static function player($server, $world, $player){
        $playerModel = new PlayerTop();
        $playerModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.player_top');

        return $playerModel->find($player);
    }
}
