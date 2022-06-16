<?php

namespace App\Console\DatabaseUpdate;

use App\Ally;
use App\Conquer;
use App\Follow;
use App\Player;
use App\World;
use App\Util\BasicFunctions;
use Carbon\Carbon;

class DoConquer
{
    public static function run(World $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1500M');
        $minTime = Carbon::now()->subHour()->subMinutes(5);

        if (BasicFunctions::hasWorldDataTable($world, 'conquer') === false) {
            TableGenerator::conquerTable($world);
        }
        $conquer = new Conquer($world);
        $first = $conquer->orderBy('timestamp', 'DESC')->first();
        if($first == null)
            $latest = 0;
        else
            $latest = $first->timestamp;

        if(time() - $latest > 60 * 60 * 23) {
            $lines = DoWorldData::loadGzippedFile($world, "conquer_extended.txt.gz", $minTime);
            if($lines === false) return false;
        } else {
            $lines = DoWorldData::loadUncompressedFile($world, "/interface.php?func=get_conquer_extended&since=" . ($latest - 1), "interface.php?func=get_conquer_extended");
            if($lines === false) return false;
        }

        $array = array();
        $databaseConquer = static::prepareConquerDupCheck($world);
        $insertTime = Carbon::now();

        foreach ($lines as $line) {
            $line = trim($line);
            if($line == "") continue;
            $exploded = explode(',', $line);
            if(static::conquerInsideDB($databaseConquer, $exploded)) continue;

            $tempArr = array();
            list($tempArr['village_id'], $tempArr['timestamp'], $tempArr['new_owner'], $tempArr['old_owner'],
                    $tempArr['old_ally'], $tempArr['new_ally'], $tempArr['points']) = $exploded;
            
            $tempArr['created_at'] = $insertTime;
            $tempArr['updated_at'] = $insertTime;

            $old = Player::player($world, $tempArr['old_owner']);
            $oldAlly = Ally::ally($world, $tempArr['old_ally']);
            if($tempArr['old_owner'] == 0) {
                $tempArr['old_owner_name'] = "";
            } else if($old == null) {
                $tempArr['old_owner_name'] = null;
            } else {
                $tempArr['old_owner_name'] = $old->name;
            }
            if($tempArr['old_ally'] == 0) {
                $tempArr['old_ally_name'] = "";
                $tempArr['old_ally_tag'] = "";
            } else if($oldAlly == null) {
                $tempArr['old_ally_name'] = null;
                $tempArr['old_ally_tag'] = null;
            } else {
                $tempArr['old_ally_name'] = $oldAlly->name;
                $tempArr['old_ally_tag'] = $oldAlly->tag;
            }

            $new = Player::player($world, $tempArr['new_owner']);
            $newAlly = Ally::ally($world, $tempArr['new_ally']);
            if($tempArr['new_owner'] == 0) {
                $tempArr['new_owner_name'] = "";
            } else if($new == null) {
                $tempArr['new_owner_name'] = null;
            } else {
                $tempArr['new_owner_name'] = $new->name;
            }
            if($tempArr['new_ally'] == 0) {
                $tempArr['new_ally_name'] = "";
                $tempArr['new_ally_tag'] = "";
            } else if($newAlly == null) {
                $tempArr['new_ally_name'] = null;
                $tempArr['new_ally_tag'] = null;
            } else {
                $tempArr['new_ally_name'] = $newAlly->name;
                $tempArr['new_ally_tag'] = $newAlly->tag;
            }

            $array[] = $tempArr;

            $follows = Follow::where('world_id', $world->id)->where(
                fn($query) => $query->where(
                    fn($q2) => $q2->whereIn('followable_id', [$tempArr['new_owner'], $tempArr['old_owner']])
                        ->where('followable_type', 'App\\Player')
                )->orWhere(
                    fn($q2) => $q2->whereIn('followable_id', [$tempArr['new_ally'], $tempArr['old_ally']])
                        ->where('followable_type', 'App\\Ally')
                ))->get();
            if ($follows->count() > 0){
                Follow::conquereNotification($follows, $world, $tempArr);
            }
        }

        $insert = new Conquer($world);
        foreach (array_chunk($array, 3000) as $t) {
            $insert->insert($t);
        }
    }
    private static function prepareConquerDupCheck(World $world) {
        $conquerModel = new Conquer($world);

        $arrCon = array();
        foreach ($conquerModel->get() as $conquer) {
            if(!isset($arrCon[$conquer->timestamp]))
                $arrCon[$conquer->timestamp] = array();

            $arrCon[$conquer->timestamp][] = array($conquer->village_id, $conquer->old_owner, $conquer->new_owner);
        }
        return $arrCon;
    }

    private static function conquerInsideDB($arrCon, $data) {
        if(!isset($arrCon[$data[1]])) return false;
        //echo "Found in DB\n";
        $possible_dups = $arrCon[$data[1]];

        foreach($possible_dups as $possible_dup) {
            if($possible_dup[0] == $data[0] &&
                    $possible_dup[1] == $data[3] &&
                    $possible_dup[2] == $data[2]) {
                return true;
            }
        }

        return false;
    }
}
