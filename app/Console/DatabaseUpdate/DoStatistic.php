<?php

namespace App\Console\DatabaseUpdate;

use App\AllyChanges;
use App\Conquer;
use App\Util\BasicFunctions;
use App\Village;
use App\World;
use App\WorldStatistic;
use Carbon\Carbon;

class DoStatistic
{
    public static function run($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '600M');
        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        $village = new Village();
        $village->setTable($dbName.'.village_latest');
        $conqer = new Conquer();
        $conqer->setTable($dbName.'.conquer');
        $allyChanges = new AllyChanges();
        $allyChanges->setTable($dbName.'.ally_changes');

        $day = Carbon::now()->startOfDay();

        $statistic = WorldStatistic::todayWorldStatistic($worldUpdate);

        if ($statistic){
            $statistic->total_player = $worldUpdate->player_count;
            $statistic->total_ally = $worldUpdate->ally_count;
            $statistic->total_villages = $worldUpdate->village_count;
            $statistic->total_barbarian_village = $village->where('owner', 0)->count();
            $statistic->total_conquere = $conqer->count();
            $statistic->daily_conquer = $conqer->where('timestamp', '>', $day->getTimestamp())->count();
            $statistic->daily_ally_changes = $allyChanges->where('created_at', '>', $day)->count();

        }else{
            $statistic = new WorldStatistic();
            $statistic->world_id = $worldUpdate->id;
            $statistic->total_player = $worldUpdate->player_count;
            $statistic->total_ally = $worldUpdate->ally_count;
            $statistic->total_villages = $worldUpdate->village_count;
            $statistic->total_barbarian_village = $village->where('owner', 0)->count();
            $statistic->total_conquere = $conqer->count();
            $statistic->daily_conquer = $conqer->where('timestamp', '>', $day->getTimestamp())->count();
            $statistic->daily_ally_changes = $allyChanges->where('created_at', '>', $day)->count();
        }
        $statistic->save();
    }
}
