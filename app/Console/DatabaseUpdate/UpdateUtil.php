<?php

namespace App\Console\DatabaseUpdate;

use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;

class UpdateUtil
{
    public static function updateNeeded() {
        if(!BasicFunctions::existTable(null, 'worlds')) return false;
        $worldModel = new World();
        return $worldModel->where('worldUpdated_at', '<', Carbon::now()->subHours(config('dsUltimate.db_update_every_hours')))
                ->where('active', '=', 1)->count() > 0;
    }

    public static function cleanNeeded() {
        if(!BasicFunctions::existTable(null, 'worlds')) return false;
        $worldModel = new World();
        return $worldModel->where('worldCleaned_at', '<', Carbon::now()->subHours(config('dsUltimate.db_clean_every_hours')))
                ->where('active', '=', 1)->count() > 0;
    }

    public static function hashTable($mainArray, $type, $index,callable $cmpFkt = null, $cmpArr = null){
        $hashArray = array();
        foreach ($mainArray as $main){
            if($cmpFkt != null && $cmpFkt($cmpArr, $main)) {
                //remove "unwanted" entries
                continue;
            }
            $id = $main[$index];
            if (! array_key_exists(BasicFunctions::hash($id, $type), $hashArray)) {
                $hashArray[BasicFunctions::hash($id, $type)] = array();
            }
            $hashArray[BasicFunctions::hash($id, $type)][] = $main;
        }

        return $hashArray;
    }
}
