<?php

namespace App\Console\DatabaseUpdate;

use App\Conquer;
use App\Log;
use App\Notifications\DiscordNotification;
use App\Player;
use App\Util\BasicFunctions;
use App\World;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;

class DoConquer
{
    public static function run($server, $world){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '1500M');

        $dbName = BasicFunctions::getDatabaseName($server, $world);
        $worldUpdate = World::getWorld($server, $world);

        if (BasicFunctions::existTable($dbName, 'conquer') === false) {
            TableGenerator::conquerTable($dbName);
        }
        $conquer = new Conquer();
        $conquer->setTable($dbName.'.conquer');
        $first = $conquer->orderBy('timestamp', 'DESC')->first();
        if($first == null)
            $latest = 0;
        else
            $latest = $first->timestamp;

        if(time() - $latest > 60 * 60 * 23) {
            $lines = gzfile("$worldUpdate->url/map/conquer.txt.gz");
            if (!is_array($lines)) {
                BasicFunctions::createLog("ERROR_update[$server$world]", "conquer.txt.gz konnte nicht ge&ouml;ffnet werden");
                $input =[
                    'world' => $worldUpdate,
                    'file' => 'conquer.txt',
                    'url' => $worldUpdate->url.'/map/conquer.txt'
                ];
                Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
                return;
            }
        } else {
            $lines = gzfile("$worldUpdate->url/interface.php?func=get_conquer&since=" . ($latest - 1));
            if (!is_array($lines)) {
                BasicFunctions::createLog("ERROR_update[$server$world]", "interface.php?func=get_conquer konnte nicht ge&ouml;ffnet werden");
                $input =[
                    'world' => $worldUpdate,
                    'file' => 'conquer interface',
                    'url' => $worldUpdate->url.'/interface.php?func=get_conquer&since=' . ($latest - 1)
                ];
                Notification::send(new Log(), new DiscordNotification('worldUpdate', null, $input));
                return;
            }
        }

        $array = array();
        $databaseConquer = static::prepareConquerDupCheck($dbName);
        $insertTime = Carbon::now();

        foreach ($lines as $line) {
            $exploded = explode(',', trim($line));
            if(static::conquerInsideDB($databaseConquer, $exploded)) continue;

            $tempArr = array();
            list($tempArr['village_id'], $tempArr['timestamp'], $tempArr['new_owner'], $tempArr['old_owner']) = $exploded;
            $tempArr['created_at'] = $insertTime;
            $tempArr['updated_at'] = $insertTime;

//            $follow = \App\Follow::whereIn('followable_id', [$tempArr['new_owner'],$tempArr['old_owner']])->where('worlds_id', $worldUpdate->id)->get();
//
//            if ($follow->count() > 0){
//                $input = [
//                    'world' => $worldUpdate,
//                    'conquere' => [$tempArr['village_id'],$tempArr['timestamp'],$tempArr['new_owner'],$tempArr['old_owner']]
//                ];
//
//                Follow::conquereNotification($follow, $input);
//            }

            $old = Player::player($server, $world, $tempArr['old_owner']);
            if($tempArr['old_owner'] == 0) {
                $tempArr['old_owner_name'] = "";
                $tempArr['old_ally'] = 0;
                $tempArr['old_ally_name'] = "";
                $tempArr['old_ally_tag'] = "";
            } else if($old == null) {
                $tempArr['old_owner_name'] = null;
                $tempArr['old_ally'] = 0;
                $tempArr['old_ally_name'] = null;
                $tempArr['old_ally_tag'] = null;
            } else {
                $tempArr['old_owner_name'] = $old->name;
                $tempArr['old_ally'] = $old->ally_id;
                $tempArr['old_ally_name'] = ($old->allyLatest != null)?$old->allyLatest->name:"";
                $tempArr['old_ally_tag'] = ($old->allyLatest != null)?$old->allyLatest->tag:"";
            }

            $new = Player::player($server, $world, $tempArr['new_owner']);
            if($tempArr['new_owner'] == 0) {
                $tempArr['new_owner_name'] = "";
                $tempArr['new_ally'] = 0;
                $tempArr['new_ally_name'] = "";
                $tempArr['new_ally_tag'] = "";
            } else if($new == null) {
                $tempArr['new_owner_name'] = null;
                $tempArr['new_ally'] = 0;
                $tempArr['new_ally_name'] = null;
                $tempArr['new_ally_tag'] = null;
            } else {
                $tempArr['new_owner_name'] = $new->name;
                $tempArr['new_ally'] = $new->ally_id;
                $tempArr['new_ally_name'] = ($new->allyLatest != null)?$new->allyLatest->name:"";
                $tempArr['new_ally_tag'] = ($new->allyLatest != null)?$new->allyLatest->tag:"";
            }

            $array[] = $tempArr;
        }

        $insert = new Conquer();
        $insert->setTable($dbName . '.conquer');

        foreach (array_chunk($array, 3000) as $t) {
            $insert->insert($t);
        }
    }
    private static function prepareConquerDupCheck($dbName) {
        $conquerModel = new Conquer();
        $conquerModel->setTable($dbName . '.conquer');

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
