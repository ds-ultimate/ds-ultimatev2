<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\Player;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use Carbon\Carbon;

class DoClean
{
    public static function run(World $world, $type){
        $days = config('dsUltimate.db_save_day');
        if($world->isSpeed()) {
            $days = config('dsUltimate.db_save_day_speed');
        }
        
        switch($type) {
            case 'a':
                $hashSize = $world->hash_ally;
                $tablePrefix = 'ally';
                $model = new Ally();
                break;

            case 'p':
                $hashSize = $world->hash_player;
                $tablePrefix = 'player';
                $model = new Player();
                break;

            case 'v':
                $hashSize = $world->hash_village;
                $tablePrefix = 'village';
                $model = new Village();
                break;
        }

        for ($i = 0; $i < $hashSize; $i++){
            if (BasicFunctions::hasWorldDataTable($world, "{$tablePrefix}_{$i}") === true) {
                $model->setTable(BasicFunctions::getWorldDataTable($world, "{$tablePrefix}_{$i}"));
                $delete = $model->where('updated_at', '<', Carbon::now()->subDays($days));
                $delete->delete();
            }
        }
        $world->worldCleaned_at = Carbon::now();
        $world->save();
    }
}
