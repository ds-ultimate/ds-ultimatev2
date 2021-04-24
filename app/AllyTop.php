<?php

namespace App;

use App\Util\BasicFunctions;

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

    /**
     * @return Player
     */
    public function playerLatest()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\Player', 'ally_id', 'allyID', $table[0].'.ally_changes');
    }

    /**
     * @return AllyChanges
     */
    public function allyChangesOld()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'old_ally_id', 'allyID', $table[0].'.ally_changes');
    }

    /**
     * @return AllyChanges
     */
    public function allyChangesNew()
    {
        $table = explode('.', $this->table);
        return $this->myhasMany('App\AllyChanges', 'new_ally_id', 'allyID', $table[0].'.ally_changes');
    }

    /**
     * @param string $server
     * @param $world
     * @param  int $ally
     * @return $this
     */
    public static function ally($server, $world, $ally){
        $allyModel = new AllyTop();
        $allyModel->setTable(BasicFunctions::getDatabaseName($server, $world).'.ally_top');

        return $allyModel->find($ally);

    }
}
