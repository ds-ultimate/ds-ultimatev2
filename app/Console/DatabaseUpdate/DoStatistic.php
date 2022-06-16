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
    public static function run(World $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '600M');

        $village = new Village($world);
        $conqer = new Conquer($world);
        $allyChanges = new AllyChanges($world);

        $day = Carbon::now()->startOfDay();

        $statistic = WorldStatistic::todayWorldStatistic($world);

        if ($statistic){
            $statistic->total_player = $world->player_count;
            $statistic->total_ally = $world->ally_count;
            $statistic->total_villages = $world->village_count;
            $statistic->total_barbarian_village = $village->where('owner', 0)->count();
            $statistic->total_conquere = $conqer->count();
            $statistic->daily_conquer = $conqer->where('timestamp', '>', $day->getTimestamp())->count();
            $statistic->daily_ally_changes = $allyChanges->where('created_at', '>', $day)->count();

        }else{
            $statistic = new WorldStatistic();
            $statistic->world_id = $world->id;
            $statistic->total_player = $world->player_count;
            $statistic->total_ally = $world->ally_count;
            $statistic->total_villages = $world->village_count;
            $statistic->total_barbarian_village = $village->where('owner', 0)->count();
            $statistic->total_conquere = $conqer->count();
            $statistic->daily_conquer = $conqer->where('timestamp', '>', $day->getTimestamp())->count();
            $statistic->daily_ally_changes = $allyChanges->where('created_at', '>', $day)->count();
        }
        $statistic->save();
    }
}
